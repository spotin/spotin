@props(['action', 'label', 'active' => false])

<form
    method="GET"
    action="{{ $action }}"
    role="search"
    aria-label="{{ $label }}"
    class="not-prose rounded-box border-base-content/5 bg-base-100 mb-4 flex flex-col gap-3 border p-3"
>
    <div class="join w-full">
        {{ $search }}

        <button type="submit" class="btn join-item">
            <x-lucide-search class="h-4 w-4" aria-hidden="true" />
            {{ __('ui.common.filters.search') }}
        </button>
    </div>

    @if (! $slot->isEmpty() || $active)
        <div class="flex flex-wrap items-end gap-x-4 gap-y-3">
            {{ $slot }}

            @if ($active)
                <a href="{{ $action }}" class="btn btn-ghost ms-auto"> {{ __('ui.common.filters.reset') }} </a>
            @endif
        </div>
    @endif
</form>
