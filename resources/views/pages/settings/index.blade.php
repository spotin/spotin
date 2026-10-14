<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth']);
name('settings.index');

render(function (View $view) {
    Head::title(__('ui.settings.title'))
        ->description(__('ui.settings.description'));

    return $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.settings.title') }}</h1>

    <div class="not-prose grid gap-4 sm:grid-cols-2">
        <a
            href="{{ route('settings.profile') }}"
            class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
        >
            <div class="card-body flex-row items-center gap-4">
                <x-lucide-id-card class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
                <div class="min-w-0">
                    <h2 class="card-title">{{ __('ui.settings.profile.title') }}</h2>
                    <p class="text-sm opacity-70">{{ __('ui.settings.profile.description') }}</p>
                </div>
            </div>
        </a>
        <a
            href="{{ route('settings.security') }}"
            class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
        >
            <div class="card-body flex-row items-center gap-4">
                <x-lucide-lock-keyhole class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
                <div class="min-w-0">
                    <h2 class="card-title">{{ __('ui.settings.security.title') }}</h2>
                    <p class="text-sm opacity-70">{{ __('ui.settings.security.description') }}</p>
                </div>
            </div>
        </a>
        <a
            href="{{ route('settings.developer') }}"
            class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
        >
            <div class="card-body flex-row items-center gap-4">
                <x-lucide-terminal class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
                <div class="min-w-0">
                    <h2 class="card-title">{{ __('ui.settings.developer.index.title') }}</h2>
                    <p class="text-sm opacity-70">{{ __('ui.settings.developer.index.description') }}</p>
                </div>
            </div>
        </a>
    </div>
</x-default-layout>
