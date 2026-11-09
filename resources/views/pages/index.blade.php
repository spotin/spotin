<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('index');

render(function (View $view) {
    Head::title(__('ui.homepage.title'))
        ->description(__('ui.homepage.description'));

    return $view;
});
?>

<x-default-layout wide manual-success-message>
    <x-slot:hero>
        {{-- All the spots across the page, with the welcome in a card on top of them --}}
        <x-map.hero :spots="route('spots.map')" :label="__('ui.homepage.map')" aria-labelledby="page-title">
            <div class="card bg-base-100 pointer-events-auto w-full max-w-sm shadow-2xl">
                {{-- Its title and texts as the ones of the pages, by `prose`, which spaces them; its buttons as they are --}}
                <div class="card-body prose">
                    {{-- Without the margin `prose` gives a page title, too much for a card --}}
                    <h1 id="page-title" class="mb-0!">{{ __('ui.homepage.title') }}</h1>
                    <p class="opacity-80">{{ __('ui.homepage.description') }}</p>

                    <div class="not-prose flex flex-wrap gap-2">
                        <a href="#contact" class="btn btn-primary">
                            <x-lucide-mail class="h-4 w-4" aria-hidden="true" />
                            {{ __('ui.homepage.contact') }}
                        </a>
                        <a href="{{ route('spots.index') }}" class="btn">
                            {{ __('ui.homepage.explore_spots') }}
                        </a>
                        <a href="{{ route('organizations.index') }}" class="btn">
                            {{ __('ui.homepage.explore_organizations') }}
                        </a>
                    </div>

                    <p class="text-sm">
                        @guest
                            <a href="{{ route('login') }}" class="link">{{ __('ui.auth.login.title') }}</a>
                            ·
                            <a href="{{ route('register') }}" class="link">{{ __('ui.auth.register.title') }}</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="link">{{ __('ui.homepage.dashboard') }}</a>
                        @endguest
                    </p>
                </div>
            </div>
        </x-map.hero>
    </x-slot:hero>

    <p class="lead text-center">{{ __('ui.homepage.audience') }}</p>

    <section aria-labelledby="homepage-advantages" class="mt-12">
        <h2 id="homepage-advantages" class="text-center">{{ __('ui.homepage.advantages.title') }}</h2>

        <div class="not-prose grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach (['lasting' => 'infinity', 'content' => 'pencil-line', 'governance' => 'shield-check'] as $advantage => $icon)
                <article class="card card-border bg-base-100">
                    <div class="card-body">
                        <x-dynamic-component :component="'lucide-'.$icon" class="text-primary h-8 w-8" aria-hidden="true" />
                        <h3 class="card-title">{{ __("ui.homepage.advantages.$advantage.title") }}</h3>
                        <p class="opacity-80">{{ __("ui.homepage.advantages.$advantage.text") }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section aria-labelledby="homepage-workflow" class="mt-12">
        <h2 id="homepage-workflow" class="text-center">{{ __('ui.homepage.workflow.title') }}</h2>

        {{-- Down the page, the steps on either side of it in turn; all on the same side on small screens, too narrow for two --}}
        <ol class="not-prose timeline timeline-vertical timeline-snap-icon max-md:timeline-compact mx-auto max-w-3xl">
            @foreach (['organization' => 'building-2', 'members' => 'user-plus', 'spots' => 'map-pin', 'print' => 'printer', 'evolve' => 'refresh-cw'] as $step => $icon)
                <li>
                    @unless ($loop->first)
                        <hr class="bg-primary" />
                    @endunless
                    <div class="timeline-middle bg-primary text-primary-content rounded-full p-2">
                        <x-dynamic-component :component="'lucide-'.$icon" class="h-5 w-5" aria-hidden="true" />
                    </div>
                    <div @class(['timeline-box mb-6', 'timeline-start md:text-end' => $loop->odd, 'timeline-end' => $loop->even])>
                        <h3 class="font-bold">{{ __("ui.homepage.workflow.$step.title") }}</h3>
                        <p class="text-sm opacity-80">{{ __("ui.homepage.workflow.$step.text") }}</p>
                    </div>
                    @unless ($loop->last)
                        <hr class="bg-primary" />
                    @endunless
                </li>
            @endforeach
        </ol>
    </section>

    <section aria-labelledby="homepage-features" class="mt-12">
        <h2 id="homepage-features" class="text-center">{{ __('ui.homepage.features.title') }}</h2>

        <ul class="not-prose grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (['pages' => 'map-pin', 'descriptions' => 'file-text', 'hidden' => 'eye-off', 'api' => 'code-xml', 'swiss_made' => 'mountain-snow', 'open_source' => 'scale'] as $feature => $icon)
                <li class="flex gap-3">
                    <x-dynamic-component :component="'lucide-'.$icon" class="text-primary mt-0.5 h-5 w-5 shrink-0" aria-hidden="true" />
                    <div>
                        <h3 class="font-bold">{{ __("ui.homepage.features.$feature.title") }}</h3>
                        <p class="text-sm opacity-80">{{ __("ui.homepage.features.$feature.text") }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    <section id="contact" aria-labelledby="homepage-contact" class="mx-auto mt-12 max-w-2xl scroll-mt-4">
        <h2 id="homepage-contact" class="text-center">{{ __('ui.contact.title') }}</h2>
        <p class="text-center">{{ __('ui.contact.description') }}</p>

        <x-success-message />

        <div class="not-prose card card-border bg-base-100">
            <form method="POST" action="{{ route('contact.store') }}" class="card-body">
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
        </div>
    </section>
</x-default-layout>
