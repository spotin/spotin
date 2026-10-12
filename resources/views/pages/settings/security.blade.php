<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth']);
name('settings.security');

render(function (View $view) {
    Head::title(__('ui.settings.security.title'))
        ->description(__('ui.settings.security.description'));

    $breadcrumbs = [['label' => __('ui.settings.title'), 'url' => route('settings.index')], ['label' => __('ui.settings.security.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.settings.security.title') }}</h1>

    @if (session('status') === 'password-updated')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.settings.feedback.password-updated') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('user-password.update') }}">
        @csrf
        @method('PUT')

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill-the-form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.auth.common.forms.fields.current-password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="current_password"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.current-password.label') }}"
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
                    <span>{{ __('ui.auth.common.forms.fields.new-password.placeholder') }}</span>
                    <x-lucide-lock-keyhole class="h-[1em] opacity-50" />
                    <input
                        name="password"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.new-password.label') }}"
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
                    <span>{{ __('ui.auth.common.forms.fields.new-password-confirmation.placeholder') }}</span>
                    <x-lucide-lock-keyhole-open class="h-[1em] opacity-50" />
                    <input
                        name="password_confirmation"
                        type="password"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.new-password-confirmation.label') }}"
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
            <button type="submit" class="btn btn-block">{{ __('ui.settings.security.form.actions.submit') }}</button>
        </footer>
    </form>
</x-default-layout>
