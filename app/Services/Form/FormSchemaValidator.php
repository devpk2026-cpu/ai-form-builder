<?php

namespace App\Services\Form;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class FormSchemaValidator
{
    public function validate(array $schema): array
    {
        $validator = Validator::make(
            $schema,
            $this->rules()
        );

        if ($validator->fails()) {
            throw ValidationException::withMessages(
                $validator->errors()->toArray()
            );
        }

        $this->validateCustomRules($schema);

        return $schema;
    }

    protected function rules(): array
    {
        return [
            'version' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'settings' => ['nullable', 'array'],
            'settings.submit_button' => [
                'nullable',
                'string',
                'max:100',
            ],

            'sections' => [
                'required',
                'array',
                'min:1',
            ],

            'sections.*' => [
                'required',
                'array',
            ],

            'sections.*.id' => [
                'required',
                'string',
            ],

            'sections.*.title' => [
                'required',
                'string',
                'max:255',
            ],

            'sections.*.fields' => [
                'required',
                'array',
            ],

            'sections.*.fields.*.id' => [
                'required',
                'string',
            ],

            'sections.*.fields.*.type' => [
                'required',
                'string',
            ],

            'sections.*.fields.*.key' => [
                'required',
                'string',
                'regex:/^[a-zA-Z][a-zA-Z0-9_]*$/',
            ],

            'sections.*.fields.*.label' => [
                'required',
                'string',
                'max:255',
            ],

            'sections.*.fields.*.placeholder' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sections.*.fields.*.help_text' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'sections.*.fields.*.required' => [
                'required',
                'boolean',
            ],

            'sections.*.fields.*.validation' => [
                'nullable',
                'array',
            ],
        ];
    }

    protected function validateCustomRules(array $schema): void
    {
        $keys = [];

        foreach ($schema['sections'] as $section) {
            foreach ($section['fields'] as $field) {

                if (! FieldDefinition::exists($field['type'])) {
                    throw ValidationException::withMessages([
                        'schema' => [
                            "Unsupported field type: {$field['type']}"
                        ],
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Unique field key
            |--------------------------------------------------------------------------
            */

                if (in_array($field['key'], $keys, true)) {
                    throw ValidationException::withMessages([
                        'schema' => [
                            "Duplicate field key: {$field['key']}"
                        ],
                    ]);
                }

                $keys[] = $field['key'];

                /*
            |--------------------------------------------------------------------------
            | Options
            |--------------------------------------------------------------------------
            */

                if (
                    in_array(
                        $field['type'],
                        ['dropdown', 'radio', 'checkbox'],
                        true
                    )
                    && empty($field['options'])
                ) {
                    throw ValidationException::withMessages([
                        'schema' => [
                            "Field '{$field['key']}' requires options."
                        ],
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Option validation
            |--------------------------------------------------------------------------
            */

                foreach ($field['options'] ?? [] as $option) {

                    if (
                        empty($option['label'])
                        ||
                        empty($option['value'])
                    ) {
                        throw ValidationException::withMessages([
                            'schema' => [
                                "Every option for '{$field['key']}' must have label and value."
                            ],
                        ]);
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Validation settings
            |--------------------------------------------------------------------------
            */

                $validation = $field['validation'] ?? [];

                if (
                    isset($validation['min'])
                    &&
                    isset($validation['max'])
                    &&
                    $validation['min'] > $validation['max']
                ) {
                    throw ValidationException::withMessages([
                        'schema' => [
                            "Minimum value cannot be greater than maximum value for '{$field['key']}'."
                        ],
                    ]);
                }

                if (
                    isset($validation['min_length'])
                    &&
                    isset($validation['max_length'])
                    &&
                    $validation['min_length'] > $validation['max_length']
                ) {
                    throw ValidationException::withMessages([
                        'schema' => [
                            "Minimum length cannot be greater than maximum length for '{$field['key']}'."
                        ],
                    ]);
                }
            }
        }
    }
}
