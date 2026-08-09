<?php

namespace App\Livewire\Forms;

use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public function delete(int $formId): void
    {
        $form = Form::findOrFail($formId);

        $this->authorize('delete', $form);

        $form->delete();

        session()->flash('success', 'Form deleted successfully.');
    }

    public function render()
    {
        return view('livewire.forms.index', [
            'forms' => Auth::user()
                ->forms()
                ->withCount('submissions')
                ->latest()
                ->paginate(10),
        ])->layout('layouts.app');
    }
}