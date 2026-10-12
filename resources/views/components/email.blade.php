@props(['address'])

{{-- Allow a line break after "@" and dots, so a narrow column wraps between words --}}
<span {{ $attributes }}>{!! str_replace(['@', '.'], ['<wbr>@', '.<wbr>'], e($address)) !!}</span>
