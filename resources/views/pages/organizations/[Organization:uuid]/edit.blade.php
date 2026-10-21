<?php

use App\Models\Organization;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('organizations.edit');

render(function (View $view, Organization $organization) {
    Head::title(__('ui.organizations.edit.title'))
        ->description(__('ui.organizations.edit.description'));

    Gate::authorize('update', $organization);

    $breadcrumbs = [...Breadcrumbs::organization($organization), ['label' => __('ui.organizations.edit.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.organizations.edit.title') }}</h1>

    <form method="POST" action="{{ route('organizations.update', $organization) }}">
        @csrf
        @method('PATCH')

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.organizations.common.forms.fields.name.label') }}</legend>
            <input
                name="name"
                type="text"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.organizations.common.forms.fields.name.placeholder') }}"
                value="{{ old('name', $organization->name) }}"
                @error('name') aria-invalid="true" @enderror
            />
            @error('name')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <footer class="mt-4">
            <button type="submit" class="btn btn-block">{{ __('ui.organizations.edit.form.actions.submit') }}</button>
        </footer>
    </form>
</x-default-layout>
