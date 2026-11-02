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

    @if (! $setup)
        @can('delete', $spot)
            <section aria-labelledby="danger-title" class="border-error/30 rounded-box not-prose mt-10 border p-4">
                <h2 id="danger-title" class="text-error font-semibold">{{ __('ui.spots.edit.danger.title') }}</h2>
                <p class="mt-1 mb-4 text-sm">{{ __('ui.spots.delete.warning') }}</p>
                <x-confirm-modal
                    :id="'delete-spot-'.$spot->uuid.'-modal'"
                    :action="route('spots.destroy', ['spot' => $spot->uuid])"
                    :title="__('ui.spots.delete.title')"
                    :warning="__('ui.spots.delete.warning')"
                    :confirm="__('ui.spots.delete.actions.submit')"
                    :small="false"
                >
                    <x-lucide-trash-2 class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.spots.delete.title') }}
                </x-confirm-modal>
            </section>
        @endcan
    @endif
</x-default-layout>
