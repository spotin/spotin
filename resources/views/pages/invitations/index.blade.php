<?php

use App\Models\Invitation;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('invitations.index');

render(function (View $view) {
    Head::title(__('ui.invitations.index.title'))
        ->description(__('ui.invitations.index.description'));

    return $view->with([
        'invitations' => Invitation::pendingFor(auth()->user())->with(['organization', 'inviter'])->latest()->get(),
    ]);
});
?>

<x-default-layout>
    <h1>{{ __('ui.invitations.index.title') }}</h1>

    @error('invitation')
        <p role="alert" class="alert alert-error">
            <x-lucide-circle-alert class="h-6 w-6" />
            <span>{{ $message }}</span>
        </p>
    @enderror

    @if ($invitations->isEmpty())
        <p>{{ __('ui.invitations.index.table.empty') }}</p>
    @else
        <div class="not-prose rounded-box border-base-content/5 bg-base-100 overflow-x-auto border">
            <table class="table-zebra table-sm table">
                <thead>
                    <tr>
                        <th>{{ __('ui.invitations.index.table.columns.organization') }}</th>
                        <th>{{ __('ui.invitations.index.table.columns.role') }}</th>
                        <th>{{ __('ui.invitations.index.table.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invitations as $invitation)
                        <tr>
                            <td>
                                <div class="break-words">{{ $invitation->organization->name }}</div>
                                <div class="text-xs opacity-60">
                                    @if ($invitation->inviter)
                                        {{ __('ui.invitations.index.table.invited_by', ['name' => $invitation->inviter->name]) }}
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
                                        action="{{ route('invitations.accept', ['invitation' => $invitation->id]) }}"
                                    >
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary w-full sm:w-auto">
                                            {{ __('ui.invitations.index.table.actions.accept') }}
                                        </button>
                                    </form>
                                    <x-confirm-modal
                                        :id="'decline-invitation-'.$invitation->id.'-modal'"
                                        :action="route('invitations.decline', ['invitation' => $invitation->id])"
                                        :title="__('ui.invitations.index.table.actions.decline.title')"
                                        :warning="__('ui.invitations.index.table.actions.decline.warning')"
                                        :confirm="__('ui.invitations.index.table.actions.decline.actions.submit')"
                                        class="w-full sm:w-auto"
                                    >
                                        {{ __('ui.invitations.index.table.actions.decline.label') }}
                                    </x-confirm-modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-default-layout>
