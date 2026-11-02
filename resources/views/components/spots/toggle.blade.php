@props(['field', 'spot' => null])

{{-- An option of the spot form, as a checkbox with its hint. The hidden input sends it unchecked, which browsers leave out. --}}
<fieldset class="fieldset">
    <legend class="fieldset-legend">{{ __("ui.spots.common.forms.fields.{$field}.label") }}</legend>
    <input type="hidden" name="{{ $field }}" value="0" />
    {{-- The description is long: it wraps next to the checkbox, which keeps its size --}}
    <label class="label validator items-start whitespace-normal">
        <input
            type="checkbox"
            name="{{ $field }}"
            value="1"
            class="checkbox shrink-0"
            @checked(old($field, $spot?->{$field} ?? false))
            @error($field) aria-invalid="true" @enderror
        />
        {{ __("ui.spots.common.forms.fields.{$field}.hint") }}
    </label>
    @error($field)
        <span class="validator-hint mt-0">{{ $message }}</span>
    @enderror
</fieldset>
