<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['guest']);
name('password.request');

render(function (View $view) {
    Head::title(__('ui.auth.forgot_password.title'))
        ->description(__('ui.auth.forgot_password.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.forgot_password.title') }}</h1>

    @if (session('status'))
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ session('status') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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

        <button type="submit" class="btn btn-block mt-4">{{ __('ui.auth.forgot_password.form.actions.submit') }}</button>
    </form>
</x-default-layout>
