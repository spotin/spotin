<?php

use App\Models\Invitation;
use App\Models\Organization;
use App\Models\Spot;
use App\Support\GettingStarted;
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
        'pendingInvitations' => Invitation::pendingFor($user)->count(),
        'gettingStarted' => GettingStarted::for($user),
        // The organizations, for the administrators; then the spots, for them and the members of an organization
        'quickActions' => array_values(array_filter([
            $user->can('create', Organization::class)
                ? ['key' => 'organization', 'url' => route('organizations.create'), 'icon' => 'building-2']
                : null,
            $user->can('createAny', Spot::class)
                ? ['key' => 'spot', 'url' => route('spots.create'), 'icon' => 'map-pin']
                : null,
        ])),
        // The organizations the user is a member of, and all their spots
        'statistics' => [
            'organizations' => $user->organizations()->count(),
            'spots' => Spot::whereIn('organization_id', $user->organizations()->select('organizations.id'))->count(),
        ],
    ]);
});
?>

<x-default-layout>
    <h1>{{ __('ui.dashboard.title') }}</h1>

    @if ($pendingInvitations > 0)
        <x-alert type="info" icon="mail" class="alert-vertical sm:alert-horizontal">
            {{ trans_choice('ui.invitations.common.alert.message', $pendingInvitations) }}

            <x-slot:actions>
                <a href="{{ route('invitations.index') }}" class="btn btn-sm">
                    {{ __('ui.invitations.common.alert.action') }}
                </a>
            </x-slot:actions>
        </x-alert>
    @endif

    <p>{{ __('ui.dashboard.greeting', ['name' => auth()->user()->name]) }}</p>

    @if ($gettingStarted->isVisible())
        <x-dashboard.getting-started :checklist="$gettingStarted" />
    @endif

    <section aria-labelledby="overview" class="mt-8">
        <h2 id="overview">{{ __('ui.dashboard.overview') }}</h2>

        {{-- Stacked on small screens, side by side on large ones; each leads to its list --}}
        <div class="not-prose stats stats-vertical lg:stats-horizontal border-base-200 bg-base-100 w-full border">
            @foreach (['spots' => ['spots.index', 'map-pin'], 'organizations' => ['organizations.index', 'building-2']] as $statistic => [$route, $icon])
                <a href="{{ route($route) }}" class="stat hover:bg-base-200">
                    <div class="stat-figure text-primary">
                        <x-dynamic-component :component="'lucide-'.$icon" class="h-8 w-8" aria-hidden="true" />
                    </div>
                    <div class="stat-title">{{ __("ui.dashboard.statistics.$statistic.title") }}</div>
                    <div class="stat-value text-primary">{{ $statistics[$statistic] }}</div>
                    <div class="stat-desc">{{ __("ui.dashboard.statistics.$statistic.description") }}</div>
                </a>
            @endforeach
        </div>
    </section>

    @if ($quickActions)
        <section aria-labelledby="quick-actions" class="mt-8">
            <h2 id="quick-actions">{{ __('ui.dashboard.quick_actions.title') }}</h2>

            <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($quickActions as $action)
                    <x-link-card
                        :href="$action['url']"
                        :icon="$action['icon']"
                        :title="__('ui.dashboard.quick_actions.'.$action['key'].'.title')"
                        variant="action"
                        :level="3"
                    >
                        <p class="text-sm opacity-70">{{ __("ui.dashboard.quick_actions.{$action['key']}.description") }}</p>
                    </x-link-card>
                @endforeach
            </div>
        </section>
    @endif
</x-default-layout>
