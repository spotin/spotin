<?php

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.index');

render(function (View $view) {
    $user = auth()->user();
    $mine = $user !== null && request()->boolean('mine');

    $searchQuery = request()->query('query');
    $searchQuery = is_string($searchQuery) ? mb_substr(trim($searchQuery), 0, 100) : '';

    $title = __('ui.organizations.index.title');

    Head::title($title)->description(__('ui.organizations.index.description'));

    return $view->with([
        'mine' => $mine,
        'searchQuery' => $searchQuery,
        'active' => $mine || $searchQuery !== '',
        'title' => $title,
        'organizations' => Organization::withCount('spots')
            ->search($searchQuery)
            ->when($mine, fn ($query) => $query->mine($user))
            ->orderBy('organizations.name')
            ->paginate(25)
            ->withQueryString(),
        'roles' => $user?->organizations()->pluck('memberships.role', 'organizations.id') ?? collect(),
        'pendingInvitations' => $user ? OrganizationInvitation::pendingFor($user)->count() : 0,
    ]);
});
?>

<x-default-layout>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
        <h1 class="m-0! min-w-0 break-words">{{ $title }}</h1>

        @can('create', App\Models\Organization::class)
            <a href="{{ route('organizations.create') }}" class="btn btn-primary btn-sm shrink-0">
                {{ __('ui.organizations.index.create') }}
            </a>
        @endcan
    </div>

    @if (session('status') === 'organization-left')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.organizations.members.feedback.organization-left') }}</span>
        </p>
    @endif

    @if (session('status') === 'organization-deleted')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.organizations.feedback.organization-deleted') }}</span>
        </p>
    @endif

    <x-organizations.pending-invitations-alert :count="$pendingInvitations" />

    <x-filters
        :action="route('organizations.index')"
        :label="__('ui.organizations.index.filter.label')"
        :active="$active"
    >
        <x-slot:search>
            <x-filters.search :placeholder="__('ui.organizations.index.filter.search')" :value="$searchQuery" />
        </x-slot:search>

        @auth
            <x-filters.checkbox name="mine" :label="__('ui.organizations.index.filter.mine')" :checked="$mine" />
        @endauth
    </x-filters>

    @if ($organizations->isEmpty())
        <p>{{ $active ? __('ui.organizations.index.no-result') : __('ui.organizations.index.empty') }}</p>
    @else
        <p class="text-sm opacity-70">{{ trans_choice('ui.organizations.index.count', $organizations->total()) }}</p>

        <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($organizations as $organization)
                <x-organizations.card :organization="$organization" :role="$roles[$organization->id] ?? null" />
            @endforeach
        </div>

        {{ $organizations->links() }}
    @endif
</x-default-layout>
