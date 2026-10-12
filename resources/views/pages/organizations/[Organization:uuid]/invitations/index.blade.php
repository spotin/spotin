<?php

use App\Models\Organization;
use App\Support\OrganizationBreadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('organizations.invitations.index');

render(function (View $view, Organization $organization) {
    Gate::authorize('manageMembers', $organization);

    Head::title(__('ui.organizations.invitations.index.title').' - '.$organization->name)
        ->description(__('ui.organizations.invitations.index.description'));

    $breadcrumbs = [...OrganizationBreadcrumbs::for($organization, auth()->user()), ['label' => __('ui.organizations.invitations.index.title')]];

    return $view->with(['breadcrumbs' => $breadcrumbs, 'invitations' => $organization->invitations()->orderBy('email')->paginate(25)]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.organizations.invitations.index.title') }}</h1>

    @if (session('status') === 'invitation-sent')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.organizations.invitations.feedback.invitation-sent') }}</span>
        </p>
    @elseif (session('status') === 'invitation-revoked')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.organizations.invitations.feedback.invitation-revoked') }}</span>
        </p>
    @endif

    <p>
        <a
            href="{{ route('organizations.invitations.create', ['organization' => $organization->uuid]) }}"
            class="btn btn-primary"
        >
            {{ __('ui.organizations.invitations.index.invite') }}
        </a>
    </p>

    @if ($invitations->isEmpty())
        <p>{{ __('ui.organizations.invitations.index.table.empty') }}</p>
    @else
        <div class="not-prose rounded-box border-base-content/5 bg-base-100 overflow-x-auto border">
            <table class="table-zebra table-sm table">
                <thead>
                    <tr>
                        <th>{{ __('ui.organizations.invitations.index.table.columns.email') }}</th>
                        <th>{{ __('ui.organizations.invitations.index.table.columns.role') }}</th>
                        <th>{{ __('ui.organizations.invitations.index.table.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invitations as $invitation)
                        <tr>
                            <td>
                                <div>
                                    <x-email :address="$invitation->email" class="break-words" />
                                </div>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
                                    <span class="opacity-60">
                                        {{ __('ui.organizations.invitations.index.table.columns.expires') }} {{ $invitation->expires_at->isoFormat('LLL') }}
                                    </span>
                                    @if ($invitation->isExpired())
                                        <span class="badge badge-warning badge-sm">
                                            {{ __('ui.organizations.invitations.index.table.expired') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ __('ui.organizations.common.forms.fields.role.options.' . $invitation->role->value) }}
                            </td>
                            <td>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <form
                                        method="POST"
                                        action="{{ route('organizations.invitations.resend', ['organization' => $organization->uuid, 'invitation' => $invitation->id]) }}"
                                    >
                                        @csrf
                                        <button type="submit" class="btn btn-sm w-full sm:w-auto">
                                            {{ __('ui.organizations.invitations.index.table.actions.resend') }}
                                        </button>
                                    </form>
                                    <x-revoke-invitation-modal
                                        :organization="$organization"
                                        :invitation="$invitation"
                                        class="w-full sm:w-auto"
                                    >
                                        {{ __('ui.organizations.invitations.index.table.actions.revoke') }}
                                    </x-revoke-invitation-modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $invitations->links() }}
    @endif
</x-default-layout>
