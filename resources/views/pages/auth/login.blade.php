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

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.email.label') }}</legend>
            <input
                name="email"
                type="text"
                inputmode="email"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.email.placeholder') }}"
                value="{{ old('email') }}"
                @error('email') aria-invalid="true" @enderror
            />
            @error('email')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.password.label') }}</legend>
            <input
                name="password"
                type="password"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.password.placeholder') }}"
                @error('password') aria-invalid="true" @enderror
            />
            @error('password')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.login.form.options') }}</legend>
            <label class="label">
                <input type="checkbox" name="remember" class="checkbox" @checked(old('remember')) />
                {{ __('ui.auth.common.forms.fields.remember.label') }}
            </label>
        </fieldset>

        <button type="submit" class="btn btn-block mt-4">{{ __('ui.auth.login.form.actions.submit') }}</button>
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
