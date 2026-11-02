<?php

use App\Support\Breadcrumbs;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth']);
name('settings.profile');

render(function (View $view) {
    Head::title(__('ui.settings.profile.title'))
        ->description(__('ui.settings.profile.description'));

    $breadcrumbs = [...Breadcrumbs::settings(), ['label' => __('ui.settings.profile.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.settings.profile.title') }}</h1>

    @if (session('status') === 'profile-information-updated')
        <x-alert type="success" closable>{{ __('ui.settings.profile.feedback.profile_information_updated') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('user-profile-information.update') }}">
        @csrf
        @method('PUT')

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.name.label') }}</legend>
            <input
                name="name"
                type="text"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.name.placeholder') }}"
                value="{{ old('name', auth()->user()->name) }}"
                @error('name', 'updateProfileInformation') aria-invalid="true" @enderror
            />
            @error('name', 'updateProfileInformation')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.email.label') }}</legend>
            <input
                name="email"
                type="text"
                inputmode="email"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.auth.common.forms.fields.email.placeholder') }}"
                value="{{ old('email', auth()->user()->email) }}"
                @error('email', 'updateProfileInformation') aria-invalid="true" @enderror
            />
            @error('email', 'updateProfileInformation')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <footer class="mt-4">
            <button type="submit" class="btn btn-block">{{ __('ui.settings.profile.form.actions.submit') }}</button>
        </footer>
    </form>
</x-default-layout>
