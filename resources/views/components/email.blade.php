@props(['address'])

{{--
    A link to write to the address, with the user's email application. Allow a line break after "@" and dots, so a narrow
    column wraps between words
--}}
<a
    href="mailto:{{ $address }}"
    {{ $attributes->class('link link-hover') }}
>{!! str_replace(['@', '.'], ['<wbr>@', '.<wbr>'], e($address)) !!}</a>
