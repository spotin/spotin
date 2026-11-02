<?php

use App\Models\Spot;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('spots.edit');

render(function (View $view, Spot $spot) {
    $user = request()->user();

    // Open to everyone while the spot needs to be set up; afterwards, as the other pages of the members
    if (! $spot->needs_setup) {
        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        if (! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
    }

    Gate::authorize('update', $spot);

    // Who does not manage the spot only sets it up, as the first person who scans it
    $setup = $user?->can('manage', $spot) !== true;

    if ($setup) {
        Head::title(__('ui.spots.setup.title'))->description(__('ui.spots.setup.description'));
    } else {
        Head::title(__('ui.spots.edit.title'))->description(__('ui.spots.edit.description'));
    }

    // Who sets the spot up does not find it in the lists: no trail
    $breadcrumbs = $setup ? [] : [...Breadcrumbs::spot($spot), ['label' => __('ui.spots.edit.title')]];

    return $view->with(['breadcrumbs' => $breadcrumbs, 'setup' => $setup]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    @if ($setup)
        <x-scanned :title="__('ui.spots.show.scanned')" />

        <h1>{{ __('ui.spots.setup.title') }}</h1>

        <p>{{ __('ui.spots.setup.introduction', ['organization' => $spot->organization->name]) }}</p>
    @else
        <h1>{{ __('ui.spots.edit.title') }}</h1>

        <p>
            {{ __('ui.spots.edit.introduction', ['spot' => $spot->displayName(), 'organization' => $spot->organization->name]) }}
        </p>
    @endif

    <x-spots.form
        :action="route('spots.update', $spot)"
        method="PATCH"
        :spot="$spot"
        :setup="$setup"
        :submit="$setup ? __('ui.spots.setup.actions.submit') : __('ui.spots.edit.form.actions.submit')"
    />
</x-default-layout>
