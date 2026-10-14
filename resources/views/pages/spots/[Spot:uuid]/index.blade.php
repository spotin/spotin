<?php

use App\Models\Spot;
use App\Support\Breadcrumbs;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('spots.show');

render(function (View $view, Spot $spot) {
    Head::title($spot->name)->description(__('ui.spots.show.description'));

    return $view->with('breadcrumbs', Breadcrumbs::spot($spot));
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
        <h1 class="m-0! min-w-0 break-words">{{ $spot->name }}</h1>

        @canany(['update', 'delete'], $spot)
            <div class="not-prose flex shrink-0 gap-2">
                @can('update', $spot)
                    <a href="{{ route('spots.edit', ['spot' => $spot->uuid]) }}" class="btn btn-sm">
                        {{ __('ui.spots.show.actions.edit') }}
                    </a>
                @endcan

                @can('delete', $spot)
                    <x-confirm-modal
                        :id="'delete-spot-'.$spot->uuid.'-modal'"
                        :action="route('spots.destroy', ['spot' => $spot->uuid])"
                        :title="__('ui.spots.delete.title')"
                        :warning="__('ui.spots.delete.warning')"
                        :confirm="__('ui.spots.delete.actions.submit')"
                    >
                        {{ __('ui.spots.show.actions.delete') }}
                    </x-confirm-modal>
                @endcan
            </div>
        @endcanany
    </div>

    <dl>
        <dt>{{ __('ui.spots.show.fields.organization') }}</dt>
        <dd>
            <a href="{{ route('organizations.show', ['organization' => $spot->organization->uuid]) }}">
                {{ $spot->organization->name }}
            </a>
        </dd>
        @can('viewCreator', $spot)
            <dt>{{ __('ui.spots.show.fields.creator') }}</dt>
            <dd>{{ $spot->creator?->name ?? __('ui.spots.common.unknown_creator') }}</dd>
        @endcan
        <dt>{{ __('ui.spots.show.fields.created') }}</dt>
        <dd>{{ $spot->created_at->isoFormat('LLL') }}</dd>
    </dl>
</x-default-layout>
