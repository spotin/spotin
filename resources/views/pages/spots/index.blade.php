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
    <x-spots.list :listing="$listing" />
</x-default-layout>
