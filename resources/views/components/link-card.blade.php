@props(['href', 'icon', 'title', 'variant' => 'item', 'level' => 2])

{{--
    A card that is a single link: its icon in a circle, its title with what the slot adds under it, and an arrow. The
    `action` variant puts the icon in the primary color, for what the user can do; the `item` one, neutral, for the cards
    of the lists, many on a page. `level` is the one of its heading, under the heading of the page or of its section.
--}}
<a
    href="{{ $href }}"
    {{ $attributes->class('card card-border card-side bg-base-100 hover:bg-base-200 focus-visible:outline-primary items-center transition-colors focus-visible:outline-2') }}
>
    <div
        @class([
            'ml-4 shrink-0 rounded-full p-3',
            'bg-primary text-primary-content' => $variant === 'action',
            'bg-base-200' => $variant === 'item',
        ])
    >
        <x-dynamic-component :component="'lucide-'.$icon" class="h-6 w-6" aria-hidden="true" />
    </div>
    <div class="card-body min-w-0 p-4">
        <h{{ $level }} class="card-title text-base">
            <span class="line-clamp-2 break-words" title="{{ $title }}">{{ $title }}</span>
        </h{{ $level }}>
        {{ $slot }}
    </div>
    <x-lucide-arrow-right class="mr-4 h-5 w-5 shrink-0 opacity-50" aria-hidden="true" />
</a>
