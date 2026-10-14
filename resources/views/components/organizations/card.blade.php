@props(['organization', 'role' => null])

<a
    href="{{ route('organizations.show', ['organization' => $organization->uuid]) }}"
    class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
>
    <div class="card-body flex-row items-center gap-4">
        <x-lucide-user-group class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
        <div class="min-w-0">
            <h2 class="card-title">
                <span class="line-clamp-2 break-words" title="{{ $organization->name }}">
                    {{ $organization->name }}
                </span>
            </h2>
            @isset($organization->spots_count)
                <p class="text-sm opacity-70">
                    {{ trans_choice('ui.organizations.show.sections.spots.summary', $organization->spots_count) }}
                </p>
            @endisset

            @if ($role)
                <p class="text-sm opacity-70">{{ __('ui.organizations.common.forms.fields.role.options.' . $role) }}</p>
            @endif
        </div>
    </div>
</a>
