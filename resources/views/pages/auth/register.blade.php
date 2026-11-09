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

    {{-- Accounts are only created to join the organization of an invitation, followed from its link --}}
    @if ($invitation)
        <x-alert
            type="info"
            icon="mail"
        >{{ __('ui.auth.register.invitation', ['organization' => $invitation->organization->name]) }}</x-alert>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

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
                    readonly
                    value="{{ $invitation->email }}"
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

            <button type="submit" class="btn btn-primary btn-block mt-4">
                {{ __('ui.auth.register.form.actions.submit') }}
            </button>
        </form>
    @else
        @error('email')
            <x-alert type="error">{{ $message }}</x-alert>
        @enderror

        <h2>{{ __('ui.auth.register.invitation_only.title') }}</h2>
        <p>{{ __('ui.auth.register.invitation_only.text') }}</p>
        <p><a href="{{ route('contact') }}">{{ __('ui.auth.register.invitation_only.contact') }}</a></p>
    @endif

    <p class="text-center text-sm">
        {{ __('ui.auth.register.already_have_an_account') }}
        <a href="{{ route('login') }}"> {{ __('ui.auth.register.login') }} </a>
    </p>
</x-default-layout>
