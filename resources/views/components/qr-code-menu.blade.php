@props(['svg', 'png', 'scan', 'hint'])

{{--
    The QR code to print, and the address it leads to, to try and share: a button and its menu, which need no JS. One per
    page: `svg` and `png` are the addresses of its downloads, `scan` the one it holds, `hint` what opening it does.
--}}
<button type="button" class="btn btn-sm" popovertarget="qr-code-popover" style="anchor-name: --qr-code-anchor">
    <x-lucide-qr-code class="h-4 w-4" aria-hidden="true" />
    {{ __('ui.common.qr_code.label') }}
</button>
<div
    popover
    id="qr-code-popover"
    style="position-anchor: --qr-code-anchor"
    class="dropdown dropdown-end bg-base-100 rounded-box w-72 p-2 shadow-sm"
>
    <ul class="menu w-full p-0">
        @foreach (['svg' => $svg, 'png' => $png] as $format => $url)
            <li>
                <a href="{{ $url }}" download>
                    <x-lucide-download class="h-4 w-4" aria-hidden="true" />
                    {{ __("ui.common.qr_code.download_{$format}") }}
                </a>
            </li>
        @endforeach
        <li>
            <a href="{{ $scan }}" target="_blank">
                <x-lucide-external-link class="h-4 w-4" aria-hidden="true" />
                {{ __('ui.common.qr_code.open') }}
            </a>
        </li>
    </ul>

    {{-- The link can be selected, and copied with the button, shown by resources/js/app.js --}}
    <div class="flex flex-col gap-1 px-3 pt-2 pb-1">
        <label for="scan-link" class="text-xs opacity-70">{{ __('ui.common.qr_code.scan_link') }}</label>
        <div class="join w-full">
            <input id="scan-link" type="text" readonly class="input input-sm join-item w-full" value="{{ $scan }}" />
            <button
                type="button"
                class="btn btn-sm join-item"
                hidden
                data-copy="scan-link"
                data-copied="{{ __('ui.common.qr_code.copied') }}"
            >
                <x-lucide-copy class="h-4 w-4" aria-hidden="true" />
                <span>{{ __('ui.common.qr_code.copy') }}</span>
            </button>
        </div>
        <span class="text-xs opacity-70">{{ $hint }}</span>
    </div>
</div>
