@props(['listing'])

<div class="mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
    <h1 class="m-0! min-w-0 break-words">{{ __('ui.organizations.index.title') }}</h1>

    @if ($listing->canCreate)
        <a href="{{ route('organizations.create') }}" class="btn btn-primary btn-sm shrink-0">
            {{ __('ui.organizations.index.create') }}
        </a>
    @endif
</div>

<x-filters
    :action="$listing->action"
    :label="__('ui.organizations.index.filter.label')"
    :active="$listing->isFiltered()"
>
    <x-slot:search>
        <x-filters.search :placeholder="__('ui.organizations.index.filter.search')" :value="$listing->searchQuery" />
    </x-slot:search>

    @auth
        <x-filters.checkbox name="mine" :label="__('ui.organizations.index.filter.mine')" :checked="$listing->mine" />
    @endauth
</x-filters>

@if ($listing->organizations->isEmpty())
    <p>{{ $listing->isFiltered() ? __('ui.organizations.index.no_result') : __('ui.organizations.index.empty') }}</p>
@else
    <p class="text-sm opacity-70">
        {{ trans_choice('ui.organizations.index.count', $listing->organizations->total()) }}
    </p>

    <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
        @foreach ($listing->organizations as $organization)
            <x-organizations.card :organization="$organization" :role="$listing->roles[$organization->id] ?? null" />
        @endforeach
    </div>

    {{ $listing->organizations->links() }}
@endif
