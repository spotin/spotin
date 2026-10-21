@props(['type' => 'info', 'icon' => null, 'role' => 'alert', 'actions' => null, 'closable' => false])

@php
    // Written in full, so that Tailwind finds the classes of daisyUI
    $classes = ['info' => 'alert-info', 'success' => 'alert-success', 'error' => 'alert-error'];
    $icons = ['info' => 'info', 'success' => 'check-circle', 'error' => 'circle-alert'];
@endphp

{{--
    An alert of its type (`info`, `success` or `error`), with its icon, its text and its `actions`, if any. `closable`
    gives it a button that closes it, for a message shown once after an action: shown and handled by resources/js/app.js,
    as it needs JS. The notes that stay on a page are not closable.
--}}
{{-- Closable, it makes room on its right for its button, round in its corner, as the one of a dialog --}}
<div role="{{ $role }}" data-alert {{ $attributes->class(['alert', $classes[$type], 'relative pr-12' => $closable]) }}>
    <x-dynamic-component :component="'lucide-'.($icon ?? $icons[$type])" class="h-6 w-6 shrink-0" aria-hidden="true" />
    <div>{{ $slot }}</div>

    @if ($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endif

    @if ($closable)
        <button
            type="button"
            class="btn btn-sm btn-circle btn-ghost absolute top-2 right-2"
            aria-label="{{ __('ui.common.actions.close') }}"
            title="{{ __('ui.common.actions.close') }}"
            data-alert-close
            hidden
        >
            <x-lucide-x class="h-4 w-4" aria-hidden="true" />
        </button>
    @endif
</div>
