<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Forms\Index;
use App\Livewire\Forms\Builder;
use App\Livewire\Forms\PublicForm;
use App\Livewire\Submissions\Index as SubmissionIndex;
use App\Livewire\Submissions\Show as SubmissionShow;
use App\Livewire\Imports\Create as ImportCreate;
use App\Livewire\Imports\Preview as ImportPreview;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/forms', Index::class)
        ->name('forms.index');

    Route::get('/forms/create', Builder::class)
        ->name('forms.create');

    Route::get('/forms/{form}/edit', Builder::class)
        ->name('forms.edit');

    Route::get('/forms/{form}/submissions', SubmissionIndex::class)
        ->name('forms.submissions.index');

    Route::get(
        '/forms/{form}/submissions/{submission}',
        SubmissionShow::class
    )->name('forms.submissions.show');

    Route::get('/imports/create', ImportCreate::class)
        ->name('imports.create');

    Route::get('/imports/{import}/preview', ImportPreview::class)
        ->name('imports.preview');
});

Route::get('/forms/{form:slug}', PublicForm::class)
    ->name('forms.public');
