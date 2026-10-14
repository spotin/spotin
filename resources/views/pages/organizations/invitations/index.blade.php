<?php

use App\Models\OrganizationInvitation;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('organizations.invitations.pending');

render(function (View $view) {
    Head::title(__('ui.organizations.invitations.pending.title'))
        ->description(__('ui.organizations.invitations.pending.description'));

    $breadcrumbs = [
        ['label' => __('ui.organizations.index.title'), 'url' => route('organizations.index')],
        ['label' => __('ui.organizations.invitations.pending.title')],
    ];

    return $view->with([
        'breadcrumbs' => $breadcrumbs,
        'invitations' => OrganizationInvitation::pendingFor(auth()->user())->with(['organization', 'inviter'])->latest()->get(),
    ]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.organizations.invitations.pending.title') }}</h1>

    @if (session('status') === 'invitation-declined')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.organizations.invitations.feedback.invitation-declined') }}</span>
        </p>
    @endif

    @error('invitation')
        <p role="alert" class="alert alert-error">
            <x-lucide-circle-alert class="h-6 w-6" />
            <span>{{ $message }}</span>
        </p>
    @enderror

    @if ($invitations->isEmpty())
        <p>{{ __('ui.organizations.invitations.pending.table.empty') }}</p>
    @else
        <div class="not-prose rounded-box border-base-content/5 bg-base-100 overflow-x-auto border">
            <table class="table-zebra table-sm table">
                <thead>
                    <tr>
                        <th>{{ __('ui.organizations.invitations.pending.table.columns.organization') }}</th>
                        <th>{{ __('ui.organizations.invitations.pending.table.columns.role') }}</th>
                        <th>{{ __('ui.organizations.invitations.pending.table.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invitations as $invitation)
                        <tr>
                            <td>
                                <div class="break-words">{{ $invitation->organization->name }}</div>
                                <div class="text-xs opacity-60">
                                    @if ($invitation->inviter)
                                        {{ __('ui.organizations.invitations.pending.table.invited-by', ['name' => $invitation->inviter->name]) }}
                                        <br />
                                    @endif

                                    {{ __('ui.organizations.invitations.index.table.columns.expires') }} {{ $invitation->expires_at->isoFormat('LLL') }}
                                </div>
                            </td>
                            <td>
                                {{ __('ui.organizations.common.forms.fields.role.options.' . $invitation->role->value) }}
                            </td>
                            <td>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <form
                                        method="POST"
                                        action="{{ route('organizations.invitations.pending.accept', ['invitation' => $invitation->id]) }}"
                                    >
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-full sm:w-auto">
                                            {{ __('ui.organizations.invitations.pending.table.actions.accept') }}
                                        </button>
                                    </form>
                                    <x-organizations.decline-invitation-modal
                                        :invitation="$invitation"
                                        class="w-full sm:w-auto"
                                    >
                                        {{ __('ui.organizations.invitations.pending.table.actions.decline') }}
                                    </x-organizations.decline-invitation-modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-default-layout>
