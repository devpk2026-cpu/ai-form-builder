<?php

namespace App\Livewire\Imports;

use App\Models\Import;
use App\Services\Import\FormImportParser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Create extends Component
{
    use WithFileUploads;

    public $file;

    public function analyzeImport(): void
    {
        $this->validate([
            'file' => [
                'required',
                'file',
                'mimes:docx,xlsx',
                'max:10240',
            ],
        ]);

        $path = $this->file->store(
            'imports/' . Auth::id(),
            'local'
        );

        $import = Import::create([
            'user_id' => Auth::id(),
            'form_id' => null,
            'file_name' => $this->file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $this->file->getClientOriginalExtension(),
            'status' => 'processing',
        ]);

        try {

            $parser = app(FormImportParser::class);

            $schema = $parser->parse(
                Storage::disk('local')->path($path),
                $import->file_type
            );

            $import->update([
                'status' => 'parsed',
                'parsed_data' => $schema,
                'error_message' => null,
            ]);

            session()->flash(
                'success',
                'File uploaded and parsed successfully.'
            );

            $this->redirect(
                route('imports.preview', $import),
                navigate: true
            );
        } catch (\Throwable $e) {

            report($e);

            $import->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->addError(
                'file',
                'Unable to parse this file: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.imports.create')
            ->layout('layouts.app');
    }
}
