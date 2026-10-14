<?php

use App\Models\Spot;
use App\Support\SpotBreadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('spots.show');

render(function (View $view, Spot $spot) {
    Gate::authorize('view', $spot);

    Head::title($spot->name)->description(__('ui.spots.show.description'));

    return $view->with('breadcrumbs', SpotBreadcrumbs::for($spot));
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
                    <x-spots.delete-modal :spot="$spot">
                        {{ __('ui.spots.show.actions.delete') }}
                    </x-spots.delete-modal>
                @endcan
            </div>
        @endcanany
    </div>

    @if (session('status') === 'spot-created')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.spots.feedback.spot-created') }}</span>
        </p>
    @endif

    <dl>
        <dt>{{ __('ui.spots.show.fields.organization') }}</dt>
        <dd>
            <a href="{{ route('organizations.show', ['organization' => $spot->organization->uuid]) }}">
                {{ $spot->organization->name }}
            </a>
        </dd>
        @can('viewCreator', $spot)
            <dt>{{ __('ui.spots.show.fields.creator') }}</dt>
            <dd>{{ $spot->creator?->name ?? __('ui.spots.common.unknown-creator') }}</dd>
        @endcan
        <dt>{{ __('ui.spots.show.fields.created') }}</dt>
        <dd>{{ $spot->created_at->isoFormat('LLL') }}</dd>
    </dl>
</x-default-layout>
