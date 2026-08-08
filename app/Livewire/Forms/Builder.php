<?php

namespace App\Livewire\Forms;

use App\Models\Form;
use App\Services\Form\FormSchemaValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Builder extends Component
{
    public ?Form $form = null;

    public string $title = '';

    public string $description = '';

    public string $status = 'draft';

    public array $sections = [];

    /*
    |--------------------------------------------------------------------------
    | Builder state
    |--------------------------------------------------------------------------
    */

    public ?string $selectedFieldId = null;

    public int $historyIndex = -1;

    public array $history = [];

    public bool $isDirty = false;

    public function mount(?Form $form = null): void
    {
        $this->form = $form;

        if ($this->form) {
            $this->authorize('update', $this->form);

            $this->title = $this->form->title;
            $this->description = $this->form->description ?? '';
            $this->status = $this->form->status;

            $this->sections = $this->form->schema_json['sections'] ?? [];

            $this->ensureFieldStructure();

            $this->pushHistory();

            return;
        }

        $this->sections = [
            [
                'id' => (string) Str::uuid(),
                'title' => 'Untitled Section',
                'fields' => [],
            ],
        ];

        $this->pushHistory();
    }

    /*
    |--------------------------------------------------------------------------
    | Section management
    |--------------------------------------------------------------------------
    */

    public function addSection(): void
    {
        $this->sections[] = [
            'id' => (string) Str::uuid(),
            'title' => 'New Section',
            'fields' => [],
        ];

        $this->changed();
    }

    public function removeSection(int $sectionIndex): void
    {
        if (! isset($this->sections[$sectionIndex])) {
            return;
        }

        $this->sections = array_values(
            array_filter(
                $this->sections,
                fn ($_, $index) => $index !== $sectionIndex,
                ARRAY_FILTER_USE_BOTH
            )
        );

        if (empty($this->sections)) {
            $this->addSection();

            return;
        }

        $this->selectedFieldId = null;

        $this->changed();
    }

    /*
    |--------------------------------------------------------------------------
    | Field management
    |--------------------------------------------------------------------------
    */

    public function addField(
        int $sectionIndex,
        string $type
    ): void {
        if (! isset($this->sections[$sectionIndex])) {
            return;
        }

        $field = $this->makeField($type);

        $this->sections[$sectionIndex]['fields'][] = $field;

        $this->selectedFieldId = $field['id'];

        $this->changed();
    }

    public function removeField(
        int $sectionIndex,
        int $fieldIndex
    ): void {
        if (! isset(
            $this->sections[$sectionIndex]['fields'][$fieldIndex]
        )) {
            return;
        }

        $removedFieldId =
            $this->sections[$sectionIndex]['fields'][$fieldIndex]['id'];

        unset(
            $this->sections[$sectionIndex]['fields'][$fieldIndex]
        );

        $this->sections[$sectionIndex]['fields'] =
            array_values(
                $this->sections[$sectionIndex]['fields']
            );

        if ($this->selectedFieldId === $removedFieldId) {
            $this->selectedFieldId = null;
        }

        $this->changed();
    }

    protected function makeField(string $type): array
    {
        $field = [
            'id' => (string) Str::uuid(),

            'type' => $type,

            'key' => 'field_' . Str::lower(Str::random(8)),

            'label' => $this->defaultLabel($type),

            'placeholder' => null,

            'help_text' => null,

            'default' => null,

            'required' => false,

            'validation' => [],
        ];

        if (in_array(
            $type,
            ['dropdown', 'radio', 'checkbox'],
            true
        )) {
            $field['options'] = [
                [
                    'label' => 'Option 1',
                    'value' => 'option_1',
                ],
            ];
        }

        if ($type === 'file') {
            $field['validation'] = [
                'file_types' => [],
                'max_size' => null,
            ];
        }

        if ($type === 'rating') {
            $field['validation'] = [
                'min' => 1,
                'max' => 5,
                'step' => 1,
            ];
        }

        return $field;
    }

    protected function defaultLabel(string $type): string
    {
        return match ($type) {
            'text' => 'Text Field',
            'textarea' => 'Textarea',
            'number' => 'Number',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'date' => 'Date',
            'dropdown' => 'Dropdown',
            'radio' => 'Radio',
            'checkbox' => 'Checkbox',
            'file' => 'File Upload',
            'rating' => 'Rating',
            'section' => 'Section Heading',
            default => 'New Field',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Field selection
    |--------------------------------------------------------------------------
    */

    public function selectField(string $fieldId): void
    {
        $this->selectedFieldId = $fieldId;
    }

    public function updatedTitle(): void
    {
        $this->changed();
    }

    public function updatedDescription(): void
    {
        $this->changed();
    }

    public function updatedSections(): void
    {
        $this->changed();
    }

    /*
    |--------------------------------------------------------------------------
    | Drag and Drop
    |--------------------------------------------------------------------------
    */

    public function moveField(
        int $fromSection,
        int $fromIndex,
        int $toSection,
        int $toIndex
    ): void {
        if (
            ! isset($this->sections[$fromSection]['fields'][$fromIndex])
            ||
            ! isset($this->sections[$toSection]['fields'])
        ) {
            return;
        }

        $field = $this->sections[$fromSection]['fields'][$fromIndex];

        unset(
            $this->sections[$fromSection]['fields'][$fromIndex]
        );

        $this->sections[$fromSection]['fields'] =
            array_values(
                $this->sections[$fromSection]['fields']
            );

        $targetFields = $this->sections[$toSection]['fields'];

        $toIndex = max(
            0,
            min($toIndex, count($targetFields))
        );

        array_splice(
            $targetFields,
            $toIndex,
            0,
            [$field]
        );

        $this->sections[$toSection]['fields'] = $targetFields;

        $this->selectedFieldId = $field['id'];

        $this->changed();
    }

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    */

    public function addOption(
        int $sectionIndex,
        int $fieldIndex
    ): void {
        $this->sections[$sectionIndex]['fields'][$fieldIndex]['options'][] = [
            'label' => 'New Option',
            'value' => 'option_' . Str::lower(Str::random(6)),
        ];

        $this->changed();
    }

    public function removeOption(
        int $sectionIndex,
        int $fieldIndex,
        int $optionIndex
    ): void {
        unset(
            $this->sections[$sectionIndex]
                ['fields'][$fieldIndex]
                ['options'][$optionIndex]
        );

        $this->sections[$sectionIndex]
            ['fields'][$fieldIndex]
            ['options'] = array_values(
                $this->sections[$sectionIndex]
                    ['fields'][$fieldIndex]
                    ['options']
            );

        $this->changed();
    }

    /*
    |--------------------------------------------------------------------------
    | History
    |--------------------------------------------------------------------------
    */

    protected function pushHistory(): void
    {
        $state = [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'sections' => $this->sections,
        ];

        // Remove redo states.
        if ($this->historyIndex < count($this->history) - 1) {
            $this->history = array_slice(
                $this->history,
                0,
                $this->historyIndex + 1
            );
        }

        $this->history[] = $state;

        // Keep history manageable.
        if (count($this->history) > 30) {
            array_shift($this->history);
        }

        $this->historyIndex = count($this->history) - 1;
    }

    protected function changed(): void
    {
        $this->isDirty = true;

        $this->pushHistory();
    }

    public function undo(): void
    {
        if ($this->historyIndex <= 0) {
            return;
        }

        $this->historyIndex--;

        $this->restoreHistoryState(
            $this->history[$this->historyIndex]
        );
    }

    public function redo(): void
    {
        if (
            $this->historyIndex >=
            count($this->history) - 1
        ) {
            return;
        }

        $this->historyIndex++;

        $this->restoreHistoryState(
            $this->history[$this->historyIndex]
        );
    }

    protected function restoreHistoryState(array $state): void
    {
        $this->title = $state['title'];
        $this->description = $state['description'];
        $this->status = $state['status'];
        $this->sections = $state['sections'];

        $this->isDirty = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    public function save(
        FormSchemaValidator $schemaValidator
    ): void {
        $this->ensureFieldStructure();

        $schema = [
            'version' => '1.0',

            'title' => $this->title,

            'description' => $this->description ?: null,

            'settings' => [
                'submit_button' => 'Submit',
            ],

            'sections' => $this->sections,
        ];

        $schemaValidator->validate($schema);

        if ($this->form) {
            $this->authorize('update', $this->form);

            $this->form->update([
                'title' => $this->title,
                'description' => $this->description ?: null,
                'status' => $this->status,
                'schema_json' => $schema,
            ]);

            $this->isDirty = false;

            session()->flash(
                'success',
                'Form updated successfully.'
            );

            return;
        }

        $this->form = Auth::user()->forms()->create([
            'title' => $this->title,

            'slug' => $this->generateUniqueSlug(),

            'description' => $this->description ?: null,

            'status' => $this->status,

            'schema_json' => $schema,
        ]);

        $this->isDirty = false;

        session()->flash(
            'success',
            'Form created successfully.'
        );

        $this->redirect(
            route('forms.edit', $this->form),
            navigate: true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Autosave
    |--------------------------------------------------------------------------
    */

    public function autosave(
        FormSchemaValidator $schemaValidator
    ): void {
        if (! $this->form || ! $this->isDirty) {
            return;
        }

        $schema = [
            'version' => '1.0',
            'title' => $this->title,
            'description' => $this->description ?: null,
            'settings' => [
                'submit_button' => 'Submit',
            ],
            'sections' => $this->sections,
        ];

        $schemaValidator->validate($schema);

        $this->authorize('update', $this->form);

        $this->form->update([
            'title' => $this->title,
            'description' => $this->description ?: null,
            'status' => $this->status,
            'schema_json' => $schema,
        ]);

        $this->isDirty = false;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function generateUniqueSlug(): string
    {
        $baseSlug = Str::slug($this->title);

        if ($baseSlug === '') {
            $baseSlug = 'form';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Form::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    protected function ensureFieldStructure(): void
    {
        foreach ($this->sections as $sectionIndex => $section) {
            foreach (
                $section['fields'] ?? []
                as $fieldIndex => $field
            ) {
                $this->sections[$sectionIndex]['fields'][$fieldIndex]
                    ['validation'] ??= [];

                if (in_array(
                    $field['type'],
                    ['dropdown', 'radio', 'checkbox'],
                    true
                )) {
                    $this->sections[$sectionIndex]['fields'][$fieldIndex]
                        ['options'] ??= [];
                }
            }
        }
    }

    public function getSelectedFieldProperty(): ?array
    {
        if (! $this->selectedFieldId) {
            return null;
        }

        foreach ($this->sections as $section) {
            foreach ($section['fields'] as $field) {
                if ($field['id'] === $this->selectedFieldId) {
                    return $field;
                }
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.forms.builder')
            ->layout('layouts.app');
    }
}