<?php

use App\Support\Breadcrumbs;
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

    $breadcrumbs = [...Breadcrumbs::settings(), ['label' => __('ui.settings.security.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.settings.security.title') }}</h1>

    @if (session('status') === 'password-updated')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.settings.security.feedback.password_updated') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('user-password.update') }}">
        @csrf
        @method('PUT')

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.current_password.label') }}</legend>
            <input
                name="current_password"
                type="password"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.current_password.placeholder') }}"
                @error('current_password', 'updatePassword') aria-invalid="true" @enderror
            />
            @error('current_password', 'updatePassword')
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
                @error('password', 'updatePassword') aria-invalid="true" @enderror
            />
            @error('password', 'updatePassword')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">
                {{ __('ui.auth.common.forms.fields.new_password_confirmation.label') }}
            </legend>
            <input
                name="password_confirmation"
                type="password"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.new_password_confirmation.placeholder') }}"
                @error('password', 'updatePassword') aria-invalid="true" @enderror
            />
            @error('password', 'updatePassword')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <footer class="mt-4">
            <button type="submit" class="btn btn-block">{{ __('ui.settings.security.form.actions.submit') }}</button>
        </footer>
    </form>
</x-default-layout>
