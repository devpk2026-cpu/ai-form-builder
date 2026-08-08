<?php

namespace App\Services\Form;

class FormValidationService
{
    public function buildRules(array $schema): array
    {
        $rules = [];

        foreach ($schema['sections'] as $section) {
            foreach ($section['fields'] as $field) {
                if ($field['type'] === 'section') {
                    continue;
                }

                $rules[$field['key']] = $this->rulesForField($field);
            }
        }

        return $rules;
    }

    protected function rulesForField(array $field): array
    {
        $rules = [];

        if ($field['required'] ?? false) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        switch ($field['type']) {
            case 'text':
            case 'textarea':
                $rules[] = 'string';
                break;

            case 'email':
                $rules[] = 'email';
                break;

            case 'number':
                $rules[] = 'numeric';
                break;

            case 'date':
                $rules[] = 'date';
                break;

            case 'phone':
                $rules[] = 'string';
                break;

            case 'dropdown':
            case 'radio':
                $rules[] = 'string';
                break;

            case 'checkbox':
                $rules[] = 'array';
                break;

            case 'rating':
                $rules[] = 'integer';
                $rules[] = 'min:1';
                $rules[] = 'max:5';
                break;

            case 'file':
                $rules[] = 'file';
                break;
        }

        return $rules;
    }
}