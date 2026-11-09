@props(['organization', 'role' => null])

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
</x-link-card>
