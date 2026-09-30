<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::singleton('locale', LocaleController::class)->only(['update']);

Route::get('/', function () {
    return view('welcome');
});

Route::controller(AuthController::class)->prefix('auth')->name('auth.')->group(function () {
    // Forgot password
    Route::get('/forgot-password', 'showForgotPassword')->name('forgot-password');
    Route::post('/forgot-password', 'forgotPassword');

    // Reset password
    Route::get('/reset-password/{token}', 'showResetPassword')->name('reset-password');
    Route::post('/reset-password', 'resetPassword');

    // Sign in
    Route::get('/sign-in', 'showSignIn')->name('sign_in');
    Route::post('/sign-in', 'signIn');

    // Sign out
    Route::post('/sign-out', 'signOut')->name('sign_out');

    // Sign up
    Route::get('/sign-up', 'showSignUp')->name('sign_up');
    Route::post('/sign-up', 'signUp');
});
