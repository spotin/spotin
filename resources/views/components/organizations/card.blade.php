@props(['organization', 'role' => null])

{{-- Deleted, it still leads to its page, which only who can restore it reaches: to restore it from there --}}
<x-link-card
    :href="route('organizations.show', ['organization' => $organization->uuid])"
    icon="building-2"
    :title="$organization->name"
>
    {{-- Its numbers of spots and of members, when the list counted them, on one line --}}
    @php
        $counts = array_filter([
            isset($organization->spots_count) ? trans_choice('ui.organizations.common.spots', $organization->spots_count) : null,
            isset($organization->members_count) ? trans_choice('ui.organizations.common.members', $organization->members_count) : null,
        ]);
    @endphp

    @if ($counts)
        <p class="text-sm opacity-70">{{ implode(' · ', $counts) }}</p>
    @endif

    @if ($role)
        <p class="text-sm opacity-70">{{ __('ui.organizations.common.forms.fields.role.options.' . $role) }}</p>
    @endif

    @if ($organization->trashed())
        <div class="flex flex-wrap gap-1">
            <span class="badge badge-sm badge-error badge-outline gap-1">
                <x-lucide-trash-2 class="h-3 w-3" aria-hidden="true" />
                {{ __('ui.organizations.common.deleted') }}
            </span>
        </div>
    @endif
</x-link-card>
