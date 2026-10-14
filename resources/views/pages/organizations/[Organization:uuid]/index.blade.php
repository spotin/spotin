<?php

use App\Models\Organization;
use App\Support\OrganizationBreadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.show');

render(function (View $view, Organization $organization) {
    Gate::authorize('view', $organization);

    Head::title($organization->name)->description(__('ui.organizations.show.description'));

    $breadcrumbs = OrganizationBreadcrumbs::for($organization);

    return $view->with([
        'breadcrumbs' => $breadcrumbs,
        'membersCount' => Gate::allows('viewMembers', $organization) ? $organization->users()->count() : null,
        'invitationsCount' => Gate::allows('manageMembers', $organization) ? $organization->invitations()->count() : null,
        'spotsCount' => $organization->spots()->count(),
    ]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
        <h1 class="m-0! min-w-0 break-words">{{ $organization->name }}</h1>

        @canany(['update', 'delete'], $organization)
            <div class="not-prose flex shrink-0 gap-2">
                @can('update', $organization)
                    <a
                        href="{{ route('organizations.edit', ['organization' => $organization->uuid]) }}"
                        class="btn btn-sm"
                    >
                        {{ __('ui.organizations.show.actions.edit') }}
                    </a>
                @endcan

                @can('delete', $organization)
                    <x-organizations.delete-modal :organization="$organization">
                        {{ __('ui.organizations.show.actions.delete') }}
                    </x-organizations.delete-modal>
                @endcan
            </div>
        @endcanany
    </div>

    @if (session('status') === 'organization-created')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.organizations.feedback.organization-created') }}</span>
        </p>
    @elseif (session('status') === 'invitation-accepted')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.organizations.invitations.feedback.invitation-accepted') }}</span>
        </p>
    @endif

    <div class="not-prose grid gap-4 sm:grid-cols-2">
        <a
            href="{{ route('spots.index', ['organization' => $organization->uuid]) }}"
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
