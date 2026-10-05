<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Head\Facades\Head;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::registerView(function () {
            Head::title(__('ui.auth.register.title'))
                ->description(__('ui.auth.register.description'));

            return view('auth.register');
        });

        Fortify::verifyEmailView(function () {
            Head::title(__('ui.auth.verify_email.title'))
                ->description(__('ui.auth.verify_email.description'));

            return view('auth.verify-email');
        });

        Fortify::loginView(function () {
            Head::title(__('ui.auth.login.title'))
                ->description(__('ui.auth.login.description'));

            return view('auth.login');
        });

        Fortify::requestPasswordResetLinkView(function () {
            Head::title(__('ui.auth.forgot_password.title'))
                ->description(__('ui.auth.forgot_password.description'));

            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(function (Request $request) {
            Head::title(__('ui.auth.reset_password.title'))
                ->description(__('ui.auth.reset_password.description'));

            return view('auth.reset-password', ['request' => $request]);
        });

        Fortify::confirmPasswordView(function () {
            Head::title(__('ui.auth.confirm_password.title'))
                ->description(__('ui.auth.confirm_password.description'));

            return view('auth.confirm-password');
        });

        Fortify::authenticateUsing(function (Request $request) {
            Log::info('test');
            Log::info($request->username);

            $user = User::where('username', $request->username)->orWhere('email', $request->username)->first();

            Log::info('User attempting to authenticate: '.($user ? $user->username : 'No user found'));

            if ($user &&
                Hash::check($request->password, $user->password)) {
                return $user;
            }
        });

        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }
}
