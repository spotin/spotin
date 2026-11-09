@props(['listing'])

<x-page-heading class="mb-6">
    {{ __('ui.organizations.index.title') }}

    @if ($listing->canCreate)
        <x-slot:action>
            <a href="{{ route('organizations.create') }}" class="btn btn-primary btn-sm shrink-0">
                {{ __('ui.organizations.index.create') }}
            </a>
        </x-slot:action>
    @endif
</x-page-heading>

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

        {{-- The deleted organizations, to restore them: for the administrators only, as they delete them --}}
        @if ($listing->canRestore)
            <x-filters.checkbox
                name="deleted"
                :label="__('ui.organizations.index.filter.deleted')"
                :checked="$listing->deleted"
            />
        @endif
    @endauth
</x-filters>

@error('restore')
    <x-alert type="error" closable>{{ $message }}</x-alert>
@enderror

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
