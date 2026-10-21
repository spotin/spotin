<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.show');

render(function (View $view, Organization $organization) {
    Head::title($organization->name)->description(__('ui.organizations.show.description'));

    return $view->with([
        'membersCount' => Gate::allows('viewMembers', $organization) ? $organization->users()->count() : null,
        'invitationsCount' => Gate::allows('manageMembers', $organization) ? $organization->invitations()->count() : null,
        // The spots the user finds in the list of the organization, without the hidden ones for the others
        'spotsCount' => $organization->spots()->visibleTo(request()->user())->count(),
    ]);
});
?>

<x-default-layout>
    @if ($spotsCount > 0)
        {{-- Its spots across the page --}}
        <x-slot:hero>
            <x-map.hero
                :spots="route('organizations.spots.map', ['organization' => $organization->uuid])"
                :label="__('ui.organizations.show.map')"
                class="max-lg:h-[40vh]"
            />
        </x-slot:hero>
    @endif

    <x-scanned :title="__('ui.organizations.show.scanned')" />
    <x-organizations.details
        :organization="$organization"
        :spots-count="$spotsCount"
        :members-count="$membersCount"
        :invitations-count="$invitationsCount"
    />
</x-default-layout>
