<?php

use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Organizations\InvitationController;
use App\Http\Controllers\Organizations\MemberController;
use App\Http\Controllers\Organizations\OrganizationController;
use App\Http\Controllers\Spots\SpotController;
use App\Models\Organization;
use Illuminate\Support\Facades\Route;

Route::singleton('locale', LocaleController::class)->only(['update']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('organizations/{organization:uuid}')->group(function () {
        Route::delete('membership', [MemberController::class, 'leave'])
            ->middleware('can:leave,organization')
            ->name('organizations.leave');

        Route::middleware('can:manageMembers,organization')->scopeBindings()->group(function () {
            Route::put('members/{user}', [MemberController::class, 'update'])
                ->name('organizations.members.update');
            Route::delete('members/{user}', [MemberController::class, 'destroy'])
                ->name('organizations.members.destroy');

            Route::post('invitations', [InvitationController::class, 'store'])
                ->name('organizations.invitations.store');
            Route::post('invitations/{invitation}/resend', [InvitationController::class, 'resend'])
                ->name('organizations.invitations.resend');
            Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])
                ->name('organizations.invitations.destroy');
        });
    });

    Route::post('organizations/invitations/{invitation}/accept', [InvitationController::class, 'acceptPending'])
        ->name('organizations.invitations.pending.accept');
    Route::delete('organizations/invitations/{invitation}', [InvitationController::class, 'decline'])
        ->name('organizations.invitations.pending.decline');

    Route::post('spots', [SpotController::class, 'store'])->name('spots.store');

    Route::prefix('spots/{spot:uuid}')->name('spots.')->group(function () {
        Route::put('/', [SpotController::class, 'update'])->middleware('can:update,spot')->name('update');
        Route::delete('/', [SpotController::class, 'destroy'])->middleware('can:delete,spot')->name('destroy');
    });

    Route::post('organizations', [OrganizationController::class, 'store'])
        ->middleware('can:create,'.Organization::class)
        ->name('organizations.store');
    Route::put('organizations/{organization:uuid}', [OrganizationController::class, 'update'])
        ->middleware('can:update,organization')
        ->name('organizations.update');
    Route::delete('organizations/{organization:uuid}', [OrganizationController::class, 'destroy'])
        ->middleware('can:delete,organization')
        ->name('organizations.destroy');
});
