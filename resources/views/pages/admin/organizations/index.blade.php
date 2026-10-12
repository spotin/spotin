<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('admin.organizations.index');

render(function (View $view) {
    Head::title(__('ui.admin.organizations.index.title'))
        ->description(__('ui.admin.organizations.index.description'));

    Gate::authorize('viewAny', Organization::class);

    $breadcrumbs = [['label' => __('ui.admin.title'), 'url' => route('admin.index')], ['label' => __('ui.admin.organizations.index.title')]];

    return $view->with(['breadcrumbs' => $breadcrumbs, 'organizations' => Organization::withCount('users')->orderBy('name')->paginate(25)]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.admin.organizations.index.title') }}</h1>

    @if (session('status') === 'organization-deleted')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.admin.organizations.feedback.organization-deleted') }}</span>
        </p>
    @endif

    <p>
        <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary">
            {{ __('ui.admin.organizations.index.create') }}
        </a>
    </p>

    @if ($organizations->isEmpty())
        <p>{{ __('ui.admin.organizations.index.table.empty') }}</p>
    @else
        <div class="not-prose rounded-box border-base-content/5 bg-base-100 overflow-x-auto border">
            <table class="table-zebra table-sm table">
                <thead>
                    <tr>
                        <th>{{ __('ui.admin.organizations.index.table.columns.name') }}</th>
                        <th>{{ __('ui.admin.organizations.index.table.columns.members') }}</th>
                        <th>{{ __('ui.admin.organizations.index.table.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($organizations as $organization)
                        <tr>
                            <td>
                                <a href="{{ route('organizations.show', ['organization' => $organization->uuid]) }}">
                                    {{ $organization->name }}
                                </a>
                            </td>
                            <td>{{ $organization->users_count }}</td>
                            <td>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <a
                                        href="{{ route('organizations.show', ['organization' => $organization->uuid]) }}"
                                        class="btn btn-sm w-full sm:w-auto"
                                    >
                                        {{ __('ui.admin.organizations.index.table.actions.show') }}
                                    </a>
                                    <a
                                        href="{{ route('admin.organizations.edit', ['organization' => $organization->uuid]) }}"
                                        class="btn btn-sm w-full sm:w-auto"
                                    >
                                        {{ __('ui.admin.organizations.index.table.actions.edit') }}
                                    </a>
                                    <x-delete-organization-modal :organization="$organization" class="w-full sm:w-auto">
                                        {{ __('ui.admin.organizations.index.table.actions.delete') }}
                                    </x-delete-organization-modal>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $organizations->links() }}
    @endif
</x-default-layout>
