<?php

namespace App\Livewire\Forms;

use App\Models\Form;
use App\Models\Submission;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PublicForm extends Component
{
    use WithFileUploads;

    public Form $form;

    public array $data = [];

    public bool $submitted = false;

    public function mount(Form $form): void
    {
        abort_unless(
            $form->status === 'published',
            404
        );

        $this->form = $form;

        $this->initializeFields();
    }

    protected function initializeFields(): void
    {
        foreach ($this->form->schema_json['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {

                if ($field['type'] === 'section') {
                    continue;
                }

                $key = $field['key'];

                $default = $field['default'] ?? null;

                if (
                    $field['type'] === 'checkbox'
                    &&
                    $default === null
                ) {
                    $default = [];
                }

                $this->data[$key] = $default;
            }
        }
    }

    public function submit(): void
    {

        $rules = $this->buildValidationRules();

        $validator = Validator::make(
            $this->data,
            $rules,
            [],
            $this->attributeNames()
        );

        $validator->validate();

        $this->storeUploadedFiles();

        Submission::create([
            'form_id' => $this->form->id,

            'data_json' => $this->data,

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

            'submitted_at' => now(),
        ]);

        $this->submitted = true;

        $this->data = [];
    }


    private function buildValidationRules(): array
    {
        $rules = [];

        foreach ($this->form->schema_json['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $key = $field['key'] ?? null;

                if (!$key) {
                    continue;
                }

                $fieldRules = [];

                if (($field['required'] ?? false) === true) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                switch ($field['type']) {

                    case 'text':
                    case 'textarea':
                        $fieldRules[] = 'string';
                        break;

                    case 'email':
                        $fieldRules[] = 'email';
                        break;

                    case 'number':
                        $fieldRules[] = 'numeric';
                        break;

                    case 'date':
                        $fieldRules[] = 'date';
                        break;

                    case 'phone':
                        $fieldRules[] = 'string';
                        break;

                    case 'dropdown':
                    case 'radio':
                        $fieldRules[] = 'string';

                        $values = collect($field['options'] ?? [])
                            ->pluck('value')
                            ->filter()
                            ->values()
                            ->all();

                        if (!empty($values)) {
                            $fieldRules[] = \Illuminate\Validation\Rule::in($values);
                        }

                        break;

                    case 'checkbox':
                        $fieldRules[] = 'array';
                        break;

                    case 'rating':
                        $fieldRules[] = 'integer';

                        $validation = $field['validation'] ?? [];

                        if (isset($validation['min'])) {
                            $fieldRules[] = 'min:' . (int) $validation['min'];
                        }

                        if (isset($validation['max'])) {
                            $fieldRules[] = 'max:' . (int) $validation['max'];
                        }

                        break;

                    case 'file':
                        $fieldRules[] = 'file';

                        $validation = $field['validation'] ?? [];

                        if (!empty($validation['file_types'])) {
                            $mimes = collect(
                                explode(',', $validation['file_types'])
                            )
                                ->map(fn($type) => trim($type))
                                ->filter()
                                ->implode(',');

                            if ($mimes !== '') {
                                $fieldRules[] = 'mimes:' . $mimes;
                            }
                        }

                        if (!empty($validation['max_size'])) {
                            $fieldRules[] = 'max:' . (int) $validation['max_size'];
                        }

                        break;
                }



                $validation = $field['validation'] ?? [];

                if (
                    isset($validation['min_length']) &&
                    in_array($field['type'], ['text', 'textarea'])
                ) {
                    $fieldRules[] = 'min:' . (int) $validation['min_length'];
                }

                if (
                    isset($validation['max_length']) &&
                    in_array($field['type'], ['text', 'textarea'])
                ) {
                    $fieldRules[] = 'max:' . (int) $validation['max_length'];
                }

                $rules[$key] = $fieldRules;
            }
        }

        return $rules;
    }

    // protected function buildValidationRules(): array
    // {
    //     $rules = [];

    //     foreach ($this->form->schema_json['sections'] ?? [] as $section) {

    //         foreach ($section['fields'] ?? [] as $field) {

    //             if ($field['type'] === 'section') {
    //                 continue;
    //             }

    //             $key = $field['key'];

    //             $fieldRules = [];

    //             if ($field['required'] ?? false) {
    //                 $fieldRules[] = 'required';
    //             } else {
    //                 $fieldRules[] = 'nullable';
    //             }

    //             switch ($field['type']) {

    //                 case 'email':
    //                     $fieldRules[] = 'email';
    //                     break;

    //                 case 'number':
    //                     $fieldRules[] = 'numeric';
    //                     break;

    //                 case 'phone':
    //                     $fieldRules[] = 'string';
    //                     break;

    //                 case 'date':
    //                     $fieldRules[] = 'date';
    //                     break;

    //                 case 'file':
    //                     $fieldRules[] = 'file';

    //                     $validation =
    //                         $field['validation'] ?? [];

    //                     if (! empty($validation['max_size'])) {
    //                         $fieldRules[] =
    //                             'max:' . (int) $validation['max_size'];
    //                     }

    //                     break;

    //                 case 'checkbox':
    //                     $fieldRules[] = 'array';
    //                     break;

    //                 default:
    //                     $fieldRules[] = 'string';
    //                     break;
    //             }

    //             $validation =
    //                 $field['validation'] ?? [];

    //             if (
    //                 isset($validation['min_length'])
    //             ) {
    //                 $fieldRules[] =
    //                     'min:' . (int) $validation['min_length'];
    //             }

    //             if (
    //                 isset($validation['max_length'])
    //             ) {
    //                 $fieldRules[] =
    //                     'max:' . (int) $validation['max_length'];
    //             }

    //             if (
    //                 isset($validation['min'])
    //                 &&
    //                 in_array(
    //                     $field['type'],
    //                     ['number', 'rating'],
    //                     true
    //                 )
    //             ) {
    //                 $fieldRules[] =
    //                     'min:' . $validation['min'];
    //             }

    //             if (
    //                 isset($validation['max'])
    //                 &&
    //                 in_array(
    //                     $field['type'],
    //                     ['number', 'rating'],
    //                     true
    //                 )
    //             ) {
    //                 $fieldRules[] =
    //                     'max:' . $validation['max'];
    //             }

    //             if (
    //                 ! empty($validation['regex'])
    //             ) {
    //                 $fieldRules[] =
    //                     'regex:' . $validation['regex'];
    //             }

    //             $rules[$key] = $fieldRules;
    //         }
    //     }

    //     return $rules;
    // }

    protected function attributeNames(): array
    {
        $attributes = [];

        foreach ($this->form->schema_json['sections'] ?? [] as $section) {

            foreach ($section['fields'] ?? [] as $field) {

                if ($field['type'] === 'section') {
                    continue;
                }

                $attributes[$field['key']] =
                    $field['label'];
            }
        }

        return $attributes;
    }

    public function render()
    {
        return view('livewire.forms.public-form')
            ->layout('layouts.public');
    }


    private function storeUploadedFiles(): void
    {
        foreach ($this->data as $key => $value) {

            if ($value instanceof TemporaryUploadedFile) {
                $path = $value->store(
                    'submissions/' . $this->form->id,
                    'public'
                );

                $this->data[$key] = $path;
            }
        }
    }
}
