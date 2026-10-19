<?php

use App\Http\Requests\ListFiltersRequest;
use App\Support\OrganizationListing;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.index');

render(function (View $view, ListFiltersRequest $request) {
    Head::title(__('ui.organizations.index.title'))->description(__('ui.organizations.index.description'));

    return $view->with('listing', OrganizationListing::for($request));
});
?>

<x-default-layout>
    <x-organizations.list :listing="$listing" />
</x-default-layout>
