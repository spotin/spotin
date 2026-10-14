<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Support\OrganizationBreadcrumbs;
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

    $breadcrumbs = [...OrganizationBreadcrumbs::for($organization), ['label' => __('ui.organizations.invitations.index.title'), 'url' => route('organizations.invitations.index', ['organization' => $organization->uuid])], ['label' => __('ui.organizations.invitations.create.title')]];

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

        <fieldset class="fieldset mb-4 gap-3">
            <legend class="fieldset-legend">{{ __('ui.common.fill-the-form') }}</legend>

            <div>
                <label class="floating-label input validator w-full">
                    <span>{{ __('ui.organizations.invitations.create.fields.email.placeholder') }}</span>
                    <x-lucide-mail class="h-[1em] opacity-50" />
                    <input
                        name="email"
                        type="email"
                        required
                        placeholder="{{ __('ui.organizations.invitations.create.fields.email.label') }}"
                        value="{{ old('email') }}"
                        @error('email') aria-invalid="true" @enderror
                        class="input w-full"
                    />
                </label>
                @error('email')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="select w-full">
                    <span class="label">
                        <x-lucide-user-shield class="h-[1em]" />
                    </span>
                    <select
                        name="role"
                        required
                        @error('role') aria-invalid="true" @enderror
                        class="invalid:text-base-content/50! [&>option]:text-base-content"
                    >
                        <option value="" disabled selected>
                            {{ __('ui.organizations.common.forms.fields.role.label') }}
                        </option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role') === $role->value)>
                                {{ __('ui.organizations.common.forms.fields.role.options.' . $role->value) }}
                            </option>
                        @endforeach
                    </select>
                </label>
                @error('role')
                    <span class="validator-hint">{{ $message }}</span>
                @enderror
            </div>
        </fieldset>

        <footer>
            <button type="submit" class="btn btn-block">
                {{ __('ui.organizations.invitations.create.actions.submit') }}
            </button>
        </footer>
    </form>
</x-default-layout>
