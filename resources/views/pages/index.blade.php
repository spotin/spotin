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

<x-default-layout>
    <x-slot:hero>
        {{-- All the spots across the page, with the welcome in a card on top of them --}}
        <x-map.hero :spots="route('spots.map')" :label="__('ui.homepage.map')" full aria-labelledby="page-title">
            <div class="card bg-base-100 pointer-events-auto w-full max-w-sm shadow-2xl">
                {{-- Its title and texts as the ones of the pages, by `prose`, which spaces them; its buttons as they are --}}
                <div class="card-body prose">
                    <h1 id="page-title">{{ __('ui.homepage.title') }}</h1>
                    <p class="opacity-80">{{ __('ui.homepage.introduction') }}</p>

                    <div class="not-prose flex flex-wrap gap-2">
                        <a href="{{ route('spots.index') }}" class="btn btn-primary">
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
</x-default-layout>
