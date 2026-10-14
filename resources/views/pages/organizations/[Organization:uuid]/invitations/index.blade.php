<?php

use App\Models\Organization;
use App\Support\Breadcrumbs;
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

    $breadcrumbs = Breadcrumbs::organizationInvitations($organization);

    return $view->with(['breadcrumbs' => $breadcrumbs, 'invitations' => $organization->invitations()->orderBy('email')->paginate(25)]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
        <h1 class="m-0! min-w-0 break-words">{{ __('ui.organizations.invitations.index.title') }}</h1>

        <a
            href="{{ route('organizations.invitations.create', ['organization' => $organization->uuid]) }}"
            class="btn btn-primary btn-sm shrink-0"
        >
            {{ __('ui.organizations.invitations.index.invite') }}
        </a>
    </div>

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
                                    <x-confirm-modal
                                        :id="'revoke-invitation-'.$invitation->id.'-modal'"
                                        :action="route('organizations.invitations.destroy', ['organization' => $organization->uuid, 'invitation' => $invitation->id])"
                                        :title="__('ui.organizations.invitations.index.table.actions.revoke.title')"
                                        :warning="__('ui.organizations.invitations.index.table.actions.revoke.warning')"
                                        :confirm="__('ui.organizations.invitations.index.table.actions.revoke.actions.submit')"
                                        class="w-full sm:w-auto"
                                    >
                                        {{ __('ui.organizations.invitations.index.table.actions.revoke.label') }}
                                    </x-confirm-modal>
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
