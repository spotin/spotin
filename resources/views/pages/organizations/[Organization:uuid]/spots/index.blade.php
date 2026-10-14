<?php

use App\Http\Requests\ListFiltersRequest;
use App\Models\Organization;
use App\Support\Breadcrumbs;
use App\Support\SpotListing;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.spots.index');

render(function (View $view, Organization $organization, ListFiltersRequest $request) {
    Head::title(__('ui.spots.index.title').' - '.$organization->name)->description(__('ui.spots.index.description'));

    $listing = SpotListing::for($request, $organization);

    return $view->with(['listing' => $listing, 'breadcrumbs' => [...Breadcrumbs::organization($organization), ['label' => __('ui.spots.index.title')]]]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <x-spots.list :listing="$listing" />
</x-default-layout>
