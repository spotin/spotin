<?php

use App\Http\Controllers\LocaleController;
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
