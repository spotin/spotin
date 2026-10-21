<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth']);
name('password.confirm');

render(function (View $view) {
    Head::title(__('ui.auth.password_confirmation.title'))
        ->description(__('ui.auth.password_confirmation.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.password_confirmation.title') }}</h1>
    <p>{{ __('ui.auth.password_confirmation.explanation') }}</p>

    <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.password.label') }}</legend>
            <input
                name="password"
                type="password"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.password.placeholder') }}"
                autocomplete="current-password"
                @error('password') aria-invalid="true" @enderror
            />
            @error('password')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <button type="submit" class="btn btn-block mt-4">
            {{ __('ui.auth.password_confirmation.form.actions.submit') }}
        </button>
    </form>
</x-default-layout>
