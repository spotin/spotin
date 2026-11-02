<?php

use App\Http\Requests\ListFiltersRequest;
use App\Models\Organization;
use App\Support\SpotListing;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.show');

render(function (View $view, Organization $organization, ListFiltersRequest $request) {
    Head::title($organization->name)->description(__('ui.organizations.show.description'));

    return $view->with([
        'membersCount' => Gate::allows('viewMembers', $organization) ? $organization->users()->count() : null,
        'invitationsCount' => Gate::allows('manageMembers', $organization) ? $organization->invitations()->count() : null,
        // The spots the user finds in the list of the organization, without the hidden ones for the others
        'spotsCount' => $organization->spots()->visibleTo(request()->user())->count(),
        // The map is only shown when one of these spots has a location
        'hasMap' => $organization->spots()->visibleTo(request()->user())->located()->exists(),
        // Its spots, as in the list of all the spots: with the filters, page by page
        'listing' => SpotListing::for($request, $organization),
    ]);
});
?>

<x-default-layout>
    @if ($hasMap && $listing->spots->isNotEmpty())
        {{-- Its spots across the page: all the results of its list, with its filters --}}
        <x-slot:hero>
            <x-map.hero :spots="$listing->map" :label="__('ui.organizations.show.map')" class="max-lg:h-[40vh]" />
        </x-slot:hero>
    @endif

    <x-scanned :title="__('ui.organizations.show.scanned')" />

    {{--
        What only the members of the organization and the administrators see: who manage it, or are part of it, with
        their tools. Its deletion is on its edit page, away from the actions of the visitors.
    --}}
    @can('viewMembers', $organization)
        <section aria-labelledby="manage-title" class="card card-border card-sm bg-base-200 not-prose mb-8">
            <div class="card-body">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="manage-title" class="card-title">
                        @can('manage', $organization)
                            {{ __('ui.organizations.show.manage.title') }}
                        @else
                            {{ __('ui.organizations.show.manage.title_member') }}
                        @endcan
                    </h2>

                    {{-- Only actions, as in the box of the managers of a spot --}}
                    @canany(['update', 'manage'], $organization)
                        <div class="card-actions">
                            @can('update', $organization)
                                <a
                                    href="{{ route('organizations.edit', ['organization' => $organization->uuid]) }}"
                                    class="btn btn-sm"
                                >
                                    <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                                    {{ __('ui.organizations.show.manage.actions.edit') }}
                                </a>
                            @endcan

                            @can('manage', $organization)
                                <x-qr-code-menu
                                    :svg="route('organizations.qr-code', ['organization' => $organization->uuid, 'format' => 'svg'])"
                                    :png="route('organizations.qr-code', ['organization' => $organization->uuid, 'format' => 'png'])"
                                    :scan="route('organizations.redirect', ['organization' => $organization->uuid])"
                                    :hint="__('ui.common.qr_code.hint')"
                                />
                            @endcan
                        </div>
                    @endcanany
                </div>

                {{--
                    What only its members know about it, on one line: its members and invitations, leading to their pages,
                    and when it was created
                --}}
                <p class="text-sm opacity-70">
                    <a
                        href="{{ route('organizations.members.index', ['organization' => $organization->uuid]) }}"
                        class="link"
                    >{{ trans_choice('ui.organizations.show.manage.members', $membersCount) }}</a>
                    <span aria-hidden="true">·</span>
                    @can('manageMembers', $organization)
                        <a
                            href="{{ route('organizations.invitations.index', ['organization' => $organization->uuid]) }}"
                            class="link"
                        >{{ trans_choice('ui.organizations.show.manage.invitations', $invitationsCount) }}</a>
                        <span aria-hidden="true">·</span>
                    @endcan
                    {{ __('ui.organizations.show.manage.created', ['date' => $organization->created_at->isoFormat('LL')]) }}
                </p>
            </div>
        </section>
    @endcan

    {{-- The organization: its name, the way to its spots, and the actions of its visitors --}}
    <article>
        <header class="not-prose flex flex-col gap-1">
            <h1 class="max-w-full text-3xl font-extrabold break-words">{{ $organization->name }}</h1>

            {{--
                The number of its spots, leading to their list below, and the actions on one row, as the box of its
                members; below each other when narrow
            --}}
            <div class="flex flex-wrap items-center justify-between gap-2">
                <a href="#spots" class="link link-hover text-sm opacity-80">
                    {{ trans_choice('ui.organizations.common.spots', $spotsCount) }}
                </a>

                {{-- Lighter than the tools of its members --}}
                <div role="group" aria-label="{{ __('ui.common.actions.label') }}" class="flex flex-wrap gap-1">
                    {{-- TODO: Like, report and share the organization --}}
                    <button type="button" class="btn btn-ghost btn-sm">
                        <x-lucide-heart class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.common.actions.like') }}
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm">
                        <x-lucide-flag class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.common.actions.report') }}
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm">
                        <x-lucide-share-2 class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.common.actions.share') }}
                    </button>
                </div>
            </div>
        </header>
    </article>

    {{-- Its spots, with the filters and the pages of the list of all the spots --}}
    <section id="spots" class="mt-10">
        <x-spots.list :listing="$listing" nested />
    </section>
</x-default-layout>
