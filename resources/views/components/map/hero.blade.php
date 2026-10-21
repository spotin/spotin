@props(['spots', 'label', 'full' => false])

{{--
    A map of spots across the width of the page, for the `hero` slot of `x-default-layout`. `full` fills the height of the
    page; otherwise it leaves the content of the page in view below it. What it holds, such as the welcome card of the
    home page, is put on top of the map: the hero stacks them in the same place, and lets the clicks through to the map,
    except on what it holds.
--}}
<section {{ $attributes->class(['hero bg-base-200', 'min-h-[28rem] flex-grow' => $full, 'h-[55vh] min-h-80' => ! $full]) }}>
    <x-map :spots="$spots" :label="$label" :framed="false" class="h-full" />

    @if ($slot->hasActualContent())
        <div class="hero-content pointer-events-none w-full max-w-6xl justify-start">{{ $slot }}</div>
    @endif
</section>
