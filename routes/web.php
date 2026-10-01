<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::singleton('locale', LocaleController::class)->only(['update']);

Route::get('/', function () {
    return view('welcome');
})->name('homepage');

Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
    // Forgot password
    Route::get('/forgot-password', 'forgotPassword')->name('forgot-password');
    Route::post('/forgot-password', 'forgotPasswordSubmit')->name('forgot-password.submit');

    // Reset password
    Route::get('/reset-password/{token}', 'resetPassword')->name('reset-password');
    Route::post('/reset-password', 'resetPasswordSubmit')->name('reset-password.submit');

    // Sign in
    Route::get('/sign-in', 'signIn')->name('sign_in');
    Route::post('/sign-in', 'signInSubmit')->name('sign_in.submit');

    // Sign out
    Route::get('/sign-out', 'signOut')->name('sign_out');
    Route::post('/sign-out', 'signOutSubmit')->name('sign_out.submit');

    // Sign up
    Route::get('/sign-up', 'signUp')->name('sign_up');
    Route::post('/sign-up', 'signUpSubmit')->name('sign_up.submit');
});
