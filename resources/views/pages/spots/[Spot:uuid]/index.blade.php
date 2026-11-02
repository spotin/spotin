<?php

use App\Models\Spot;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('spots.show');

render(function (View $view, Spot $spot) {
    // Until it is set up, only who manages it sees its page: the others set it up
    if ($spot->needs_setup && request()->user()?->can('manage', $spot) !== true) {
        return redirect()->route('spots.edit', ['spot' => $spot->uuid]);
    }

    Head::title($spot->displayName())->description(__('ui.spots.show.description'));

    return $view;
});
?>

<x-default-layout>
    @if ($spot->hasLocation())
        <x-slot:hero>
            {{-- The spot across the page, given in the page without a link to itself, and its coordinates under it --}}
            <x-map.hero
                :spots="App\Models\Spot::featureCollection([$spot], links: false)"
                :label="__('ui.spots.show.map')"
                class="max-lg:h-[40vh]"
            />
            {{-- Without JS, the map is hidden and the coordinates stay --}}
            <p class="bg-base-200 flex items-center justify-center gap-1 px-4 py-2 text-xs">
                <x-lucide-map-pin class="h-3.5 w-3.5 shrink-0" aria-hidden="true" />
                <span class="sr-only">{{ __('ui.spots.show.fields.coordinates') }}</span>
                {{-- In their own element, without space around: copied as they are --}}
                <span>{{ number_format($spot->latitude, 6, '.', '') }}, {{ number_format($spot->longitude, 6, '.', '') }}</span>
            </p>
        </x-slot:hero>
    @endif

    <x-scanned :title="__('ui.spots.show.scanned')" />

    @can('manage', $spot)
        @if ($spot->needs_setup)
            <x-alert type="info" icon="wrench" role="status">{{ __('ui.spots.show.notes.needs_setup') }}</x-alert>
        @endif

        @if ($spot->redirectsVisitors())
            <x-alert
                type="info"
                icon="external-link"
                role="status"
            >{{ __('ui.spots.show.notes.redirect', ['website' => $spot->website]) }}</x-alert>
        @endif

        @if ($spot->is_hidden)
            <x-alert
                type="info"
                icon="eye-off"
                role="status"
            >{{ __('ui.spots.show.notes.hidden', ['organization' => $spot->organization->name]) }}</x-alert>
        @endif
    @endcan

    {{-- The spot: its name and details, its description, and the way to its website, in the page as its organization --}}
    <article>
        <div class="flex flex-col gap-2">
            {{-- Spaced from the description, which may start with a heading of its own --}}
            <header class="mb-4 flex flex-col items-center gap-2 text-center">
                <h1 class="card-title m-0! max-w-full justify-center text-3xl font-extrabold break-words">
                    {{ $spot->displayName() }}
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

        {{-- The actions on the spot: on one line when they fit, on more otherwise --}}
        <footer class="mt-6">
            <div
                role="group"
                aria-label="{{ __('ui.common.actions.label') }}"
                class="not-prose flex flex-wrap justify-center gap-2"
            >
                {{-- TODO: Like, report and share the spot --}}
                <button type="button" class="btn btn-ghost">
                    <x-lucide-heart class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.common.actions.like') }}
                </button>
                <button type="button" class="btn btn-ghost">
                    <x-lucide-flag class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.common.actions.report') }}
                </button>
                <button type="button" class="btn btn-ghost">
                    <x-lucide-share-2 class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.common.actions.share') }}
                </button>

                @can('manage', $spot)
                    <button
                        type="button"
                        class="btn btn-ghost"
                        popovertarget="qr-code-popover"
                        style="anchor-name: --qr-code-anchor"
                    >
                        <x-lucide-qr-code class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.spots.show.qr_code.label') }}
                    </button>
                    <div
                        popover
                        id="qr-code-popover"
                        style="position-anchor: --qr-code-anchor"
                        class="dropdown dropdown-center bg-base-100 rounded-box w-72 p-2 shadow-sm"
                    >
                        <ul class="menu w-full p-0">
                            @foreach (['svg', 'png'] as $format)
                                <li>
                                    <a
                                        href="{{ route('spots.qr-code', ['spot' => $spot->uuid, 'format' => $format]) }}"
                                        download
                                    >
                                        <x-lucide-download class="h-4 w-4" aria-hidden="true" />
                                        {{ __("ui.spots.show.qr_code.download_{$format}") }}
                                    </a>
                                </li>
                            @endforeach
                            <li>
                                <a href="{{ route('spots.redirect', ['spot' => $spot->uuid]) }}" target="_blank">
                                    <x-lucide-external-link class="h-4 w-4" aria-hidden="true" />
                                    {{ __('ui.spots.show.qr_code.open') }}
                                </a>
                            </li>
                        </ul>

                        {{-- The link can be selected, and copied with the button, shown by resources/js/app.js --}}
                        <div class="flex flex-col gap-1 px-3 pt-2 pb-1">
                            <label
                                for="scan-link"
                                class="text-xs opacity-70"
                            >{{ __('ui.spots.show.qr_code.scan_link') }}</label>
                            <div class="join w-full">
                                <input
                                    id="scan-link"
                                    type="text"
                                    readonly
                                    class="input input-sm join-item w-full"
                                    value="{{ route('spots.redirect', ['spot' => $spot->uuid]) }}"
                                />
                                <button
                                    type="button"
                                    class="btn btn-sm join-item"
                                    hidden
                                    data-copy="scan-link"
                                    data-copied="{{ __('ui.spots.show.qr_code.copied') }}"
                                >
                                    <x-lucide-copy class="h-4 w-4" aria-hidden="true" />
                                    <span>{{ __('ui.spots.show.qr_code.copy') }}</span>
                                </button>
                            </div>
                            <span class="text-xs opacity-70">{{ __('ui.spots.show.qr_code.hint') }}</span>
                        </div>
                    </div>
                @endcan

                @can('update', $spot)
                    <a href="{{ route('spots.edit', ['spot' => $spot->uuid]) }}" class="btn btn-ghost">
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
                    >
                        <x-lucide-trash-2 class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.spots.show.actions.delete') }}
                    </x-confirm-modal>
                @endcan
            </div>
        </footer>
    </article>
</x-default-layout>
