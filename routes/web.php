<?php

use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

Route::singleton('locale', LocaleController::class)->only(['update']);
