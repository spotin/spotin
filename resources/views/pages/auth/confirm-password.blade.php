<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth']);
name('password.confirm');

render(function (View $view) {
    Head::title(__('ui.auth.password-confirmation.title'))
        ->description(__('ui.auth.password-confirmation.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.password-confirmation.title') }}</h1>
    <p>{{ __('ui.auth.password-confirmation.explanation') }}</p>

    <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill-the-form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.common.forms.fields.password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="{{ __('ui.auth.common.forms.fields.password.label') }}"
                        @error('password') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('password')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <button type="submit" class="btn btn-block">
            {{ __('ui.auth.password-confirmation.form.actions.submit') }}
        </button>
    </form>
</x-default-layout>
