@props(['closable' => true])

{{--
    The message of an action that succeeded, for the request after it. Shown by `x-default-layout` above the page, or by
    a page that places it itself, next to what the action changed. Closable, unless it goes with something that stays,
    such as a new token.
--}}
@if (session('success'))
    <x-alert type="success" :closable="$closable">{{ session('success') }}</x-alert>
@endif
