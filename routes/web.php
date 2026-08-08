<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Forms\Index;
use App\Livewire\Forms\Builder;
use App\Livewire\Forms\PublicForm;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    Route::get('/forms', Index::class)
        ->name('forms.index');

    Route::get('/forms/create', Builder::class)
        ->name('forms.create');

    Route::get('/forms/{form}/edit', Builder::class)
        ->name('forms.edit');

});

Route::get('/forms/{form:slug}', PublicForm::class)
    ->name('forms.public');