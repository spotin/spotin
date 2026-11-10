<?php

use App\Http\Controllers\Api\V1\MeController;
use App\Http\Controllers\Api\V1\Organizations\OrganizationController;
use App\Http\Controllers\Api\V1\Spots\SpotController;
use App\Models\Organization;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->middleware(['auth:sanctum', 'verified', 'throttle:api'])->group(function () {
    // Needs a valid token but no specific ability, so that a client can check what its token allows.
    Route::get('me', MeController::class)->name('me');

    Route::prefix('organizations')->name('organizations.')->group(function () {
        Route::get('/', [OrganizationController::class, 'index'])
            ->middleware('abilities:organizations:read')->name('index');
        // Deleted too, for who can restore it, as its page
        Route::get('{organization:uuid}', [OrganizationController::class, 'show'])
            ->withTrashed()
            ->middleware('abilities:organizations:read')->name('show');
        Route::post('/', [OrganizationController::class, 'store'])
            ->middleware(['abilities:organizations:create', 'can:create,'.Organization::class])->name('store');
        Route::patch('{organization:uuid}', [OrganizationController::class, 'update'])
            ->middleware(['abilities:organizations:update', 'can:update,organization'])->name('update');
        Route::delete('{organization:uuid}', [OrganizationController::class, 'destroy'])
            ->middleware(['abilities:organizations:delete', 'can:delete,organization'])->name('destroy');
        // Whoever may delete may undo it: with the same ability
        Route::post('{organization:uuid}/restore', [OrganizationController::class, 'restore'])
            ->withTrashed()
            ->middleware(['abilities:organizations:delete', 'can:restore,organization'])->name('restore');
    });

    Route::prefix('spots')->name('spots.')->group(function () {
        Route::get('/', [SpotController::class, 'index'])
            ->middleware('abilities:spots:read')->name('index');
        Route::post('/', [SpotController::class, 'store'])
            ->middleware('abilities:spots:create')->name('store');
        // Deleted too, for who can restore it, as its page
        Route::get('{spot:uuid}', [SpotController::class, 'show'])
            ->withTrashed()
            ->middleware('abilities:spots:read')->name('show');
        Route::patch('{spot:uuid}', [SpotController::class, 'update'])
            ->middleware(['abilities:spots:update', 'can:update,spot'])->name('update');
        Route::delete('{spot:uuid}', [SpotController::class, 'destroy'])
            ->middleware(['abilities:spots:delete', 'can:delete,spot'])->name('destroy');
        Route::post('{spot:uuid}/restore', [SpotController::class, 'restore'])
            ->withTrashed()
            ->middleware(['abilities:spots:delete', 'can:restore,spot'])->name('restore');
    });
});
