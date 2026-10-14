<?php

use App\Models\OrganizationInvitation;
use App\Models\Spot;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('dashboard');

render(function (View $view) {
    $user = auth()->user();

    Head::title(__('ui.dashboard.title'))->description(__('ui.dashboard.description'));

    return $view->with([
        'pendingInvitations' => OrganizationInvitation::pendingFor($user)->count(),
        'organizations' => $user->organizations()->withCount('spots')->orderBy('organizations.name')->limit(6)->get(),
        'spots' => Spot::mine($user)->with('organization')->latest('id')->limit(6)->get(),
    ]);
});
?>

<x-default-layout>
    <h1>{{ __('ui.dashboard.title') }}</h1>

    <p>{{ __('ui.dashboard.greeting', ['name' => auth()->user()->name]) }}</p>

    @if ($pendingInvitations > 0)
        <p role="alert" class="alert alert-info alert-vertical sm:alert-horizontal">
            <x-lucide-mail class="h-6 w-6" />
            <span>{{ trans_choice('ui.organizations.index.pending-invitations', $pendingInvitations) }}</span>
            <a href="{{ route('organizations.invitations.pending') }}" class="btn btn-sm">
                {{ __('ui.organizations.index.view-pending-invitations') }}
            </a>
        </p>
    @endif

    <section aria-labelledby="dashboard-spots" class="mt-8">
        <div class="mb-4 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
            <h2 id="dashboard-spots" class="m-0!">{{ __('ui.dashboard.spots.title') }}</h2>
            <a href="{{ route('spots.index', ['mine' => 1]) }}" class="text-sm">
                {{ __('ui.common.actions.see-all') }}
            </a>
        </div>

        @if ($spots->isEmpty())
            <p>{{ __('ui.dashboard.spots.empty') }}</p>
        @else
            <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($spots as $spot)
                    <x-spots.card :spot="$spot" />
                @endforeach
            </div>
        @endif
    </section>

    <section aria-labelledby="dashboard-organizations" class="mt-8">
        <div class="mb-4 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
            <h2 id="dashboard-organizations" class="m-0!">{{ __('ui.dashboard.organizations.title') }}</h2>
            <a href="{{ route('organizations.index', ['mine' => 1]) }}" class="text-sm">
                {{ __('ui.common.actions.see-all') }}
            </a>
        </div>

        @if ($organizations->isEmpty())
            <p>{{ __('ui.dashboard.organizations.empty') }}</p>
        @else
            <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($organizations as $organization)
                    <x-organizations.card :organization="$organization" :role="$organization->pivot->role->value" />
                @endforeach
            </div>
        @endif
    </section>
</x-default-layout>
