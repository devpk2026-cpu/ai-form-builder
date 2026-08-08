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
        foreach ($schema['sections'] as $section) {
            foreach ($section['fields'] as $field) {
                if (! FieldDefinition::exists($field['type'])) {
                    throw ValidationException::withMessages([
                        'schema' => [
                            "Unsupported field type: {$field['type']}"
                        ],
                    ]);
                }

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
            }
        }
    }
}