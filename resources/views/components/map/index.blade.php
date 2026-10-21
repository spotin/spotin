@props(['mode' => 'display', 'spots' => null, 'label' => null, 'framed' => true])

{{--
    A map drawn by resources/js/map.js, hidden without JS. `display` shows `spots`: the address of their GeoJSON, or the
    GeoJSON itself (`Spot::featureCollection()`). `picker` is used by `x-map.picker`, which gives its point. `framed` gives
    it a border and rounded corners, unless it is inside something that has them.
--}}
@php($locale = [
    'Map.Title' => $label ?? __('ui.common.map.label'),
    'Marker.Title' => __('ui.common.map.controls.marker'),
    'NavigationControl.ZoomIn' => __('ui.common.map.controls.zoom_in'),
    'NavigationControl.ZoomOut' => __('ui.common.map.controls.zoom_out'),
    'NavigationControl.ResetBearing' => __('ui.common.map.controls.reset_bearing'),
    'GeolocateControl.FindMyLocation' => __('ui.common.map.controls.locate'),
    'GeolocateControl.LocationNotAvailable' => __('ui.common.map.controls.locate_unavailable'),
    'FullscreenControl.Enter' => __('ui.common.map.controls.fullscreen_enter'),
    'FullscreenControl.Exit' => __('ui.common.map.controls.fullscreen_exit'),
    'AttributionControl.ToggleAttribution' => __('ui.common.map.controls.attribution'),
    'CooperativeGesturesHandler.WindowsHelpText' => __('ui.common.map.controls.scroll_windows'),
    'CooperativeGesturesHandler.MacHelpText' => __('ui.common.map.controls.scroll_mac'),
    'CooperativeGesturesHandler.MobileHelpText' => __('ui.common.map.controls.two_fingers'),
])

<div
    hidden
    data-map="{{ $mode }}"
    data-map-style="{{ config('services.map.style') }}"
    data-map-center="{{ json_encode([config('services.map.center.longitude'), config('services.map.center.latitude')]) }}"
    data-map-zoom="{{ config('services.map.zoom') }}"
    data-map-locale="{{ json_encode($locale) }}"
    @if ($spots !== null)
        data-map-spots="{{ is_string($spots) ? $spots : json_encode($spots) }}"
        data-map-spot-link="{{ __('ui.common.map.spot_link') }}"
        data-map-recenter="{{ __('ui.common.map.controls.recenter_spots') }}"
    @else
        data-map-recenter="{{ __('ui.common.map.controls.recenter') }}"
    @endif
    role="region"
    aria-label="{{ $label ?? __('ui.common.map.label') }}"
    {{ $attributes->class(['not-prose w-full overflow-hidden', 'rounded-box border-base-content/10 border' => $framed]) }}
>
    {{-- The icon of the button that goes back to the point or the spots, copied by resources/js/map.js --}}
    <template data-map-recenter-icon>
        <x-lucide-crosshair class="mx-auto h-4 w-4 stroke-3" aria-hidden="true" />
    </template>
</div>
