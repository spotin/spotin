<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['guest']);
name('password.reset');

render(function (View $view) {
    Head::title(__('ui.auth.reset_password.title'))
        ->description(__('ui.auth.reset_password.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.reset_password.title') }}</h1>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}" />

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.email.label') }}</legend>
            <input
                name="email"
                type="text"
                inputmode="email"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.email.placeholder') }}"
                value="{{ request('email') }}"
                @error('email') aria-invalid="true" @enderror
            />
            @error('email')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.new_password.label') }}</legend>
            <input
                name="password"
                type="password"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.new_password.placeholder') }}"
                @error('password') aria-invalid="true" @enderror
            />
            @error('password')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.new_password_confirmation.label') }}</legend>
            <input
                name="password_confirmation"
                type="password"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.new_password_confirmation.placeholder') }}"
                @error('password') aria-invalid="true" @enderror
            />
            @error('password')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <button type="submit" class="btn btn-block mt-4">{{ __('ui.auth.reset_password.form.actions.submit') }}</button>
    </form>
</x-default-layout>
