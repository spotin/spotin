<?php

use App\Models\Organization;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.show');

render(function (View $view, Organization $organization) {
    Head::title($organization->name)->description(__('ui.organizations.show.description'));

    return $view->with([
        'breadcrumbs' => Breadcrumbs::organization($organization),
        'membersCount' => Gate::allows('viewMembers', $organization) ? $organization->users()->count() : null,
        'invitationsCount' => Gate::allows('manageMembers', $organization) ? $organization->invitations()->count() : null,
        // The spots the user finds in the list of the organization, without the hidden ones for the others
        'spotsCount' => $organization->spots()->visibleTo(request()->user())->count(),
    ]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    {{-- The title, centered, with the actions of the editors under it --}}
    <div class="mb-6 flex flex-col items-center gap-3 text-center">
        <h1 class="m-0! max-w-full break-words">{{ $organization->name }}</h1>

        @canany(['update', 'delete'], $organization)
            <div class="not-prose flex flex-wrap justify-center gap-2">
                @can('update', $organization)
                    <a
                        href="{{ route('organizations.edit', ['organization' => $organization->uuid]) }}"
                        class="btn btn-sm"
                    >
                        {{ __('ui.organizations.show.actions.edit') }}
                    </a>
                @endcan

                @can('delete', $organization)
                    <x-confirm-modal
                        :id="'delete-organization-'.$organization->uuid.'-modal'"
                        :action="route('organizations.destroy', ['organization' => $organization->uuid])"
                        :title="__('ui.organizations.delete.title')"
                        :warning="__('ui.organizations.delete.warning')"
                        :confirm="__('ui.organizations.delete.actions.submit')"
                    >
                        {{ __('ui.organizations.show.actions.delete') }}
                    </x-confirm-modal>
                @endcan
            </div>
        @endcanany
    </div>

    <x-scanned :title="__('ui.organizations.show.scanned')" />

    <div class="not-prose grid gap-4 sm:grid-cols-2">
        <a
            href="{{ route('organizations.spots.index', ['organization' => $organization->uuid]) }}"
            class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
        >
            <div class="card-body flex-row items-center gap-4">
                <x-lucide-map-pin class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
                <div class="min-w-0">
                    <h2 class="card-title">{{ __('ui.organizations.show.sections.spots.title') }}</h2>
                    <p class="text-sm opacity-70">
                        {{ trans_choice('ui.organizations.show.sections.spots.summary', $spotsCount) }}
                    </p>
                </div>
            </div>
        </a>

        @can('viewMembers', $organization)
            <a
                href="{{ route('organizations.members.index', ['organization' => $organization->uuid]) }}"
                class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
            >
                <div class="card-body flex-row items-center gap-4">
                    <x-lucide-users class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
                    <div class="min-w-0">
                        <h2 class="card-title">{{ __('ui.organizations.show.sections.members.title') }}</h2>
                        <p class="text-sm opacity-70">
                            {{ trans_choice('ui.organizations.show.sections.members.summary', $membersCount) }}
                        </p>
                    </div>
                </div>
            </a>
        @endcan

        @can('manageMembers', $organization)
            <a
                href="{{ route('organizations.invitations.index', ['organization' => $organization->uuid]) }}"
                class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
            >
                <div class="card-body flex-row items-center gap-4">
                    <x-lucide-mail class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
                    <div class="min-w-0">
                        <h2 class="card-title">{{ __('ui.organizations.show.sections.invitations.title') }}</h2>
                        <p class="text-sm opacity-70">
                            {{ trans_choice('ui.organizations.show.sections.invitations.summary', $invitationsCount) }}
                        </p>
                    </div>
                </div>
            </a>
        @endcan
    </div>
</x-default-layout>
