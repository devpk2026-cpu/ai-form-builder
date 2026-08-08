<?php

namespace App\Livewire\Submissions;

use App\Models\Form;
use App\Models\Submission;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    public Form $form;

    public Submission $submission;

    public function mount(
        Form $form,
        Submission $submission
    ): void {
        $this->authorize('view', $form);

        abort_unless(
            $submission->form_id === $form->id,
            404
        );

        $this->form = $form;
        $this->submission = $submission;
    }

    public function download(string $fieldKey)
    {
        $this->authorize('view', $this->form);

        abort_unless(
            $this->submission->form_id === $this->form->id,
            404
        );

        $field = $this->findField($fieldKey);

        abort_unless(
            $field !== null,
            404
        );

        abort_unless(
            ($field['type'] ?? null) === 'file',
            404
        );

        $path = $this->submission->data_json[$fieldKey] ?? null;

        abort_unless(
            is_string($path) && $path !== '',
            404
        );

        // Extra security: only allow files from our submissions directory.
        $expectedPrefix = 'submissions/' . $this->form->id . '/';

        abort_unless(
            str_starts_with($path, $expectedPrefix),
            404
        );

        abort_unless(
            Storage::disk('public')->exists($path),
            404
        );

        return Storage::disk('public')->download(
            $path,
            basename($path)
        );
    }

    private function findField(string $fieldKey): ?array
    {
        foreach ($this->form->schema_json['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {

                if (($field['key'] ?? null) === $fieldKey) {
                    return $field;
                }
            }
        }

        return null;
    }

    public function render()
    {
        return view('livewire.submissions.show')->layout('layouts.app');
    }
}
