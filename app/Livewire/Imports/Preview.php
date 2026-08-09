<?php

namespace App\Livewire\Imports;

use App\Models\Form;
use App\Models\Import;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Preview extends Component
{
    public Import $import;

    public array $schema = [];

    public function mount(Import $import): void
    {
        abort_unless(
            $import->user_id === Auth::id(),
            403
        );

        abort_unless(
            $import->status === 'parsed',
            404
        );

        $this->import = $import;

        $this->schema = $import->parsed_data ?? [];
    }

    public function removeField(
        int $sectionIndex,
        int $fieldIndex
    ): void {
        unset(
            $this->schema['sections'][$sectionIndex]['fields'][$fieldIndex]
        );

        $this->schema['sections'][$sectionIndex]['fields'] =
            array_values(
                $this->schema['sections'][$sectionIndex]['fields']
            );
    }

    public function createForm(): void
    {
        $this->validate([
            'schema.title' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        $form = Form::create([
            'user_id' => Auth::id(),
            'title' => $this->schema['title'],
            'slug' => Str::slug(
                $this->schema['title']
            ) . '-' . Str::lower(
                Str::random(6)
            ),
            'description' =>
                $this->schema['description'] ?? null,
            'schema_json' => $this->schema,
            'status' => 'draft',
        ]);

        $this->import->update([
            'form_id' => $form->id,
            'status' => 'completed',
        ]);

        $this->redirect(
            route('forms.edit', $form),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.imports.preview')
            ->layout('layouts.app');
    }
}