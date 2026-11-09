<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('contact');

render(function (View $view) {
    Head::title(__('ui.contact.title'))
        ->description(__('ui.contact.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.contact.title') }}</h1>
    <p>{{ __('ui.contact.description') }}</p>

    <form method="POST" action="{{ route('contact.store') }}">
        @csrf

        @foreach (['name' => 'text', 'email' => 'email', 'organization' => 'text'] as $field => $type)
            <fieldset class="fieldset">
                <legend class="fieldset-legend">{{ __("ui.contact.fields.$field.label") }}</legend>
                <input
                    name="{{ $field }}"
                    type="{{ $type }}"
                    class="input validator w-full"
                    required
                    maxlength="255"
                    autocomplete="{{ $field }}"
                    placeholder="{{ __("ui.contact.fields.$field.placeholder") }}"
                    value="{{ old($field) }}"
                    @error($field) aria-invalid="true" @enderror
                />
                @error($field)
                    <span class="validator-hint mt-0">{{ $message }}</span>
                @enderror
            </fieldset>
        @endforeach

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.contact.fields.message.label') }}</legend>
            <textarea
                name="message"
                class="textarea validator h-32 w-full"
                required
                maxlength="5000"
                placeholder="{{ __('ui.contact.fields.message.placeholder') }}"
                @error('message') aria-invalid="true" @enderror
            >{{ old('message') }}</textarea>
            @error('message')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        {{-- The honeypot: out of sight and out of the tab order, so only the bots that fill every field fill it --}}
        <div class="hidden" aria-hidden="true">
            <label>
                {{ __('ui.contact.fields.website.label') }}
                <input name="website" type="text" tabindex="-1" autocomplete="off" />
            </label>
        </div>

        <footer class="mt-4">
            <button type="submit" class="btn btn-primary btn-block">
                <x-lucide-send class="h-4 w-4" aria-hidden="true" />
                {{ __('ui.contact.actions.submit') }}
            </button>
        </footer>
    </form>
</x-default-layout>
