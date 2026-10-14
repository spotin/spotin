<?php

use App\Models\Organization;
use App\Models\Spot;
use App\Support\OrganizationBreadcrumbs;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('spots.index');

render(function (View $view) {
    $user = auth()->user();
    $mine = $user !== null && request()->boolean('mine');

    $searchQuery = request()->query('query');
    $searchQuery = is_string($searchQuery) ? mb_substr(trim($searchQuery), 0, 100) : '';

    $uuid = request()->query('organization');
    $organization = is_string($uuid) && $uuid !== '' ? Organization::where('uuid', $uuid)->firstOrFail() : null;

    $title = __('ui.spots.index.title');

    Head::title($title)->description(__('ui.spots.index.description'));

    return $view->with([
        'mine' => $mine,
        'searchQuery' => $searchQuery,
        'organization' => $organization,
        'active' => $mine || $searchQuery !== '' || $organization !== null,
        'title' => $title,
        'breadcrumbs' => $organization ? [...OrganizationBreadcrumbs::for($organization), ['label' => __('ui.spots.index.title')]] : [],
        'canCreate' => $user?->spotOrganizations()->exists() ?? false,
        'spots' => Spot::with('organization')
            ->search($searchQuery)
            ->when($organization, fn ($query) => $query->whereBelongsTo($organization))
            ->when($mine, fn ($query) => $query->mine($user))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString(),
    ]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
        <h1 class="m-0! min-w-0 break-words">{{ $title }}</h1>

        @if ($canCreate)
            <a
                href="{{ route('spots.create', $organization ? ['organization' => $organization->uuid] : []) }}"
                class="btn btn-primary btn-sm shrink-0"
            >
                {{ __('ui.spots.index.create') }}
            </a>
        @endif
    </div>

    @if (session('status') === 'spot-deleted')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.spots.feedback.spot-deleted') }}</span>
        </p>
    @endif

    <x-filters :action="route('spots.index')" :label="__('ui.spots.index.filter.label')" :active="$active">
        <x-slot:search>
            <x-filters.search :placeholder="__('ui.spots.index.filter.search')" :value="$searchQuery" />
        </x-slot:search>

        @auth
            <x-filters.checkbox name="mine" :label="__('ui.spots.index.filter.mine')" :checked="$mine" />
        @endauth
    </x-filters>

    @if ($spots->isEmpty())
        <p>{{ $active ? __('ui.spots.index.no-result') : __('ui.spots.index.empty') }}</p>
    @else
        <p class="text-sm opacity-70">{{ trans_choice('ui.spots.index.count', $spots->total()) }}</p>

        <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($spots as $spot)
                <x-spots.card :spot="$spot" />
            @endforeach
        </div>

        {{ $spots->links() }}
    @endif
</x-default-layout>
