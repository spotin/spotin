<?php

use App\Models\Invitation;
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

    return $view->with('invitation', Invitation::findInSession()?->load('organization'));
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.register.title') }}</h1>

    @if ($invitation)
        <p role="alert" class="alert alert-info">
            <x-lucide-mail class="h-6 w-6" />
            <span>{{ __('ui.auth.register.invitation', ['organization' => $invitation->organization->name]) }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <x-steps>
            <x-steps.step :title="__('ui.auth.register.steps.information.title')">
                <p>{{ __('ui.auth.register.steps.information.audience') }}</p>
                <p>{{ __('ui.auth.register.steps.information.membership') }}</p>
                <p>{{ __('ui.auth.register.steps.information.discover') }}</p>
            </x-steps.step>

            <x-steps.step :title="__('ui.auth.register.steps.account.title')">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.name.label') }}</legend>
                    <input
                        name="name"
                        type="text"
                        class="input validator w-full"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.name.placeholder') }}"
                        value="{{ old('name') }}"
                        @error('name') aria-invalid="true" @enderror
                    />
                    @error('name')
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
                        value="{{ old('email', $invitation?->email) }}"
                        @readonly($invitation)
                        @error('email') aria-invalid="true" @enderror
                    />
                    @error('email')
                        <span class="validator-hint mt-0">{{ $message }}</span>
                    @enderror
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('ui.auth.common.forms.fields.password.label') }}</legend>
                    <input
                        name="password"
                        type="password"
                        class="input validator w-full"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.password.placeholder') }}"
                        @error('password') aria-invalid="true" @enderror
                    />
                    @error('password')
                        <span class="validator-hint mt-0">{{ $message }}</span>
                    @enderror
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        {{ __('ui.auth.common.forms.fields.password_confirmation.label') }}
                    </legend>
                    <input
                        name="password_confirmation"
                        type="password"
                        class="input validator w-full"
                        required
                        placeholder="{{ __('ui.auth.common.forms.fields.password_confirmation.placeholder') }}"
                        @error('password') aria-invalid="true" @enderror
                    />
                    @error('password')
                        <span class="validator-hint mt-0">{{ $message }}</span>
                    @enderror
                </fieldset>

                <x-slot:actions>
                    <button type="submit" class="btn btn-primary">
                        {{ __('ui.auth.register.form.actions.submit') }}
                    </button>
                </x-slot:actions>
            </x-steps.step>
        </x-steps>
    </form>

    <p class="text-center text-sm">
        {{ __('ui.auth.register.already_have_an_account') }}
        <a href="{{ route('login') }}"> {{ __('ui.auth.register.login') }} </a>
    </p>
</x-default-layout>
