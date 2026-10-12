<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['guest']);
name('password.request');

render(function (View $view) {
    Head::title(__('ui.auth.forgot-password.title'))
        ->description(__('ui.auth.forgot-password.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.forgot-password.title') }}</h1>

    @if (session('status'))
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ session('status') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill-the-form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.common.forms.fields.email.placeholder') }}</span>
                    <x-lucide-mail class="h-[1em] opacity-50" />
                    <input
                        name="email"
                        type="email"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.email.label') }}"
                        value="{{ old('email') }}"
                        @error('email') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('email')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <button type="submit" class="btn btn-block">{{ __('ui.auth.forgot-password.form.actions.submit') }}</button>
    </form>
</x-default-layout>
