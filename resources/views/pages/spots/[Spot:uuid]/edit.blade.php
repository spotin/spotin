<?php

use App\Models\Spot;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('spots.edit');

render(function (View $view, Spot $spot) {
    Gate::authorize('update', $spot);

    Head::title(__('ui.spots.edit.title'))->description(__('ui.spots.edit.description'));

    $breadcrumbs = [...Breadcrumbs::spot($spot), ['label' => __('ui.spots.edit.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.spots.edit.title') }}</h1>

    <p>{{ __('ui.spots.edit.introduction', ['spot' => $spot->name, 'organization' => $spot->organization->name]) }}</p>

    <x-spots.form
        :action="route('spots.update', $spot)"
        method="PATCH"
        :spot="$spot"
        :submit="__('ui.spots.edit.form.actions.submit')"
    />
</x-default-layout>
