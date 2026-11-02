<?php

use App\Models\Organization;
use App\Support\Breadcrumbs;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('spots.create');

render(function (View $view, Request $request) {
    $user = $request->user();

    // The organizations the user creates spots in: theirs, or all of them for the administrators
    $organizations = $user->isAdmin()
        ? Organization::orderBy('name')->get()
        : $user->organizations()->orderBy('name')->get();

    abort_if($organizations->isEmpty(), 403);

    Head::title(__('ui.spots.create.title'))->description(__('ui.spots.create.description'));

    // The organization of the address, such as the one whose page the user comes from, or the only one of the user: the
    // spot is created in it, without choosing. An organization they cannot create spots in is left out: they choose one.
    $organization = $organizations->firstWhere('uuid', $request->query('organization'))
        ?? ($organizations->count() === 1 ? $organizations->first() : null);

    return $view->with([
        'breadcrumbs' => [...Breadcrumbs::spots(), ['label' => __('ui.spots.create.title')]],
        'organizations' => $organizations,
        'organization' => $organization,
    ]);
});
?>

{{-- The choice of the organization, unless it is known; offered again when it was refused --}}
@php($choose = $organization === null || $errors->has('organization'))

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.spots.create.title') }}</h1>

    @if ($choose)
        <p>{{ __('ui.spots.create.introduction') }}</p>
    @else
        <p>
            {{ __('ui.spots.create.introduction_in', ['organization' => $organization->name]) }}
            @if ($organizations->count() > 1)
                <a href="{{ route('spots.create') }}" class="link">{{ __('ui.spots.create.change_organization') }}</a>
            @endif
        </p>
    @endif

    <x-spots.form
        :action="route('spots.store')"
        :organizations="$choose ? $organizations : null"
        :organization="$organization?->uuid"
        :submit="__('ui.spots.create.form.actions.submit')"
    />
</x-default-layout>
