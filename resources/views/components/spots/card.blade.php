@props(['spot'])

<a
    href="{{ route('spots.show', ['spot' => $spot->uuid]) }}"
    class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
>
    <div class="card-body flex-row items-center gap-4">
        <x-lucide-map-pin class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
        <div class="min-w-0">
            <h2 class="card-title">
                <span class="line-clamp-2 break-words" title="{{ $spot->displayName() }}">
                    {{ $spot->displayName() }}
                </span>
            </h2>
            <p class="text-sm opacity-70">{{ $spot->organization->name }}</p>

            @if ($spot->needs_setup)
                <span class="badge badge-sm badge-outline border-base-content/30 mt-1 gap-1">
                    <x-lucide-wrench class="h-3 w-3" aria-hidden="true" />
                    {{ __('ui.spots.common.needs_setup') }}
                </span>
            @endif
            @if ($spot->is_hidden)
                <span class="badge badge-sm badge-outline border-base-content/30 mt-1 gap-1">
                    <x-lucide-eye-off class="h-3 w-3" aria-hidden="true" />
                    {{ __('ui.spots.common.hidden') }}
                </span>
            @endif
        </div>
    </div>
</a>
