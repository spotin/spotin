<?php

use App\Http\Controllers\Invitations\InvitationController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Organizations\InvitationController as OrganizationInvitationController;
use App\Http\Controllers\Organizations\MemberController;
use App\Http\Controllers\Organizations\OrganizationController;
use App\Http\Controllers\Organizations\SpotController as OrganizationSpotController;
use App\Http\Controllers\Settings\TokenController;
use App\Http\Controllers\Spots\SpotController;
use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Support\Facades\Route;

Route::singleton('locale', LocaleController::class)->only(['update']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('organizations/{organization:uuid}')->group(function () {
        Route::post('spots', [OrganizationSpotController::class, 'store'])
            ->middleware('can:create,'.Spot::class.',organization')
            ->name('organizations.spots.store');

        Route::delete('membership', [MemberController::class, 'leave'])
            ->middleware('can:leave,organization')
            ->name('organizations.leave');

        Route::middleware('can:manageMembers,organization')->scopeBindings()->group(function () {
            Route::put('members/{user}', [MemberController::class, 'update'])
                ->name('organizations.members.update');
            Route::delete('members/{user}', [MemberController::class, 'destroy'])
                ->name('organizations.members.destroy');

            Route::post('invitations', [OrganizationInvitationController::class, 'store'])
                ->name('organizations.invitations.store');
            Route::post('invitations/{invitation}/resend', [OrganizationInvitationController::class, 'resend'])
                ->name('organizations.invitations.resend');
            Route::delete('invitations/{invitation}', [OrganizationInvitationController::class, 'destroy'])
                ->name('organizations.invitations.destroy');
        });
    });

    Route::post('invitations/{invitation}/accept', [InvitationController::class, 'accept'])
        ->name('invitations.accept');
    Route::delete('invitations/{invitation}', [InvitationController::class, 'decline'])
        ->name('invitations.decline');

    Route::post('settings/developer/tokens', [TokenController::class, 'store'])->name('settings.developer.tokens.store');
    Route::delete('settings/developer/tokens/{token}', [TokenController::class, 'destroy'])
        ->whereNumber('token')
        ->name('settings.developer.tokens.destroy');

    Route::prefix('spots/{spot:uuid}')->name('spots.')->group(function () {
        Route::patch('/', [SpotController::class, 'update'])->middleware('can:update,spot')->name('update');
        Route::delete('/', [SpotController::class, 'destroy'])->middleware('can:delete,spot')->name('destroy');
    });

    Route::post('organizations', [OrganizationController::class, 'store'])
        ->middleware('can:create,'.Organization::class)
        ->name('organizations.store');
    Route::patch('organizations/{organization:uuid}', [OrganizationController::class, 'update'])
        ->middleware('can:update,organization')
        ->name('organizations.update');
    Route::delete('organizations/{organization:uuid}', [OrganizationController::class, 'destroy'])
        ->middleware('can:delete,organization')
        ->name('organizations.destroy');
});
