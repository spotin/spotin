<?php

use App\Models\Spot;
use App\Support\Breadcrumbs;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('spots.show');

render(function (View $view, Spot $spot) {
    Head::title($spot->name)->description(__('ui.spots.show.description'));

    return $view->with('breadcrumbs', Breadcrumbs::spot($spot));
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <x-scanned :title="__('ui.spots.show.scanned')" />

    @can('update', $spot)
        @if ($spot->redirectsVisitors())
            <p role="status" class="alert alert-info">
                <x-lucide-external-link class="h-6 w-6 shrink-0" aria-hidden="true" />
                <span>{{ __('ui.spots.show.notes.redirect', ['website' => $spot->website]) }}</span>
            </p>
        @endif

        @if ($spot->is_hidden)
            <p role="status" class="alert alert-info">
                <x-lucide-eye-off class="h-6 w-6 shrink-0" aria-hidden="true" />
                <span>{{ __('ui.spots.show.notes.hidden', ['organization' => $spot->organization->name]) }}</span>
            </p>
        @endif
    @endcan

    {{-- The spot: its map, its name and details, its description, and the way to its website --}}
    <article class="card bg-base-100 overflow-hidden shadow-md">
        {{-- Without JS, the map is hidden and the coordinates stay --}}
        <figure class="mt-0 mb-0 flex-col items-stretch">
            <x-map
                :latitude="$spot->latitude"
                :longitude="$spot->longitude"
                :label="__('ui.spots.show.map')"
                :framed="false"
                class="h-64 sm:h-80"
            />
            <figcaption class="bg-base-200 mt-0 flex items-center gap-1 px-6 py-2 text-xs">
                <x-lucide-map-pin class="h-3.5 w-3.5 shrink-0" aria-hidden="true" />
                <span class="sr-only">{{ __('ui.spots.show.fields.coordinates') }}</span>
                {{ number_format($spot->latitude, 6, '.', '') }}, {{ number_format($spot->longitude, 6, '.', '') }}
            </figcaption>
        </figure>

        <div class="card-body">
            {{-- Spaced from the description, which may start with a heading of its own --}}
            <header class="mb-4 flex flex-col items-center gap-2 text-center">
                <h1 class="card-title m-0! max-w-full justify-center text-3xl font-extrabold break-words">
                    {{ $spot->name }}
                </h1>

                <dl class="not-prose flex flex-wrap justify-center gap-x-4 gap-y-1 text-sm opacity-80">
                    <div class="flex items-center gap-1">
                        <dt class="sr-only">{{ __('ui.spots.show.fields.organization') }}</dt>
                        <x-lucide-building-complex class="h-4 w-4 shrink-0" aria-hidden="true" />
                        <dd>
                            <a
                                href="{{ route('organizations.show', ['organization' => $spot->organization->uuid]) }}"
                                class="link link-hover"
                            >
                                {{ $spot->organization->name }}
                            </a>
                        </dd>
                    </div>
                    <div class="flex items-center gap-1">
                        <dt class="sr-only">{{ __('ui.spots.show.fields.created') }}</dt>
                        <x-lucide-calendar class="h-4 w-4 shrink-0" aria-hidden="true" />
                        <dd>{{ $spot->created_at->isoFormat('LL') }}</dd>
                    </div>
                    @can('viewCreator', $spot)
                        <div class="flex items-center gap-1">
                            <dt class="sr-only">{{ __('ui.spots.show.fields.creator') }}</dt>
                            <x-lucide-user class="h-4 w-4 shrink-0" aria-hidden="true" />
                            <dd>{{ $spot->creator?->name ?? __('ui.spots.common.unknown_creator') }}</dd>
                        </div>
                    @endcan
                </dl>
            </header>

            @if (filled($spot->description))
                {{--
                    Safe: HTML is stripped and unsafe links are dropped when rendering the Markdown. Its own `prose` removes
                    the margin above its first element, and its images are kept to a reasonable height, centered.
                --}}
                <div class="prose prose-img:mx-auto prose-img:block prose-img:max-h-80 prose-img:w-auto prose-img:rounded-box max-w-none">
                    {!! $spot->descriptionHtml() !!}
                </div>
            @endif

            @if (filled($spot->website))
                <x-leave-modal :id="'leave-spot-'.$spot->uuid.'-modal'" :url="$spot->website" class="btn-block mt-4">
                    {{ __('ui.spots.show.visit', ['website' => parse_url($spot->website, PHP_URL_HOST) ?? $spot->website]) }}
                </x-leave-modal>
            @endif
        </div>

        <footer class="bg-base-200 px-6 py-4">
            <div
                role="group"
                aria-label="{{ __('ui.spots.show.actions.label') }}"
                class="join join-vertical lg:join-horizontal not-prose w-full"
            >
                {{-- TODO: Like, report and share the spot --}}
                <button type="button" class="btn btn-ghost join-item lg:flex-1">
                    <x-lucide-heart class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.spots.show.actions.like') }}
                </button>
                <button type="button" class="btn btn-ghost join-item lg:flex-1">
                    <x-lucide-flag class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.spots.show.actions.report') }}
                </button>
                <button type="button" class="btn btn-ghost join-item lg:flex-1">
                    <x-lucide-share-2 class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.spots.show.actions.share') }}
                </button>

                @can('update', $spot)
                    <a
                        href="{{ route('spots.edit', ['spot' => $spot->uuid]) }}"
                        class="btn btn-ghost join-item lg:flex-1"
                    >
                        <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.spots.show.actions.edit') }}
                    </a>
                @endcan

                @can('delete', $spot)
                    <x-confirm-modal
                        :id="'delete-spot-'.$spot->uuid.'-modal'"
                        :action="route('spots.destroy', ['spot' => $spot->uuid])"
                        :title="__('ui.spots.delete.title')"
                        :warning="__('ui.spots.delete.warning')"
                        :confirm="__('ui.spots.delete.actions.submit')"
                        :small="false"
                        :ghost="true"
                        class="join-item lg:flex-1"
                    >
                        <x-lucide-trash-2 class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.spots.show.actions.delete') }}
                    </x-confirm-modal>
                @endcan
            </div>
        </footer>
    </article>
</x-default-layout>
