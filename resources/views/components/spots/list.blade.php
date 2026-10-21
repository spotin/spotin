@props(['listing'])

<div class="mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
    <h1 class="m-0! min-w-0 break-words">{{ __('ui.spots.index.title') }}</h1>

    @if ($listing->canCreate)
        <a
            href="{{ route('organizations.spots.create', ['organization' => $listing->organization->uuid]) }}"
            class="btn btn-primary btn-sm shrink-0"
        >
            {{ __('ui.organizations.spots.create.title') }}
        </a>
    @endif
</div>

<x-filters :action="$listing->action" :label="__('ui.spots.index.filter.label')" :active="$listing->isFiltered()">
    <x-slot:search>
        <x-filters.search :placeholder="__('ui.spots.index.filter.search')" :value="$listing->searchQuery" />
    </x-slot:search>

    @auth
        <x-filters.checkbox name="mine" :label="__('ui.spots.index.filter.mine')" :checked="$listing->mine" />
    @endauth
</x-filters>

@if ($listing->spots->isEmpty())
    <p>{{ $listing->isFiltered() ? __('ui.spots.index.no_result') : __('ui.spots.index.empty') }}</p>
@else
    <p class="text-sm opacity-70">{{ trans_choice('ui.spots.index.count', $listing->spots->total()) }}</p>

    @if ($listing->map)
        {{-- All the results, while the list shows them page by page --}}
        <x-map :spots="$listing->map" :label="__('ui.spots.index.map')" class="mb-6 h-64 sm:h-80" />
    @endif

    <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach ($listing->spots as $spot)
            <x-spots.card :spot="$spot" />
        @endforeach
    </div>

    {{ $listing->spots->links() }}
@endif
