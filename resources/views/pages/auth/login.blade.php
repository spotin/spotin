<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['guest']);
name('login');

render(function (View $view) {
    Head::title(__('ui.auth.login.title'))
        ->description(__('ui.auth.login.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.login.title') }}</h1>

    @if (session('status'))
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ session('status') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.login.form.fields.email.placeholder') }}</span>
                    <x-lucide-mail class="h-[1em] opacity-50" />
                    <input
                        name="email"
                        type="email"
                        required
                        placeholder="{{ __('ui.auth.login.form.fields.email.label') }}"
                        value="{{ old('email') }}"
                        @error('email') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('email')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.login.form.fields.password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.login.form.fields.password.label') }}"
                        @error('password') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('password')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

            <label class="label">
                <input type="checkbox" name="remember" class="checkbox" @checked(old('remember')) />
                {{ __('ui.auth.login.form.fields.remember.label') }}
            </label>
        </fieldset>

        <button type="submit" class="btn btn-block">{{ __('ui.auth.login.form.actions.submit') }}</button>
    </form>

    <p class="text-center text-sm">
        {{ __('ui.auth.login.dont_have_an_account') }}
        <a href="{{ route('register') }}"> {{ __('ui.auth.login.register') }} </a>
    </p>

    <p class="text-center text-sm">
        {{ __('ui.auth.login.forgot_your_password') }}
        <a href="{{ route('password.request') }}"> {{ __('ui.auth.login.reset_your_password') }} </a>
    </p>
</x-default-layout>
