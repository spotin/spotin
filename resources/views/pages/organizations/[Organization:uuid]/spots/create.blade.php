<?php

use App\Models\Organization;
use App\Models\Spot;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('organizations.spots.create');

render(function (View $view, Organization $organization) {
    Gate::authorize('create', [Spot::class, $organization]);

    Head::title(__('ui.organizations.spots.create.title').' - '.$organization->name)->description(__('ui.organizations.spots.create.description'));

    $breadcrumbs = [...Breadcrumbs::organizationSpots($organization), ['label' => __('ui.organizations.spots.create.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.organizations.spots.create.title') }}</h1>

    <form method="POST" action="{{ route('organizations.spots.store', ['organization' => $organization->uuid]) }}">
        @csrf

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill_the_form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.spots.common.forms.fields.name.placeholder') }}</span>
                    <x-lucide-map-pin class="h-[1em] opacity-50" />
                    <input
                        name="name"
                        type="text"
                        required
                        placeholder="{{ __('ui.spots.common.forms.fields.name.label') }}"
                        value="{{ old('name') }}"
                        @error('name') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('name')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block">
                {{ __('ui.organizations.spots.create.form.actions.submit') }}
            </button>
        </footer>
    </form>
</x-default-layout>
