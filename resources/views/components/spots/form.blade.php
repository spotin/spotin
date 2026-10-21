@props(['action', 'submit', 'spot' => null, 'method' => 'POST'])

{{-- The form of a spot, to create or edit it, split into steps and submitted at once. --}}
<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    {{-- An existing spot has all its fields filled: every step can be opened --}}
    <x-steps :complete="$spot !== null">
        <x-steps.step :title="__('ui.spots.common.forms.steps.location')">
            <x-map.picker id="spot-location" :latitude="$spot?->latitude" :longitude="$spot?->longitude" />
        </x-steps.step>

        <x-steps.step :title="__('ui.spots.common.forms.steps.details')">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">{{ __('ui.spots.common.forms.fields.name.label') }}</legend>
                <input
                    name="name"
                    type="text"
                    class="input validator w-full"
                    required
                    placeholder="{{ __('ui.spots.common.forms.fields.name.placeholder') }}"
                    value="{{ old('name', $spot?->name) }}"
                    @error('name') aria-invalid="true" @enderror
                />
                @error('name')
                    <span class="validator-hint mt-0">{{ $message }}</span>
                @enderror
            </fieldset>

            <x-markdown-editor
                name="description"
                :label="__('ui.spots.common.forms.fields.description.label')"
                :placeholder="__('ui.spots.common.forms.fields.description.placeholder')"
                :value="$spot?->description"
            />
        </x-steps.step>

        <x-steps.step :title="__('ui.spots.common.forms.steps.website')">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">{{ __('ui.spots.common.forms.fields.website.label') }}</legend>
                <input
                    name="website"
                    type="text"
                    inputmode="url"
                    class="input validator w-full"
                    placeholder="{{ __('ui.spots.common.forms.fields.website.placeholder') }}"
                    value="{{ old('website', $spot?->website) }}"
                    @error('website') aria-invalid="true" @enderror
                />
                @error('website')
                    <span class="validator-hint mt-0">{{ $message }}</span>
                @enderror
            </fieldset>

            {{-- The hidden inputs send the unchecked checkboxes, which browsers leave out --}}
            @foreach (['redirect_to_website', 'is_hidden'] as $field)
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
            @endforeach

            <x-slot:actions>
                <button type="submit" class="btn btn-primary">{{ $submit }}</button>
            </x-slot:actions>
        </x-steps.step>
    </x-steps>
</form>
