<?php

namespace App\Services\Import;

use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\Table;

class FormImportParser
{
    public function parse(string $path, string $extension): array
    {
        return match (strtolower($extension)) {
            'docx' => $this->parseWord($path),
            'xlsx' => $this->parseExcel($path),
            default => throw new \InvalidArgumentException(
                'Unsupported file type.'
            ),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | WORD
    |--------------------------------------------------------------------------
    */

    protected function parseWord(string $path): array
    {
        $phpWord = WordIOFactory::load($path);

        $lines = [];

        foreach ($phpWord->getSections() as $wordSection) {
            foreach ($wordSection->getElements() as $element) {

                $text = $this->extractWordText($element);

                if ($text === null || trim($text) === '') {
                    continue;
                }

                foreach (preg_split('/\r\n|\r|\n/', $text) as $line) {

                    $line = trim($line);

                    if ($line === '') {
                        continue;
                    }

                    $lines[] = [
                        'text' => $line,
                        'heading' => $this->isHeading($element),
                    ];
                }
            }
        }

        if (empty($lines)) {
            return $this->emptySchema();
        }

        /*
    |--------------------------------------------------------------------------
    | TITLE
    |--------------------------------------------------------------------------
    */

        $title = 'Imported Form';

        $titleIndex = null;

        foreach ($lines as $index => $line) {

            $text = trim($line['text']);

            if ($text === '') {
                continue;
            }

            /*
         * First non-field line is treated as title.
         */
            if (!$this->looksLikeField($text)) {
                $title = $text;
                $titleIndex = $index;
                break;
            }
        }

        /*
    |--------------------------------------------------------------------------
    | DESCRIPTION
    |--------------------------------------------------------------------------
    */

        $description = null;
        $startIndex = ($titleIndex ?? -1) + 1;

        for ($i = $startIndex; $i < count($lines); $i++) {

            $text = trim($lines[$i]['text']);

            if ($text === '') {
                continue;
            }

            /*
         * Don't consume section heading as description.
         */
            if (
                $this->looksLikeSection(
                    $text,
                    $lines[$i]['heading']
                )
            ) {
                break;
            }

            /*
         * Don't consume field as description.
         */
            if ($this->looksLikeField($text)) {
                break;
            }

            /*
         * Usually:
         * Please complete the following information.
         */
            $description = $text;
            $startIndex = $i + 1;

            break;
        }

        /*
    |--------------------------------------------------------------------------
    | SECTIONS
    |--------------------------------------------------------------------------
    */

        $sections = [];

        $currentSection = null;

        for ($i = $startIndex; $i < count($lines); $i++) {

            $text = trim($lines[$i]['text']);

            if ($text === '') {
                continue;
            }

            /*
         * SECTION DETECTION
         *
         * This is the important part.
         */
            if (
                $this->looksLikeSection(
                    $text,
                    $lines[$i]['heading']
                )
            ) {

                /*
             * Save previous section.
             */
                if (
                    $currentSection !== null &&
                    !empty($currentSection['fields'])
                ) {
                    $sections[] = $currentSection;
                }

                /*
             * Start new section.
             */
                $currentSection = [
                    'id' => $this->uuid(),
                    'title' => $this->cleanHeading($text),
                    'fields' => [],
                ];

                continue;
            }

            /*
         * Detect field.
         */
            $field = $this->detectWordField($text);

            if ($field === null) {
                continue;
            }

            /*
         * If no section has been detected yet,
         * create General.
         */
            if ($currentSection === null) {

                $currentSection = [
                    'id' => $this->uuid(),
                    'title' => 'General',
                    'fields' => [],
                ];
            }

            $currentSection['fields'][] = $field;
        }

        /*
     * Save last section.
     */
        if (
            $currentSection !== null &&
            !empty($currentSection['fields'])
        ) {
            $sections[] = $currentSection;
        }

        /*
    |--------------------------------------------------------------------------
    | FALLBACK
    |--------------------------------------------------------------------------
    */

        if (empty($sections)) {

            $sections[] = [
                'id' => $this->uuid(),
                'title' => 'General',
                'fields' => [],
            ];
        }

        return [
            'version' => '1.0',
            'title' => $title,
            'description' => $description,
            'settings' => [
                'submit_button' => 'Submit',
            ],
            'sections' => $sections,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | WORD FIELD DETECTION
    |--------------------------------------------------------------------------
    */

    protected function detectWordField(string $text): ?array
    {
        $text = trim($text);

        if ($text === '') {
            return null;
        }

        /*
         * Checkbox symbols.
         *
         * Example:
         * ☐ I agree to the terms
         */
        if (
            str_contains($text, '☐') ||
            str_contains($text, '□') ||
            str_contains($text, '☑')
        ) {
            $clean = preg_replace('/[☐□☑✓✔]/u', '', $text);
            $clean = trim($clean);

            $meta = $this->extractMetadata($clean);

            $label = $meta['label'];

            return $this->makeField(
                label: $label,
                type: 'checkbox',
                required: $meta['required']
            );
        }

        /*
         * Remove question numbering.
         *
         * 1. Full Name
         * 2) Email
         * Q3. Phone
         */
        $text = preg_replace(
            '/^(q(?:uestion)?\s*)?\d+[\.\):\-]\s*/i',
            '',
            $text
        );

        $text = trim($text);

        if ($text === '') {
            return null;
        }

        /*
         * Detect metadata:
         *
         * Gender [radio] [required]
         * Country [dropdown]
         * Resume [file]
         */
        $meta = $this->extractMetadata($text);

        $label = $meta['label'];
        $type = $meta['type'];
        $required = $meta['required'];

        /*
         * Explicit options.
         *
         * Gender: Male / Female / Other
         *
         * Country [dropdown]: India / USA / UK
         */
        $options = [];

        if (str_contains($label, ':')) {

            [$possibleLabel, $possibleOptions] = array_pad(
                explode(':', $label, 2),
                2,
                null
            );

            if (
                $possibleOptions !== null &&
                $this->looksLikeOptions($possibleOptions)
            ) {
                $label = trim($possibleLabel);

                $options = $this->parseOptions($possibleOptions);

                /*
                 * If type wasn't explicitly supplied,
                 * multiple options mean radio by default.
                 */
                if ($type === null) {
                    $type = 'radio';
                }
            }
        }

        /*
         * Guess type if not explicitly defined.
         */
        if ($type === null) {
            $type = $this->guessFieldType($label);
        }

        /*
         * Checkbox field with explicit options.
         */
        if (
            $type === 'checkbox' &&
            !empty($options)
        ) {
            return $this->makeField(
                label: $label,
                type: $type,
                options: $options,
                required: $required
            );
        }

        return $this->makeField(
            label: $label,
            type: $type,
            options: $options,
            required: $required
        );
    }

    /*
    |--------------------------------------------------------------------------
    | WORD METADATA
    |--------------------------------------------------------------------------
    */

    protected function extractMetadata(string $text): array
    {
        $type = null;
        $required = false;

        /*
         * Supported:
         * [text]
         * [email]
         * [number]
         * [phone]
         * [date]
         * [textarea]
         * [radio]
         * [checkbox]
         * [dropdown]
         * [select]
         * [file]
         * [rating]
         */
        if (preg_match_all('/\[([a-zA-Z0-9_-]+)\]/', $text, $matches)) {

            foreach ($matches[1] as $meta) {

                $meta = strtolower(trim($meta));

                if ($meta === 'required') {
                    $required = true;
                    continue;
                }

                $allowedTypes = [
                    'text',
                    'email',
                    'number',
                    'phone',
                    'date',
                    'textarea',
                    'radio',
                    'checkbox',
                    'dropdown',
                    'select',
                    'file',
                    'rating',
                ];

                if (in_array($meta, $allowedTypes, true)) {
                    $type = $meta;

                    if ($type === 'select') {
                        $type = 'dropdown';
                    }
                }
            }

            /*
             * Remove all metadata from label.
             */
            $text = preg_replace(
                '/\[[a-zA-Z0-9_-]+\]/',
                '',
                $text
            );

            $text = trim($text);
        }

        return [
            'label' => $text,
            'type' => $type,
            'required' => $required,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONS
    |--------------------------------------------------------------------------
    */

    protected function looksLikeOptions(string $text): bool
    {
        return str_contains($text, '/')
            || str_contains($text, '|')
            || preg_match('/,\s*/', $text);
    }

    protected function parseOptions(string $text): array
    {
        $text = trim($text);

        /*
         * Supported:
         *
         * Male / Female / Other
         * Male | Female | Other
         * Male, Female, Other
         *
         * Male:male / Female:female
         */
        $parts = preg_split(
            '/\s*(?:\/|\||,)\s*/',
            $text
        );

        $options = [];

        foreach ($parts as $part) {

            $part = trim($part);

            if ($part === '') {
                continue;
            }

            $label = $part;
            $value = null;

            /*
             * Explicit value:
             *
             * Male:male
             * Female:female
             */
            if (str_contains($part, ':')) {

                [$label, $value] = array_pad(
                    explode(':', $part, 2),
                    2,
                    null
                );

                $label = trim($label);
                $value = trim((string) $value);
            }

            if ($value === null || $value === '') {
                $value = $this->makeKey($label);
            }

            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $options;
    }

    /*
    |--------------------------------------------------------------------------
    | WORD SECTION DETECTION
    |--------------------------------------------------------------------------
    */

    protected function looksLikeSection(
        string $text,
        bool $styledHeading = false
    ): bool {
        $text = trim($text);

        if ($text === '') {
            return false;
        }

        /*
    |--------------------------------------------------------------------------
    | Explicit Word heading style
    |--------------------------------------------------------------------------
    */

        if ($styledHeading) {
            return true;
        }

        /*
    |--------------------------------------------------------------------------
    | Explicit section syntax
    |--------------------------------------------------------------------------
    |
    | [section] Personal Information
    | Section: Personal Information
    |
    */

        if (preg_match(
            '/^\[section\]\s*(.+)$/i',
            $text
        )) {
            return true;
        }

        if (preg_match(
            '/^section\s*:\s*(.+)$/i',
            $text
        )) {
            return true;
        }

        /*
    |--------------------------------------------------------------------------
    | Never treat field lines as sections.
    |--------------------------------------------------------------------------
    */

        if ($this->looksLikeField($text)) {
            return false;
        }

        /*
    |--------------------------------------------------------------------------
    | Remove numbering if someone writes:
    |
    | 1. Personal Information
    |--------------------------------------------------------------------------
    */

        $clean = preg_replace(
            '/^\d+[\.\):\-]\s*/',
            '',
            $text
        );

        $clean = strtolower(trim($clean));

        /*
    |--------------------------------------------------------------------------
    | Known/common section names
    |--------------------------------------------------------------------------
    */

        $knownSections = [
            'personal information',
            'personal details',

            'contact information',
            'contact details',

            'education',
            'educational information',
            'educational details',

            'experience',
            'work experience',
            'professional experience',
            'employment',

            'skills',
            'technical skills',

            'additional information',
            'additional details',

            'documents',
            'document information',

            'references',
            'declaration',

            'professional information',
            'career information',
            'job information',
        ];

        if (in_array($clean, $knownSections, true)) {
            return true;
        }

        /*
    |--------------------------------------------------------------------------
    | Generic section-name detection
    |--------------------------------------------------------------------------
    |
    | This handles things like:
    |
    | Qualifications
    | Certifications
    | Projects
    | Family Information
    |
    */

        $sectionWords = [
            'information',
            'details',
            'education',
            'experience',
            'skills',
            'qualification',
            'qualifications',
            'certification',
            'certifications',
            'projects',
            'references',
            'declaration',
            'employment',
            'profile',
        ];

        foreach ($sectionWords as $word) {

            if (
                $clean === $word ||
                str_ends_with($clean, ' ' . $word)
            ) {
                return true;
            }
        }

        return false;
    }

    protected function cleanHeading(string $text): string
    {
        $text = trim($text);

        $text = preg_replace(
            '/^\[section\]\s*/i',
            '',
            $text
        );

        $text = preg_replace(
            '/^section\s*:\s*/i',
            '',
            $text
        );

        $text = preg_replace(
            '/^\d+[\.\):\-]\s*/',
            '',
            $text
        );

        return trim($text);
    }

    /*
    |--------------------------------------------------------------------------
    | WORD TEXT EXTRACTION
    |--------------------------------------------------------------------------
    */

    protected function extractWordText($element): ?string
    {
        /*
         * Normal text.
         */
        if ($element instanceof Text) {
            return trim((string) $element->getText());
        }

        /*
         * TextRun.
         */
        if ($element instanceof TextRun) {

            $text = '';

            foreach ($element->getElements() as $child) {

                $childText = $this->extractWordText($child);

                if ($childText !== null) {
                    $text .= ' ' . $childText;
                }
            }

            return trim($text) ?: null;
        }

        /*
         * List item.
         */
        if ($element instanceof ListItem) {

            $text = '';

            foreach ($element->getElements() as $child) {

                $childText = $this->extractWordText($child);

                if ($childText !== null) {
                    $text .= ' ' . $childText;
                }
            }

            return trim($text) ?: null;
        }

        /*
         * Table.
         */
        if ($element instanceof Table) {

            $lines = [];

            foreach ($element->getRows() as $row) {

                $rowText = [];

                foreach ($row->getCells() as $cell) {

                    $cellText = [];

                    foreach ($cell->getElements() as $child) {

                        $childText = $this->extractWordText($child);

                        if ($childText !== null) {
                            $cellText[] = $childText;
                        }
                    }

                    if (!empty($cellText)) {
                        $rowText[] = implode(' ', $cellText);
                    }
                }

                if (!empty($rowText)) {
                    $lines[] = implode(' | ', $rowText);
                }
            }

            return !empty($lines)
                ? implode("\n", $lines)
                : null;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | WORD HEADING DETECTION
    |--------------------------------------------------------------------------
    */

    protected function isHeading($element): bool
    {
        /*
         * PhpWord paragraph style.
         */
        if (method_exists($element, 'getParagraphStyle')) {

            $style = $element->getParagraphStyle();

            if ($style) {

                if (is_object($style)) {

                    if (method_exists($style, 'getStyleName')) {

                        $name = strtolower(
                            (string) $style->getStyleName()
                        );

                        if (str_contains($name, 'heading')) {
                            return true;
                        }
                    }

                    if (method_exists($style, 'getName')) {

                        $name = strtolower(
                            (string) $style->getName()
                        );

                        if (str_contains($name, 'heading')) {
                            return true;
                        }
                    }
                }
            }
        }

        /*
         * Text style can sometimes carry heading information.
         */
        if (method_exists($element, 'getStyle')) {

            $style = $element->getStyle();

            if (is_object($style)) {

                if (method_exists($style, 'getStyleName')) {

                    $name = strtolower(
                        (string) $style->getStyleName()
                    );

                    if (str_contains($name, 'heading')) {
                        return true;
                    }
                }

                if (method_exists($style, 'getName')) {

                    $name = strtolower(
                        (string) $style->getName()
                    );

                    if (str_contains($name, 'heading')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | FIELD HELPERS
    |--------------------------------------------------------------------------
    */

    protected function looksLikeField(string $text): bool
    {
        $text = trim($text);

        if ($text === '') {
            return false;
        }

        if (
            preg_match(
                '/^(q(?:uestion)?\s*)?\d+[\.\):\-]\s*/i',
                $text
            )
        ) {
            return true;
        }

        if (
            str_contains($text, ':') ||
            str_contains($text, '☐') ||
            str_contains($text, '□') ||
            preg_match('/\[[a-zA-Z0-9_-]+\]/', $text)
        ) {
            return true;
        }

        $knownFieldWords = [
            'name',
            'email',
            'phone',
            'mobile',
            'date',
            'dob',
            'birth',
            'address',
            'comment',
            'resume',
            'cv',
            'skills',
            'qualification',
            'percentage',
            'passing year',
        ];

        $lower = strtolower($text);

        foreach ($knownFieldWords as $word) {

            if (str_contains($lower, $word)) {
                return true;
            }
        }

        return false;
    }

    protected function isDescriptionLine(string $text): bool
    {
        $lower = strtolower(trim($text));

        return str_starts_with($lower, 'please ')
            || str_starts_with($lower, 'kindly ')
            || str_starts_with($lower, 'enter ')
            || str_starts_with($lower, 'fill ')
            || str_starts_with($lower, 'complete ');
    }

    protected function guessFieldType(string $label): string
    {
        $label = strtolower($label);

        if (
            str_contains($label, 'email') ||
            str_contains($label, 'e-mail')
        ) {
            return 'email';
        }

        if (
            str_contains($label, 'phone') ||
            str_contains($label, 'mobile') ||
            str_contains($label, 'contact')
        ) {
            return 'number';
        }

        if (
            str_contains($label, 'percentage') ||
            str_contains($label, 'marks') ||
            str_contains($label, 'score') ||
            str_contains($label, 'passing year') ||
            preg_match('/\byear\b/', $label)
        ) {
            return 'number';
        }

        if (
            str_contains($label, 'date') ||
            str_contains($label, 'dob') ||
            str_contains($label, 'birth')
        ) {
            return 'date';
        }

        if (
            str_contains($label, 'resume') ||
            str_contains($label, 'cv') ||
            str_contains($label, 'document') ||
            str_contains($label, 'attachment')
        ) {
            return 'file';
        }

        if (
            str_contains($label, 'description') ||
            str_contains($label, 'summary') ||
            str_contains($label, 'address') ||
            str_contains($label, 'comment') ||
            str_contains($label, 'feedback')
        ) {
            return 'textarea';
        }

        return 'text';
    }

    protected function makeField(
        string $label,
        string $type = 'text',
        array $options = [],
        bool $required = false
    ): array {
        $field = [
            'id' => $this->uuid(),
            'type' => $type,
            'key' => $this->makeKey($label),
            'label' => trim($label),
            'placeholder' => null,
            'help_text' => null,
            'default' => null,
            'required' => $required,
            'validation' => [],
        ];

        if (!empty($options)) {
            $field['options'] = $options;
        }

        return $field;
    }

    /*
    |--------------------------------------------------------------------------
    | EXCEL
    |--------------------------------------------------------------------------
    */

    protected function parseExcel(string $path): array
    {
        $spreadsheet = IOFactory::load($path);

        $sheet = $spreadsheet->getActiveSheet();

        $rows = $sheet->toArray(
            null,
            true,
            true,
            true
        );

        if (empty($rows)) {
            return $this->emptySchema();
        }

        /*
         * Supported Excel layout:
         *
         * Section | Label | Type | Required | Options
         *
         * Personal Information | Full Name | text | yes |
         * Personal Information | Gender | radio | yes | Male:male/Female:female
         *
         * Also supports the simple header-row layout:
         *
         * Full Name | Email | Phone | DOB
         */
        $firstRow = array_map(
            fn($value) => strtolower(trim((string) $value)),
            reset($rows)
        );

        $hasStructuredLayout =
            in_array('label', $firstRow, true) ||
            in_array('field', $firstRow, true);

        if ($hasStructuredLayout) {
            return $this->parseStructuredExcel($rows);
        }

        /*
         * Simple header row.
         */
        $headers = array_shift($rows);

        $fields = [];

        foreach ($headers as $header) {

            $header = trim((string) $header);

            if ($header === '') {
                continue;
            }

            $meta = $this->extractMetadata($header);

            $fields[] = $this->makeField(
                label: $meta['label'],
                type: $meta['type'] ?? $this->guessFieldType($meta['label']),
                required: $meta['required']
            );
        }

        return [
            'version' => '1.0',
            'title' => 'Imported Form',
            'description' => 'Form imported from Excel.',
            'settings' => [
                'submit_button' => 'Submit',
            ],
            'sections' => [
                [
                    'id' => $this->uuid(),
                    'title' => 'Imported Fields',
                    'fields' => $fields,
                ],
            ],
        ];
    }

    protected function parseStructuredExcel(array $rows): array
    {
        $header = array_shift($rows);

        $columns = [];

        foreach ($header as $column => $name) {

            $name = strtolower(trim((string) $name));

            $columns[$name] = $column;
        }

        $sectionColumn =
            $columns['section'] ??
            null;

        $labelColumn =
            $columns['label'] ??
            $columns['field'] ??
            null;

        $typeColumn =
            $columns['type'] ??
            null;

        $requiredColumn =
            $columns['required'] ??
            null;

        $optionsColumn =
            $columns['options'] ??
            null;

        if ($labelColumn === null) {
            return $this->emptySchema();
        }

        $sections = [];

        foreach ($rows as $row) {

            $label = trim(
                (string) ($row[$labelColumn] ?? '')
            );

            if ($label === '') {
                continue;
            }

            $sectionName = $sectionColumn !== null
                ? trim((string) ($row[$sectionColumn] ?? 'General'))
                : 'General';

            if ($sectionName === '') {
                $sectionName = 'General';
            }

            if (!isset($sections[$sectionName])) {

                $sections[$sectionName] = [
                    'id' => $this->uuid(),
                    'title' => $sectionName,
                    'fields' => [],
                ];
            }

            $type = $typeColumn !== null
                ? strtolower(trim((string) ($row[$typeColumn] ?? '')))
                : '';

            if ($type === 'select') {
                $type = 'dropdown';
            }

            if ($type === '') {
                $type = $this->guessFieldType($label);
            }

            $required = false;

            if ($requiredColumn !== null) {

                $requiredValue = strtolower(
                    trim((string) ($row[$requiredColumn] ?? ''))
                );

                $required = in_array(
                    $requiredValue,
                    ['yes', 'true', '1', 'required'],
                    true
                );
            }

            $options = [];

            if ($optionsColumn !== null) {

                $optionsValue = trim(
                    (string) ($row[$optionsColumn] ?? '')
                );

                if ($optionsValue !== '') {
                    $options = $this->parseOptions($optionsValue);
                }
            }

            /*
             * Also support metadata directly inside label.
             */
            $meta = $this->extractMetadata($label);

            if ($meta['type'] !== null) {
                $type = $meta['type'];
            }

            if ($meta['required']) {
                $required = true;
            }

            $label = $meta['label'];

            $sections[$sectionName]['fields'][] =
                $this->makeField(
                    label: $label,
                    type: $type,
                    options: $options,
                    required: $required
                );
        }

        return [
            'version' => '1.0',
            'title' => 'Imported Form',
            'description' => 'Form imported from Excel.',
            'settings' => [
                'submit_button' => 'Submit',
            ],
            'sections' => array_values($sections),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    protected function makeKey(string $label): string
    {
        return Str::slug($label, '_')
            ?: 'field_' . uniqid();
    }

    protected function uuid(): string
    {
        return (string) Str::uuid();
    }

    protected function emptySchema(): array
    {
        return [
            'version' => '1.0',
            'title' => 'Imported Form',
            'description' => null,
            'settings' => [
                'submit_button' => 'Submit',
            ],
            'sections' => [],
        ];
    }
}
