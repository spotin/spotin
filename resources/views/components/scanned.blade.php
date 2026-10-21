@props(['title'])

{{--
    Says the page was just scanned: shown once after its QR code address (`/redirect`), which sets `scanned` for the next
    request.
--}}
@if (session('scanned'))
    <div role="status" class="alert alert-info not-prose mb-6">
        <x-lucide-scan-qr-code class="h-6 w-6 shrink-0" aria-hidden="true" />
        <div>
            <p class="font-semibold">{{ $title }}</p>
            <p>{{ __('ui.common.scanned', ['app' => config('app.name')]) }}</p>
        </div>
    </div>
@endif
