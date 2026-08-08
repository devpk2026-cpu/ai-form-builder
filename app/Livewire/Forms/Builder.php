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

    public function mount(?Form $form = null): void
    {
        $this->form = $form;

        if ($this->form) {
            $this->authorize('update', $this->form);

            $this->title = $this->form->title;
            $this->description = $this->form->description ?? '';
            $this->status = $this->form->status;

            $this->sections = $this->form->schema_json['sections'] ?? [];

            return;
        }

        $this->sections = [
            [
                'id' => (string) Str::uuid(),
                'title' => 'Untitled Section',
                'fields' => [],
            ],
        ];
    }

    public function addSection(): void
    {
        $this->sections[] = [
            'id' => (string) Str::uuid(),
            'title' => 'New Section',
            'fields' => [],
        ];
    }

    public function removeSection(int $sectionIndex): void
    {
        unset($this->sections[$sectionIndex]);

        $this->sections = array_values($this->sections);
    }

    public function addField(int $sectionIndex, string $type): void
    {
        $this->sections[$sectionIndex]['fields'][] = [
            'id' => (string) Str::uuid(),
            'type' => $type,
            'key' => 'field_' . Str::lower(Str::random(8)),
            'label' => 'New Field',
            'placeholder' => null,
            'help_text' => null,
            'default' => null,
            'required' => false,
            'validation' => [],
            'options' => in_array(
                $type,
                ['dropdown', 'radio', 'checkbox'],
                true
            )
                ? []
                : null,
        ];
    }

    public function removeField(
        int $sectionIndex,
        int $fieldIndex
    ): void {
        unset(
            $this->sections[$sectionIndex]['fields'][$fieldIndex]
        );

        $this->sections[$sectionIndex]['fields'] =
            array_values(
                $this->sections[$sectionIndex]['fields']
            );
    }

    public function save(FormSchemaValidator $schemaValidator): void
    {
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

        session()->flash(
            'success',
            'Form created successfully.'
        );

        $this->redirect(
            route('forms.edit', $this->form),
            navigate: true
        );
    }

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

    public function render()
    {
        return view('livewire.forms.builder')
            ->layout('layouts.app');
    }
}
