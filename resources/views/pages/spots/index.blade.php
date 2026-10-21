<?php

use App\Http\Requests\ListFiltersRequest;
use App\Support\SpotListing;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('spots.index');

render(function (View $view, ListFiltersRequest $request) {
    Head::title(__('ui.spots.index.title'))->description(__('ui.spots.index.description'));

    return $view->with('listing', SpotListing::for($request));
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
