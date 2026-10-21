@props(['title'])

{{--
    Says the page was just scanned: shown once after its QR code address (`/redirect`), which sets `scanned` for the next
    request.
--}}
@if (session('scanned'))
    <x-alert type="info" icon="scan-qr-code" role="status" closable class="not-prose mb-6">
        <p class="font-semibold">{{ $title }}</p>
        <p>{{ __('ui.common.scanned', ['app' => config('app.name')]) }}</p>
    </x-alert>
@endif
