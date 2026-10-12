<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('admin.index');

render(function (View $view) {
    abort_unless(auth()->user()->isAdmin(), 403);

    Head::title(__('ui.admin.title'))
        ->description(__('ui.admin.description'));

    $breadcrumbs = [['label' => __('ui.admin.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.admin.title') }}</h1>

    <div class="not-prose grid gap-4 sm:grid-cols-2">
        <a
            href="{{ route('admin.organizations.index') }}"
            class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
        >
            <div class="card-body flex-row items-center gap-4">
                <x-lucide-building-complex class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
                <div class="min-w-0">
                    <h2 class="card-title">{{ __('ui.admin.organizations.index.title') }}</h2>
                    <p class="text-sm opacity-70">{{ __('ui.admin.organizations.index.description') }}</p>
                </div>
            </div>
        </a>
    </div>
</x-default-layout>
