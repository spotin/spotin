<?php

use App\Http\Requests\ListFiltersRequest;
use App\Models\Organization;
use App\Support\SpotListing;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.spots.index');

render(function (View $view, Organization $organization, ListFiltersRequest $request) {
    Head::title(__('ui.spots.index.title').' - '.$organization->name)->description(__('ui.spots.index.description'));

    $listing = SpotListing::for($request, $organization);

    return $view->with('listing', $listing);
});
?>

<x-default-layout>
    @if ($listing->spots->isNotEmpty())
        {{-- All the results across the page, while the list shows them page by page --}}
        <x-slot:hero>
            <x-map.hero :spots="$listing->map" :label="__('ui.spots.index.map')" />
        </x-slot:hero>
    @endif

    <x-spots.list :listing="$listing" />
</x-default-layout>
