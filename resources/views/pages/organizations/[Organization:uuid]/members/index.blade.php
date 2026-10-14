<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('organizations.members.index');

render(function (View $view, Organization $organization) {
    Gate::authorize('viewMembers', $organization);

    Head::title(__('ui.organizations.members.index.title').' - '.$organization->name)
        ->description(__('ui.organizations.members.index.description'));

    $responsibleIds = $organization->responsibles()->pluck('users.id');
    $lastResponsibleId = $responsibleIds->count() === 1 ? $responsibleIds->first() : null;

    $breadcrumbs = [...Breadcrumbs::organization($organization), ['label' => __('ui.organizations.members.index.title')]];

    return $view->with([
        'breadcrumbs' => $breadcrumbs,
        'members' => $organization->users()->orderBy('name')->paginate(25),
        'lastResponsibleId' => $lastResponsibleId,
        'roles' => OrganizationRole::cases(),
        'canManage' => auth()->user()->can('manageMembers', $organization),
        'isLastResponsible' => $lastResponsibleId === auth()->id(),
    ]);
});

?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
        <h1 class="m-0! min-w-0 break-words">{{ __('ui.organizations.members.index.title') }}</h1>

        @if ($canManage)
            <a
                href="{{ route('organizations.invitations.create', ['organization' => $organization->uuid]) }}"
                class="btn btn-primary btn-sm shrink-0"
            >
                {{ __('ui.organizations.members.index.table.actions.invite') }}
            </a>
        @endif
    </div>

    @error('member')
        <p role="alert" class="alert alert-error">
            <x-lucide-circle-alert class="h-6 w-6" />
            <span>{{ $message }}</span>
        </p>
    @enderror

    @if ($members->isEmpty())
        <p>{{ __('ui.organizations.members.index.table.empty') }}</p>
    @else
        <div class="not-prose rounded-box border-base-content/5 bg-base-100 overflow-x-auto border">
            <table class="table-zebra table-sm table">
                <thead>
                    <tr>
                        <th>{{ __('ui.organizations.members.index.table.columns.name') }}</th>
                        <th>{{ __('ui.organizations.members.index.table.columns.role') }}</th>
                        <th>{{ __('ui.organizations.members.index.table.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $member)
                        <tr class="group/row">
                            <td>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    {{ $member->name }}
                                    @if ($member->is(auth()->user()))
                                        <span class="badge badge-outline badge-info">
                                            {{ __('ui.organizations.members.index.table.you') }}
                                        </span>
                                    @endif
                                </div>
                                @if ($canManage)
                                    <x-email :address="$member->email" class="block text-xs break-words opacity-60" />
                                @endif
                            </td>

                            <td>
                                @if ($canManage)
                                    <select
                                        name="role"
                                        form="role-{{ $member->id }}"
                                        class="select select-sm w-28 shrink-0 sm:w-36"
                                        aria-label="{{ __('ui.organizations.members.index.table.columns.role') }}"
                                        @disabled($member->id === $lastResponsibleId)
                                    >
                                        @foreach ($roles as $role)
                                            <option
                                                value="{{ $role->value }}"
                                                @selected($member->pivot->role === $role)
                                            >
                                                {{ __('ui.organizations.common.forms.fields.role.options.' . $role->value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    {{ __('ui.organizations.common.forms.fields.role.options.' . $member->pivot->role->value) }}
                                @endif
                            </td>

                            <td>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    @if ($canManage && $member->id !== $lastResponsibleId)
                                        <form
                                            id="role-{{ $member->id }}"
                                            method="POST"
                                            action="{{ route('organizations.members.update', ['organization' => $organization->uuid, 'user' => $member->id]) }}"
                                            class="hidden group-has-[option:checked:not([selected])]/row:block"
                                        >
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm w-full sm:w-auto">
                                                {{ __('ui.organizations.members.index.table.actions.save') }}
                                            </button>
                                        </form>
                                    @endif

                                    @if ($member->is(auth()->user()))
                                        @if ($isLastResponsible)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-disabled w-full sm:w-auto"
                                                disabled
                                                aria-describedby="last-responsible-hint"
                                            >
                                                {{ __('ui.organizations.members.index.table.actions.leave.label') }}
                                            </button>
                                        @else
                                            <x-confirm-modal
                                                :id="'leave-organization-'.$organization->uuid.'-modal'"
                                                :action="route('organizations.leave', ['organization' => $organization->uuid])"
                                                :title="__('ui.organizations.members.index.table.actions.leave.title')"
                                                :warning="__('ui.organizations.members.index.table.actions.leave.warning')"
                                                :confirm="__('ui.organizations.members.index.table.actions.leave.confirm')"
                                                class="w-full sm:w-auto"
                                            >
                                                {{ __('ui.organizations.members.index.table.actions.leave.label') }}
                                            </x-confirm-modal>
                                        @endif
                                    @elseif ($canManage)
                                        <x-confirm-modal
                                            :id="'remove-member-'.$member->id.'-modal'"
                                            :action="route('organizations.members.destroy', ['organization' => $organization->uuid, 'user' => $member->id])"
                                            :title="__('ui.organizations.members.index.table.actions.remove.title')"
                                            :warning="__('ui.organizations.members.index.table.actions.remove.warning')"
                                            :confirm="__('ui.organizations.members.index.table.actions.remove.actions.submit')"
                                            class="w-full sm:w-auto"
                                        >
                                            {{ __('ui.organizations.members.index.table.actions.remove.label') }}
                                        </x-confirm-modal>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{ $members->links() }}

    @if ($isLastResponsible)
        <p id="last-responsible-hint">
            {{ __('ui.organizations.members.index.table.actions.leave.last_responsible') }}
        </p>
    @endif
</x-default-layout>
