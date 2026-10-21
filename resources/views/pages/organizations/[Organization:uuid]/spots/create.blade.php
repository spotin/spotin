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

    <p>{{ __('ui.organizations.spots.create.introduction', ['organization' => $organization->name]) }}</p>

    <x-spots.form
        :action="route('organizations.spots.store', ['organization' => $organization->uuid])"
        :submit="__('ui.organizations.spots.create.form.actions.submit')"
    />
</x-default-layout>
