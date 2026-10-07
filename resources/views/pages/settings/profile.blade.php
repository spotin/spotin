<?php

use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;

name('settings.profile');

Head::title(__('ui.settings.profile.title'))
    ->description(__('ui.settings.profile.description'));
?>

<x-default-layout>
    <h1>{{ __('ui.settings.profile.title') }}</h1>

    @if (session('status'))
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ session('status') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('user-profile-information.update') }}">
        @csrf
        @method('PUT')

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.settings.profile.form.fields.username.placeholder') }}</span>
                    <x-lucide-user-round class="h-[1em] opacity-50" />
                    <input
                        name="username"
                        type="text"
                        required
                        placeholder="{{ __('ui.settings.profile.form.fields.username.label') }}"
                        value="{{ old('username', auth()->user()->username) }}"
                        @error('username', 'updateProfileInformation') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('username', 'updateProfileInformation')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.settings.profile.form.fields.name.placeholder') }}</span>
                    <x-lucide-id-card class="h-[1em] opacity-50" />
                    <input
                        name="name"
                        type="text"
                        required
                        placeholder="{{ __('ui.settings.profile.form.fields.name.label') }}"
                        value="{{ old('name', auth()->user()->name) }}"
                        @error('name', 'updateProfileInformation') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('name', 'updateProfileInformation')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.settings.profile.form.fields.email.placeholder') }}</span>
                    <x-lucide-mail class="h-[1em] opacity-50" />
                    <input
                        name="email"
                        type="email"
                        required
                        placeholder="{{ __('ui.settings.profile.form.fields.email.label') }}"
                        value="{{ old('email', auth()->user()->email) }}"
                        @error('email', 'updateProfileInformation') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('email', 'updateProfileInformation')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block">{{ __('ui.settings.profile.form.actions.submit') }}</button>
        </footer>
    </form>
</x-default-layout>
