<div class="card bg-base-100 shadow p-6">
    <form method="POST" action="{{ $spot ? route('spots.update', $spot) : route('spots.store') }}" class="flex flex-col gap-4">
        @csrf
        @if ($spot) @method('PATCH') @endif

        <fieldset class="fieldset">
            <label class="fieldset-label">Nom</label>
            <input name="name" class="input input-bordered w-full" value="{{ old('name', $spot?->name) }}" placeholder="Mon spot">

            <label class="fieldset-label mt-3">Description</label>
            <textarea name="description" class="textarea textarea-bordered w-full" rows="4" placeholder="Description...">{{ old('description', $spot?->description) }}</textarea>

            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <label class="fieldset-label">Latitude</label>
                    <input type="number" step="any" name="latitude" class="input input-bordered w-full" value="{{ old('latitude', $spot?->latitude) }}" min="-90" max="90">
                </div>
                <div>
                    <label class="fieldset-label">Longitude</label>
                    <input type="number" step="any" name="longitude" class="input input-bordered w-full" value="{{ old('longitude', $spot?->longitude) }}" min="-180" max="180">
                </div>
            </div>

            <label class="fieldset-label mt-3">Lien cible (URL)</label>
            <input type="url" name="website_target" class="input input-bordered w-full" value="{{ old('website_target', $spot?->website_target) }}" placeholder="https://...">

            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <label class="fieldset-label">Public</label>
                    <select name="public" class="select select-bordered w-full">
                        <option value="0" @selected(!old('public', $spot?->public))>Non</option>
                        <option value="1" @selected(old('public', $spot?->public))>Oui</option>
                    </select>
                </div>
                <div>
                    <label class="fieldset-label">Configuré</label>
                    <select name="configured" class="select select-bordered w-full">
                        <option value="1" @selected(old('configured', $spot?->configured ?? true))>Oui</option>
                        <option value="0" @selected(!old('configured', $spot?->configured ?? true))>Non</option>
                    </select>
                </div>
            </div>

            <label class="fieldset-label mt-3">Accès direct au lien cible</label>
            <select name="direct_access_to_website_target" class="select select-bordered w-full">
                <option value="0" @selected(!old('direct_access_to_website_target', $spot?->direct_access_to_website_target))>Non</option>
                <option value="1" @selected(old('direct_access_to_website_target', $spot?->direct_access_to_website_target))>Oui</option>
            </select>

            <label class="fieldset-label mt-3">Payload (JSON)</label>
            <textarea name="payload" class="textarea textarea-bordered w-full font-mono text-xs" rows="4" placeholder='{"key": "value"}'>{{ old('payload', $spot && $spot->payload ? json_encode($spot->payload) : '') }}</textarea>
        </fieldset>

        <div class="flex gap-2 justify-end mt-2">
            @if ($spot)
                <a href="{{ route('spots.show', $spot) }}" class="btn btn-ghost">Annuler</a>
            @endif
            <button class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
