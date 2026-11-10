{{--
    What a deleted spot or organization shows, by its QR code or by its page, to who cannot restore it: that it was
    removed, rather than a page not found. `title` and `text` say which one, and how it was reached.
--}}
@php
    Laravel\Head\Facades\Head::title($title);
@endphp

<x-default-layout>
    <h1>{{ $title }}</h1>

    <p>{{ $text }}</p>

    <p><a href="{{ route('index') }}" class="link">{{ __('ui.common.removed.home') }}</a></p>
</x-default-layout>
