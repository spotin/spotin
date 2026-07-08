<div class="card bg-base-100 shadow p-6">
    <form method="POST" action="{{ $spot ? route('spots.update', $spot) : route('spots.store') }}" class="flex flex-col gap-4">
        @csrf
        @if ($spot) @method('PATCH') @endif

        <fieldset class="fieldset">
            <label class="fieldset-label">{{ __('ui.spots.form.name.label') }}</label>
            <input name="name" class="input input-bordered w-full"
                   placeholder="{{ __('ui.spots.form.name.placeholder') }}"
                   value="{{ old('name', $spot?->name) }}">

            <label class="fieldset-label mt-3">{{ __('ui.spots.form.description.label') }}</label>
            <textarea name="description" class="textarea textarea-bordered w-full" rows="4"
                      placeholder="{{ __('ui.spots.form.description.placeholder') }}">{{ old('description', $spot?->description) }}</textarea>

            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <label class="fieldset-label">{{ __('ui.spots.form.latitude.label') }}</label>
                    <input type="number" step="any" name="latitude" class="input input-bordered w-full"
                           placeholder="{{ __('ui.spots.form.latitude.placeholder') }}"
                           value="{{ old('latitude', $spot?->latitude) }}" min="-90" max="90">
                </div>
                <div>
                    <label class="fieldset-label">{{ __('ui.spots.form.longitude.label') }}</label>
                    <input type="number" step="any" name="longitude" class="input input-bordered w-full"
                           placeholder="{{ __('ui.spots.form.longitude.placeholder') }}"
                           value="{{ old('longitude', $spot?->longitude) }}" min="-180" max="180">
                </div>
            </div>

            <label class="fieldset-label mt-3">{{ __('ui.spots.form.websiteTarget.label') }}</label>
            <input type="url" name="website_target" class="input input-bordered w-full"
                   placeholder="{{ __('ui.spots.form.websiteTarget.placeholder') }}"
                   value="{{ old('website_target', $spot?->website_target) }}">

            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <label class="fieldset-label">{{ __('ui.spots.form.public.label') }}</label>
                    <select name="public" class="select select-bordered w-full">
                        <option value="0" @selected(!old('public', $spot?->public))>{{ __('ui.spots.form.public.values.false') }}</option>
                        <option value="1" @selected(old('public', $spot?->public))>{{ __('ui.spots.form.public.values.true') }}</option>
                    </select>
                </div>
                <div>
                    <label class="fieldset-label">{{ __('ui.spots.form.configured.label') }}</label>
                    <select name="configured" class="select select-bordered w-full">
                        <option value="1" @selected(old('configured', $spot?->configured ?? true))>{{ __('ui.spots.form.configured.values.true') }}</option>
                        <option value="0" @selected(!old('configured', $spot?->configured ?? true))>{{ __('ui.spots.form.configured.values.false') }}</option>
                    </select>
                </div>
            </div>

            <label class="fieldset-label mt-3">{{ __('ui.spots.form.directAccessToWebsiteTarget.label') }}</label>
            <select name="direct_access_to_website_target" class="select select-bordered w-full">
                <option value="0" @selected(!old('direct_access_to_website_target', $spot?->direct_access_to_website_target))>{{ __('ui.spots.form.directAccessToWebsiteTarget.values.false') }}</option>
                <option value="1" @selected(old('direct_access_to_website_target', $spot?->direct_access_to_website_target))>{{ __('ui.spots.form.directAccessToWebsiteTarget.values.true') }}</option>
            </select>

            <label class="fieldset-label mt-3">{{ __('ui.spots.form.optionsForAdvancedUsers.payload.label') }}</label>
            <textarea name="payload" class="textarea textarea-bordered w-full font-mono text-xs" rows="4"
                      placeholder="{{ __('ui.spots.form.optionsForAdvancedUsers.payload.placeholder') }}">{{ old('payload', $spot && $spot->payload ? json_encode($spot->payload) : '') }}</textarea>
            <p class="text-xs text-base-content/50">{{ __('ui.spots.form.optionsForAdvancedUsers.payload.note') }}</p>
        </fieldset>

        <div class="flex gap-2 justify-end mt-2">
            @if ($spot)
                <a href="{{ route('spots.show', $spot) }}" class="btn btn-ghost">{{ __('ui.spots.form.actions.view') }}</a>
                <form method="POST" action="{{ route('spots.destroy', $spot) }}" onsubmit="return confirm('{{ __('ui.spots.form.actions.delete') }} ?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-ghost text-error">{{ __('ui.spots.form.actions.delete') }}</button>
                </form>
            @endif
            <button class="btn btn-primary">{{ __('ui.spots.form.actions.save') }}</button>
        </div>
    </form>
</div>
