<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\ProfileWebController;
use App\Http\Controllers\Web\SpotWebController;
use App\Http\Controllers\Web\TokenWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));
Route::get('/prices', fn () => view('prices'));
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['fr', 'en', 'de'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// Auth
Route::get('/auth/login', [AuthWebController::class, 'loginForm'])->name('auth.login');
Route::post('/auth/login', [AuthWebController::class, 'login']);
Route::get('/auth/register', [AuthWebController::class, 'registerForm'])->name('auth.register');
Route::post('/auth/register', [AuthWebController::class, 'register']);
Route::post('/auth/logout', [AuthWebController::class, 'logout'])->name('auth.logout');
Route::get('/auth/reset-password-request', [AuthWebController::class, 'resetPasswordRequestForm'])->name('auth.reset-password-request');
Route::post('/auth/reset-password-request', [AuthWebController::class, 'resetPasswordRequest']);
Route::get('/auth/reset-password/{token}', [AuthWebController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/auth/reset-password', [AuthWebController::class, 'resetPassword'])->name('auth.reset-password');

// Protected web routes
Route::middleware('auth')->group(function () {
    // Spots
    Route::get('/spots', [SpotWebController::class, 'index'])->name('spots.index');
    Route::get('/spots/create', [SpotWebController::class, 'create'])->name('spots.create');
    Route::post('/spots', [SpotWebController::class, 'store'])->name('spots.store');
    Route::get('/spots/{spot}', [SpotWebController::class, 'show'])->name('spots.show');
    Route::get('/spots/{spot}/edit', [SpotWebController::class, 'edit'])->name('spots.edit');
    Route::patch('/spots/{spot}', [SpotWebController::class, 'update'])->name('spots.update');
    Route::delete('/spots/{spot}', [SpotWebController::class, 'destroy'])->name('spots.destroy');

    // Profile
    Route::get('/profile', [ProfileWebController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileWebController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileWebController::class, 'update'])->name('profile.update');

    // Tokens
    Route::get('/tokens', [TokenWebController::class, 'index'])->name('tokens.index');
    Route::get('/tokens/create', [TokenWebController::class, 'create'])->name('tokens.create');
    Route::post('/tokens', [TokenWebController::class, 'store'])->name('tokens.store');
    Route::delete('/tokens/{tokenId}', [TokenWebController::class, 'destroy'])->name('tokens.destroy');
});
