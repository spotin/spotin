<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SpotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/spots/public', [SpotController::class, 'publicIndex']);
Route::get('/spots/{spot}', [SpotController::class, 'show']);

Route::get('/profile/{username}', [ProfileController::class, 'showByUsername']);

Route::middleware('auth:sanctum')->group(function () {
    Route::delete('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);

    Route::get('/spots', [SpotController::class, 'index']);
    Route::post('/spots', [SpotController::class, 'store']);
    Route::patch('/spots/{spot}', [SpotController::class, 'update']);
    Route::delete('/spots/{spot}', [SpotController::class, 'destroy']);
});
