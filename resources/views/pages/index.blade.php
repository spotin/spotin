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

<x-default-layout wide>
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
                        <a href="{{ route('contact') }}" class="btn btn-primary"> {{ __('ui.homepage.contact') }} </a>
                    </div>

                    @auth
                        <p class="text-sm">
                            <a href="{{ route('dashboard') }}" class="link">{{ __('ui.homepage.dashboard') }}</a>
                        </p>
                    @endauth
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
                        <x-dynamic-component
                            :component="'lucide-'.$icon"
                            class="text-primary h-8 w-8"
                            aria-hidden="true"
                        />
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
                    <x-dynamic-component
                        :component="'lucide-'.$icon"
                        class="text-primary mt-0.5 h-5 w-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div>
                        <h3 class="font-bold">{{ __("ui.homepage.features.$feature.title") }}</h3>
                        <p class="text-sm opacity-80">{{ __("ui.homepage.features.$feature.text") }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    <section aria-labelledby="homepage-call-to-action" class="mt-12 text-center">
        <h2 id="homepage-call-to-action">{{ __('ui.homepage.call_to_action.title') }}</h2>
        <p>{{ __('ui.homepage.call_to_action.text') }}</p>

        <a href="{{ route('contact') }}" class="not-prose btn btn-primary">
            <x-lucide-mail class="h-4 w-4" aria-hidden="true" />
            {{ __('ui.homepage.contact') }}
        </a>
    </section>
</x-default-layout>
