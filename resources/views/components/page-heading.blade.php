@props(['level' => 1, 'id' => null, 'baseline' => false])

{{--
    A heading, styled by `prose` as any other, with an action on its right, such as a button: below it when narrow. Its
    margins are left out, as they would push it out of line with the action. The action is its `action` slot, the space
    below the row is given by its classes. `baseline` lines up the heading and a text link, rather than a button.
--}}
<div {{ $attributes->class(['flex flex-wrap justify-between gap-x-4 gap-y-2', 'items-baseline' => $baseline, 'items-center' => ! $baseline]) }}>
    <h{{ $level }} @if ($id) id="{{ $id }}" @endif class="m-0!">{{ $slot }}</h{{ $level }}>

    {{ $action ?? '' }}
</div>
