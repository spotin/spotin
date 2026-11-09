<?php

use App\Models\Organization;
use App\Support\Breadcrumbs;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;
use function Laravel\Folio\withTrashed;

middleware(['auth', 'verified']);
name('organizations.edit');
// A deleted organization, to restore it: for who can restore it only
withTrashed();

render(function (View $view, Organization $organization) {
    abort_if($organization->trashed() && request()->user()->cannot('restore', $organization), 404);

    Head::title(__('ui.organizations.edit.title'))
        ->description(__('ui.organizations.edit.description'));

    Gate::authorize('update', $organization);

    $breadcrumbs = [...Breadcrumbs::organization($organization), ['label' => __('ui.organizations.edit.title')]];

    return $view->with('breadcrumbs', $breadcrumbs);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.organizations.edit.title') }}</h1>

    {{-- Deleted, it is restored before it is edited again: its form would change an organization nobody finds --}}
    @if ($organization->trashed())
        @error('restore')
            <x-alert type="error" closable>{{ $message }}</x-alert>
        @enderror

        <x-alert type="info" icon="trash-2" class="alert-vertical sm:alert-horizontal">
            {{ __('ui.organizations.edit.deleted', ['date' => $organization->deleted_at->isoFormat('LL')]) }}

            <x-slot:actions>
                <button type="submit" form="restore-form" class="btn btn-sm btn-primary">
                    <x-lucide-archive-restore class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.common.actions.restore') }}
                </button>
            </x-slot:actions>
        </x-alert>

        {{-- Out of the alert, a paragraph, which cannot hold a form: its button sends it by its id --}}
        <form id="restore-form" method="POST" action="{{ route('organizations.restore', ['organization' => $organization->uuid]) }}" hidden>@csrf</form>
    @else
        <form method="POST" action="{{ route('organizations.update', $organization) }}">
            @csrf
            @method('PATCH')

            <fieldset class="fieldset">
                <legend class="fieldset-legend">{{ __('ui.organizations.common.forms.fields.name.label') }}</legend>
                <input
                    name="name"
                    type="text"
                    class="input validator w-full"
                    required
                    placeholder="{{ __('ui.organizations.common.forms.fields.name.placeholder') }}"
                    value="{{ old('name', $organization->name) }}"
                    @error('name') aria-invalid="true" @enderror
                />
                @error('name')
                    <span class="validator-hint mt-0">{{ $message }}</span>
                @enderror
            </fieldset>

            <footer class="mt-4">
                <button type="submit" class="btn btn-block">{{ __('ui.organizations.edit.form.actions.submit') }}</button>
            </footer>
        </form>

        @can('delete', $organization)
            <section aria-labelledby="danger-title" class="border-error/30 rounded-box not-prose mt-10 border p-4">
                <h2 id="danger-title" class="text-error font-semibold">{{ __('ui.organizations.edit.danger.title') }}</h2>
                <p class="mt-1 mb-4 text-sm">{{ __('ui.organizations.delete.warning') }}</p>
                <x-confirm-modal
                    :id="'delete-organization-'.$organization->uuid.'-modal'"
                    :action="route('organizations.destroy', ['organization' => $organization->uuid])"
                    :title="__('ui.organizations.delete.title')"
                    :warning="__('ui.organizations.delete.warning')"
                    :confirm="__('ui.organizations.delete.actions.submit')"
                    :small="false"
                >
                    <x-lucide-trash-2 class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.organizations.delete.title') }}
                </x-confirm-modal>
            </section>
        @endcan
    @endif
</x-default-layout>
