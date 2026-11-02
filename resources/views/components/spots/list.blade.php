@props(['listing', 'nested' => false])

{{--
    A list of spots, with its filters and its pages, and the creation of a spot, in its organization when it is the one
    of an organization. `nested` puts it in the page of its organization, under its name: a section of the page.
--}}
<x-page-heading :level="$nested ? 2 : 1" class="mb-6">
    {{ __('ui.spots.index.title') }}

    @if ($listing->canCreate)
        <x-slot:action>
            <a
                href="{{ route('spots.create', $listing->organization ? ['organization' => $listing->organization->uuid] : []) }}"
                class="btn btn-primary btn-sm shrink-0"
            >
                {{ __('ui.spots.create.title') }}
            </a>
        </x-slot:action>
    @endif
</x-page-heading>

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

    <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach ($listing->spots as $spot)
            <x-spots.card :spot="$spot" />
        @endforeach
    </div>

    {{ $listing->spots->links() }}
@endif
