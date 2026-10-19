<?php

use App\Http\Controllers\Api\V1\Organizations\OrganizationController;
use App\Http\Controllers\Api\V1\Organizations\SpotController as OrganizationSpotController;
use App\Http\Controllers\Api\V1\Spots\SpotController;
use App\Http\Controllers\Api\V1\UserController;
use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->middleware(['auth:sanctum', 'verified', 'throttle:api'])->group(function () {
    // Needs a valid token but no specific ability, so that a client can check what its token allows.
    Route::get('user', UserController::class)->name('user');

    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('/', [OrganizationController::class, 'index'])
            ->middleware('abilities:organizations:read')->name('index');
        Route::get('{organization:uuid}', [OrganizationController::class, 'show'])
            ->middleware('abilities:organizations:read')->name('show');
        Route::post('/', [OrganizationController::class, 'store'])
            ->middleware(['abilities:organizations:create', 'can:create,'.Organization::class])->name('store');
        Route::match(['put', 'patch'], '{organization:uuid}', [OrganizationController::class, 'update'])
            ->middleware(['abilities:organizations:update', 'can:update,organization'])->name('update');
        Route::delete('{organization:uuid}', [OrganizationController::class, 'destroy'])
            ->middleware(['abilities:organizations:delete', 'can:delete,organization'])->name('destroy');

        Route::get('{organization:uuid}/spots', [OrganizationSpotController::class, 'index'])
            ->middleware('abilities:spots:read')->name('spots.index');
        Route::post('{organization:uuid}/spots', [OrganizationSpotController::class, 'store'])
            ->middleware(['abilities:spots:create', 'can:create,'.Spot::class.',organization'])->name('spots.store');
    });

    Route::prefix('spots')->name('spots.')->group(function () {
        Route::get('/', [SpotController::class, 'index'])
            ->middleware('abilities:spots:read')->name('index');
        Route::get('{spot:uuid}', [SpotController::class, 'show'])
            ->middleware('abilities:spots:read')->name('show');
        Route::match(['put', 'patch'], '{spot:uuid}', [SpotController::class, 'update'])
            ->middleware(['abilities:spots:update', 'can:update,spot'])->name('update');
        Route::delete('{spot:uuid}', [SpotController::class, 'destroy'])
            ->middleware(['abilities:spots:delete', 'can:delete,spot'])->name('destroy');
    });
});
