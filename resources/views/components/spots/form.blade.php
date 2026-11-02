@props(['action', 'submit', 'spot' => null, 'method' => 'POST', 'setup' => false])

{{--
    The form of a spot, to create or edit it, split into steps and submitted at once. `setup` is the form of the first
    person who scans a spot that needs to be set up: the name is required, and what belongs to its members is left out.
--}}
<form method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    {{-- An existing spot has all its fields filled: every step can be opened --}}
    <x-steps :complete="$spot !== null">
        {{-- Set up now, or by the first person who scans it, who gives its name then: the first decision of the members --}}
        @unless ($setup)
            <x-steps.step :title="__('ui.spots.common.forms.steps.setup')">
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">{{ __('ui.spots.common.forms.fields.needs_setup.label') }}</legend>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach (['now' => '0', 'on_site' => '1'] as $choice => $value)
                            {{-- A card for each choice, highlighted once chosen; its radio looks like the checkboxes of the form --}}
                            <label class="card card-border card-sm has-checked:border-primary has-checked:bg-primary/5 cursor-pointer">
                                <span class="card-body flex-row items-start gap-3">
                                    <input
                                        type="radio"
                                        name="needs_setup"
                                        id="needs-setup-{{ $choice }}"
                                        value="{{ $value }}"
                                        class="radio shrink-0"
                                        @checked((string) (int) old('needs_setup', $spot?->needs_setup ?? false) === $value)
                                    />
                                    <span class="flex flex-col gap-1 whitespace-normal">
                                        <span class="font-semibold">
                                            {{ __("ui.spots.common.forms.fields.needs_setup.{$choice}.label") }}
                                        </span>
                                        <span class="text-sm opacity-70">
                                            {{ __("ui.spots.common.forms.fields.needs_setup.{$choice}.hint") }}
                                        </span>
                                    </span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('needs_setup')
                        <span class="validator-hint mt-0">{{ $message }}</span>
                    @enderror
                </fieldset>
            </x-steps.step>
        @endunless

        <x-steps.step :title="__('ui.spots.common.forms.steps.name_and_description')">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">{{ __('ui.spots.common.forms.fields.name.label') }}</legend>
                {{-- Required to set the spot up; otherwise, only while it is set up now, by resources/js/app.js --}}
                <input
                    name="name"
                    type="text"
                    class="input validator w-full"
                    @if ($setup)
                        required
                    @else
                        data-required-unless-checked="needs-setup-on_site"
                    @endif
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

        <x-steps.step :title="$setup ? __('ui.spots.common.forms.fields.website.label') : __('ui.spots.common.forms.steps.website')">
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

            <x-spots.toggle field="redirect_to_website" :spot="$spot" />

            {{-- Whether the spot is listed belongs to its members --}}
            @unless ($setup)
                <x-spots.toggle field="is_hidden" :spot="$spot" />
            @endunless
        </x-steps.step>

        <x-steps.step :title="__('ui.spots.common.forms.steps.location')">
            <x-map.picker id="spot-location" :latitude="$spot?->latitude" :longitude="$spot?->longitude" />

            <x-slot:actions>
                <button type="submit" class="btn btn-primary">{{ $submit }}</button>
            </x-slot:actions>
        </x-steps.step>
    </x-steps>
</form>
