<?php

use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;

middleware(['auth']);
name('settings.security');

Head::title(__('ui.settings.security.title'))
    ->description(__('ui.settings.security.description'));
?>

<x-default-layout>
    <div class="prose max-w-none">
        <h1>{{ __('ui.settings.security.title') }}</h1>

        @if (session('status') === 'password-updated')
            <p role="alert" class="alert alert-success">
                <x-lucide-check-circle class="h-6 w-6" />
                <span>{{ __('ui.settings.security.form.feedback.success') }}</span>
            </p>
        @endif

        <form method="POST" action="{{ route('user-password.update') }}">
            @csrf
            @method('PUT')

            <fieldset class="fieldset mb-4 gap-3">
                <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

                <div>
                    <label class="floating-label input validator w-full">
                        <span>{{ __('ui.settings.security.form.fields.current_password.placeholder') }}</span>
                        <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                        <input
                            name="current_password"
                            type="password"
                            required
                            placeholder="{{ __('ui.settings.security.form.fields.current_password.label') }}"
                            @error('current_password', 'updatePassword') aria-invalid="true" @enderror
                            class="input w-full"
                        />
                    </label>
                    @error('current_password', 'updatePassword')
                        <span class="validator-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="floating-label input validator w-full">
                        <span>{{ __('ui.settings.security.form.fields.new_password.placeholder') }}</span>
                        <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                        <input
                            name="password"
                            type="password"
                            required
                            placeholder="{{ __('ui.settings.security.form.fields.new_password.label') }}"
                            @error('password', 'updatePassword') aria-invalid="true" @enderror
                            class="input w-full"
                        />
                    </label>
                    @error('password', 'updatePassword')
                        <span class="validator-hint">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="floating-label input validator w-full">
                        <span>{{ __('ui.settings.security.form.fields.confirm_password.placeholder') }}</span>
                        <x-lucide-lock-keyhole-open class="h-[1em] opacity-50" />
                        <input
                            name="password_confirmation"
                            type="password"
                            required
                            placeholder="{{ __('ui.settings.security.form.fields.confirm_password.label') }}"
                            @error('password', 'updatePassword') aria-invalid="true" @enderror
                            class="input w-full"
                        />
                    </label>
                    @error('password', 'updatePassword')
                        <span class="validator-hint">{{ $message }}</span>
                    @enderror
                </div>
            </fieldset>

            <footer>
                <button type="submit" class="btn btn-block">
                    {{ __('ui.settings.security.form.actions.submit') }}
                </button>
            </footer>
        </form>
    </div>
</x-default-layout>
