<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Head\Facades\Head;

Route::singleton('locale', LocaleController::class)->only(['update']);

Route::get('/', function () {
    Head::title(__('ui.welcome.title'))
        ->description(__('ui.welcome.description'));

    return view('welcome');
});

Route::get('/dashboard', function () {
    Head::title(__('ui.dashboard.title'))
        ->description(__('ui.dashboard.description'));

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::singleton('profile', ProfileController::class)
    ->destroyable()
    ->middleware(['auth', 'verified'])
    ->withHead(
        title: __('ui.profile.title'),
        description: __('ui.profile.description')
    )
    ->only(['show', 'destroy']);
