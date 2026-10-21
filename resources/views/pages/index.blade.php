<?php

use App\Models\Spot;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('index');

render(function (View $view) {
    Head::title(__('ui.homepage.title'))
        ->description(__('ui.homepage.description'));

    // Without the hidden spots, as in the lists
    return $view->with('latestSpots', Spot::visibleTo(request()->user())->with('organization')->latest('id')->limit(6)->get());
});
?>

<x-default-layout>
    <section aria-labelledby="page-title" class="text-center">
        <h1 id="page-title">{{ __('ui.homepage.title') }}</h1>
        <p class="opacity-80">{{ __('ui.homepage.introduction') }}</p>

        <div class="flex flex-wrap justify-center gap-2">
            <a href="{{ route('spots.index') }}" class="btn btn-primary">{{ __('ui.homepage.explore_spots') }}</a>
            <a href="{{ route('organizations.index') }}" class="btn">{{ __('ui.homepage.explore_organizations') }}</a>
        </div>

        @guest
            <p>
                <a href="{{ route('login') }}">{{ __('ui.auth.login.title') }}</a>
                ·
                <a href="{{ route('register') }}">{{ __('ui.auth.register.title') }}</a>
            </p>
        @else
            <p>
                <a href="{{ route('dashboard') }}">{{ __('ui.homepage.dashboard') }}</a>
            </p>
        @endguest
    </section>

    <section aria-labelledby="homepage-latest-spots" class="mt-8">
        <div class="mb-4 flex flex-wrap items-baseline justify-between gap-x-4 gap-y-2">
            <h2 id="homepage-latest-spots">{{ __('ui.homepage.latest_spots') }}</h2>
            <a href="{{ route('spots.index') }}" class="text-sm"> {{ __('ui.common.actions.see_all') }} </a>
        </div>

        @if ($latestSpots->isEmpty())
            <p>{{ __('ui.homepage.latest_spots_empty') }}</p>
        @else
            <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach ($latestSpots as $spot)
                    <x-spots.card :spot="$spot" />
                @endforeach
            </div>
        @endif
    </section>
</x-default-layout>
