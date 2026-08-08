<?php

namespace App\Livewire\Submissions;

use App\Models\Form;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithPagination;

    public Form $form;

    public function mount(Form $form): void
    {
        $this->authorize('view', $form);

        $this->form = $form;
    }

    public function delete(int $submissionId): void
    {
        $this->authorize('view', $this->form);

        $submission = $this->form
            ->submissions()
            ->findOrFail($submissionId);

        $this->deleteUploadedFiles($submission);

        $submission->delete();

        session()->flash(
            'success',
            'Submission deleted successfully.'
        );
    }

    private function deleteUploadedFiles($submission): void
    {
        $data = $submission->data_json ?? [];

        foreach ($this->form->schema_json['sections'] ?? [] as $section) {

            foreach ($section['fields'] ?? [] as $field) {

                if (($field['type'] ?? null) !== 'file') {
                    continue;
                }

                $key = $field['key'] ?? null;

                if (!$key) {
                    continue;
                }

                $path = $data[$key] ?? null;

                if (!is_string($path) || $path === '') {
                    continue;
                }

                $expectedPrefix = 'submissions/' . $this->form->id . '/';

                if (!str_starts_with($path, $expectedPrefix)) {
                    continue;
                }

                Storage::disk('public')->delete($path);
            }
        }
    }

    public function render()
    {
        return view('livewire.submissions.index', [
            'submissions' => $this->form
                ->submissions()
                ->latest('submitted_at')
                ->paginate(10),
        ])->layout('layouts.app');
    }
}
