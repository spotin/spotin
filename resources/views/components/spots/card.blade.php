@props(['spot'])

<x-link-card :href="route('spots.show', ['spot' => $spot->uuid])" icon="map-pin" :title="$spot->displayName()">
    <p class="text-sm opacity-70">{{ $spot->organization->name }}</p>

    @if ($spot->needs_setup || $spot->is_hidden)
        <div class="flex flex-wrap gap-1">
            @if ($spot->needs_setup)
                <span class="badge badge-sm badge-outline border-base-content/30 gap-1">
                    <x-lucide-wrench class="h-3 w-3" aria-hidden="true" />
                    {{ __('ui.spots.common.needs_setup') }}
                </span>
            @endif

            @if ($spot->is_hidden)
                <span class="badge badge-sm badge-outline border-base-content/30 gap-1">
                    <x-lucide-eye-off class="h-3 w-3" aria-hidden="true" />
                    {{ __('ui.spots.common.hidden') }}
                </span>
            @endif
        </div>
    @endif
</x-link-card>
