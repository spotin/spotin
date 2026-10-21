@props(['id', 'latitude' => null, 'longitude' => null])

{{--
    Picks a point: on the map, drawn by resources/js/map.js, or by typing its coordinates, which also works without JS.
    The fields are named `latitude` and `longitude`.
--}}
<div id="{{ $id }}" data-map-picker class="flex flex-col gap-3">
    {{-- Hidden with its map, without JS or WebGL: the coordinates can still be typed --}}
    <fieldset class="fieldset has-[>[data-map][hidden]]:hidden">
        <legend class="fieldset-legend">{{ __('ui.common.map.label') }}</legend>
        <x-map mode="picker" class="h-96" />
        <span class="label">{{ __('ui.common.map.hint') }}</span>
    </fieldset>

    <div class="grid gap-3 sm:grid-cols-2">
        @foreach (['latitude' => [-90, 90, $latitude], 'longitude' => [-180, 180, $longitude]] as $field => [$min, $max, $value])
            <fieldset class="fieldset">
                <legend class="fieldset-legend">{{ __("ui.common.map.fields.{$field}.label") }}</legend>
                <input
                    name="{{ $field }}"
                    type="number"
                    step="0.000001"
                    min="{{ $min }}"
                    max="{{ $max }}"
                    class="input validator w-full"
                    required
                    placeholder="{{ __("ui.common.map.fields.{$field}.placeholder") }}"
                    value="{{ old($field, $value) }}"
                    @error($field) aria-invalid="true" @enderror
                />
                @error($field)
                    <span class="validator-hint mt-0">{{ $message }}</span>
                @enderror
            </fieldset>
        @endforeach
    </div>
</div>
