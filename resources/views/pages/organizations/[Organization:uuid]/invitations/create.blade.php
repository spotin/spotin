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
name('organizations.invitations.create');

render(function (View $view, Organization $organization) {
    Gate::authorize('manageMembers', $organization);

    Head::title(__('ui.organizations.invitations.create.title').' - '.$organization->name)
        ->description(__('ui.organizations.invitations.create.description'));

    $breadcrumbs = [...Breadcrumbs::organizationInvitations($organization), ['label' => __('ui.organizations.invitations.create.title')]];

    return $view->with(['breadcrumbs' => $breadcrumbs, 'roles' => OrganizationRole::cases()]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.organizations.invitations.create.title') }}</h1>

    <form
        method="POST"
        action="{{ route('organizations.invitations.store', ['organization' => $organization->uuid]) }}"
    >
        @csrf

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.organizations.invitations.create.fields.email.label') }}</legend>
            <input
                name="email"
                type="text"
                inputmode="email"
                class="input validator w-full"
                required
                placeholder="{{ __('ui.organizations.invitations.create.fields.email.placeholder') }}"
                value="{{ old('email') }}"
                @error('email') aria-invalid="true" @enderror
            />
            @error('email')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <fieldset class="fieldset">
            <legend class="fieldset-legend">{{ __('ui.organizations.common.forms.fields.role.label') }}</legend>
            <select
                name="role"
                class="select validator invalid:text-base-content/50! [&>option]:text-base-content w-full"
                required
                @error('role') aria-invalid="true" @enderror
            >
                <option value="" disabled selected>
                    {{ __('ui.organizations.common.forms.fields.role.placeholder') }}
                </option>
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected(old('role') === $role->value)>
                        {{ __('ui.organizations.common.forms.fields.role.options.' . $role->value) }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <span class="validator-hint mt-0">{{ $message }}</span>
            @enderror
        </fieldset>

        <footer class="mt-4">
            <button type="submit" class="btn btn-block">
                {{ __('ui.organizations.invitations.create.actions.submit') }}
            </button>
        </footer>
    </form>
</x-default-layout>
