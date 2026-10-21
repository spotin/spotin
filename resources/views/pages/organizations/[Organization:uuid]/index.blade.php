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
    <x-scanned :title="__('ui.organizations.show.scanned')" />

    {{-- The organization: the map of its spots, its name and details, and its actions --}}
    <article class="card bg-base-100 overflow-hidden shadow-md">
        @if ($spotsCount > 0)
            <figure class="mt-0 mb-0">
                <x-map
                    :spots="route('organizations.spots.map', ['organization' => $organization->uuid])"
                    :label="__('ui.organizations.show.map')"
                    :framed="false"
                    class="h-64 sm:h-80"
                />
            </figure>
        @endif

        <div class="card-body">
            <header class="flex flex-col items-center gap-2 text-center">
                <h1 class="card-title m-0! max-w-full justify-center text-3xl font-extrabold break-words">
                    {{ $organization->name }}
                </h1>

                {{--
                    The details on one line, each with its icon, leading to their page: the names are read by screen
                    readers
                --}}
                <dl class="not-prose flex flex-wrap justify-center gap-x-4 gap-y-1 text-sm">
                    <div class="flex items-center gap-1">
                        <dt class="sr-only">{{ __('ui.organizations.show.sections.spots.title') }}</dt>
                        <x-lucide-map-pin class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                        <dd>
                            <a
                                href="{{ route('organizations.spots.index', ['organization' => $organization->uuid]) }}"
                                class="link link-hover"
                            >
                                {{ trans_choice('ui.organizations.show.sections.spots.summary', $spotsCount) }}
                            </a>
                        </dd>
                    </div>
                    @can('viewMembers', $organization)
                        <div class="flex items-center gap-1">
                            <dt class="sr-only">{{ __('ui.organizations.show.sections.members.title') }}</dt>
                            <x-lucide-users class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                            <dd>
                                <a
                                    href="{{ route('organizations.members.index', ['organization' => $organization->uuid]) }}"
                                    class="link link-hover"
                                >
                                    {{ trans_choice('ui.organizations.show.sections.members.summary', $membersCount) }}
                                </a>
                            </dd>
                        </div>
                    @endcan
                    @can('manageMembers', $organization)
                        <div class="flex items-center gap-1">
                            <dt class="sr-only">{{ __('ui.organizations.show.sections.invitations.title') }}</dt>
                            <x-lucide-mail class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                            <dd>
                                <a
                                    href="{{ route('organizations.invitations.index', ['organization' => $organization->uuid]) }}"
                                    class="link link-hover"
                                >
                                    {{ trans_choice('ui.organizations.show.sections.invitations.summary', $invitationsCount) }}
                                </a>
                            </dd>
                        </div>
                    @endcan
                </dl>
            </header>
        </div>

        {{-- The actions on the organization, in a band across the card --}}
        <footer class="bg-base-200 px-6 py-4">
            {{-- Stacked on small screens, side by side on large ones, sharing the width --}}
            <div
                role="group"
                aria-label="{{ __('ui.common.actions.label') }}"
                class="join join-vertical lg:join-horizontal not-prose w-full"
            >
                {{-- TODO: Like, report and share the organization --}}
                <button type="button" class="btn btn-ghost join-item lg:flex-1">
                    <x-lucide-heart class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.common.actions.like') }}
                </button>
                <button type="button" class="btn btn-ghost join-item lg:flex-1">
                    <x-lucide-flag class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.common.actions.report') }}
                </button>
                <button type="button" class="btn btn-ghost join-item lg:flex-1">
                    <x-lucide-share-2 class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.common.actions.share') }}
                </button>

                @can('update', $organization)
                    <a
                        href="{{ route('organizations.edit', ['organization' => $organization->uuid]) }}"
                        class="btn btn-ghost join-item lg:flex-1"
                    >
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
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
                        :small="false"
                        :ghost="true"
                        class="join-item lg:flex-1"
                    >
                        <x-lucide-trash-2 class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.organizations.show.actions.delete') }}
                    </x-confirm-modal>
                @endcan
            </div>
        </footer>
    </article>
</x-default-layout>
