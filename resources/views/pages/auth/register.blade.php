<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['guest']);
name('register');

render(function (View $view) {
    Head::title(__('ui.auth.register.title'))
        ->description(__('ui.auth.register.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.register.title') }}</h1>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill-the-form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.common.forms.fields.name.placeholder') }}</span>
                    <x-lucide-id-card class="h-[1em] opacity-50" />
                    <input
                        name="name"
                        type="text"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.name.label') }}"
                        value="{{ old('name') }}"
                        @error('name') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('name')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

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

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.common.forms.fields.password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.password.label') }}"
                        @error('password') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('password')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.common.forms.fields.password-confirmation.placeholder') }}</span>
                    <x-lucide-lock-keyhole-open class="h-[1em] opacity-50" />
                    <input
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.password-confirmation.label') }}"
                        @error('password') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('password')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <button type="submit" class="btn btn-block">{{ __('ui.auth.register.form.actions.submit') }}</button>
    </form>

    <p class="text-center text-sm">
        {{ __('ui.auth.register.already-have-an-account') }}
        <a href="{{ route('login') }}"> {{ __('ui.auth.register.login') }} </a>
    </p>
</x-default-layout>
