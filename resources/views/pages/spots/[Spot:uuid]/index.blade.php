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

    {{--
        What only the managers of the spot see: what visitors do not know about it, and the tools to manage it. Its
        deletion is on its edit page, away from the actions of the visitors.
    --}}
    @can('manage', $spot)
        <section aria-labelledby="manage-title" class="card card-border card-sm bg-base-200 not-prose mb-8">
            <div class="card-body">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="manage-title" class="card-title">{{ __('ui.spots.show.manage.title') }}</h2>

                    <div class="card-actions">
                        <a href="{{ route('spots.edit', ['spot' => $spot->uuid]) }}" class="btn btn-sm">
                            <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                            {{ __('ui.spots.show.manage.actions.edit') }}
                        </a>

                        <x-qr-code-menu
                            :svg="route('spots.qr-code', ['spot' => $spot->uuid, 'format' => 'svg'])"
                            :png="route('spots.qr-code', ['spot' => $spot->uuid, 'format' => 'png'])"
                            :scan="route('spots.redirect', ['spot' => $spot->uuid])"
                            :hint="__('ui.spots.show.manage.qr_code_hint')"
                        />
                    </div>
                </div>

                {{--
                    What visitors do not know about the spot, as quiet badges, below the actions: their full explanation
                    is in their tooltip, and read by screen readers. Focusable, so that the keyboard shows the tooltip too.
                --}}
                @if ($spot->needs_setup || $spot->redirectsVisitors() || $spot->is_hidden)
                    <ul class="flex flex-wrap gap-2">
                        @if ($spot->needs_setup)
                            <li class="tooltip" data-tip="{{ __('ui.spots.show.manage.notes.needs_setup') }}">
                                <span tabindex="0" class="badge badge-sm badge-outline border-base-content/30">
                                    <x-lucide-wrench class="h-3 w-3" aria-hidden="true" />
                                    {{ __('ui.spots.common.needs_setup') }}
                                    <span class="sr-only">: {{ __('ui.spots.show.manage.notes.needs_setup') }}</span>
                                </span>
                            </li>
                        @endif

                        @if ($spot->redirectsVisitors())
                            @php($host = parse_url($spot->website, PHP_URL_HOST) ?? $spot->website)
                            <li
                                class="tooltip"
                                data-tip="{{ __('ui.spots.show.manage.notes.redirect', ['website' => $host]) }}"
                            >
                                <span tabindex="0" class="badge badge-sm badge-outline border-base-content/30">
                                    <x-lucide-external-link class="h-3 w-3" aria-hidden="true" />
                                    {{ __('ui.spots.show.manage.badges.redirect') }}
                                    <span class="sr-only">: {{ __('ui.spots.show.manage.notes.redirect', ['website' => $host]) }}</span>
                                </span>
                            </li>
                        @endif

                        @if ($spot->is_hidden)
                            <li
                                class="tooltip"
                                data-tip="{{ __('ui.spots.show.manage.notes.hidden', ['organization' => $spot->organization->name]) }}"
                            >
                                <span tabindex="0" class="badge badge-sm badge-outline border-base-content/30">
                                    <x-lucide-eye-off class="h-3 w-3" aria-hidden="true" />
                                    {{ __('ui.spots.common.hidden') }}
                                    <span class="sr-only">
                                        : {{ __('ui.spots.show.manage.notes.hidden', ['organization' => $spot->organization->name]) }}
                                    </span>
                                </span>
                            </li>
                        @endif
                    </ul>
                @endif

                {{-- Who created the spot, and when: of use to its managers, not to its visitors --}}
                <p class="text-sm opacity-70">
                    @if ($spot->creator)
                        {{ __('ui.spots.show.manage.created_by', ['creator' => $spot->creator->name, 'date' => $spot->created_at->isoFormat('LL')]) }}
                    @else
                        {{ __('ui.spots.show.manage.created', ['date' => $spot->created_at->isoFormat('LL')]) }}
                    @endif
                </p>
            </div>
        </section>
    @endcan

    {{--
        The spot: its name, the organization behind it, the actions of its visitors, its description, and the way to its
        website, in the page as its organization
    --}}
    <article class="flex flex-col gap-2">
        {{-- Spaced from the description, which may start with a heading of its own --}}
        <header class="not-prose mb-4 flex flex-col gap-1">
            <h1 class="max-w-full text-3xl font-extrabold break-words">{{ $spot->displayName() }}</h1>

            {{-- The organization and the actions on one row, as the box of the managers; below each other when narrow --}}
            <div class="flex flex-wrap items-center justify-between gap-2">
                {{-- Who stands behind the spot, which its visitors may want to know before trusting it --}}
                <p class="text-sm opacity-80">
                    {!!
                        __('ui.spots.show.by', [
                            'organization' => '<a href="'.e(route('organizations.show', ['organization' => $spot->organization->uuid])).'" class="link link-hover">'.e($spot->organization->name).'</a>',
                        ])
                    !!}
                </p>

                {{-- Lighter than the tools of the managers --}}
                <div role="group" aria-label="{{ __('ui.common.actions.label') }}" class="flex flex-wrap gap-1">
                    {{-- TODO: Like, report and share the spot --}}
                    <button type="button" class="btn btn-ghost btn-sm">
                        <x-lucide-heart class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.common.actions.like') }}
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm">
                        <x-lucide-flag class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.common.actions.report') }}
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm">
                        <x-lucide-share-2 class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.common.actions.share') }}
                    </button>
                </div>
            </div>
        </header>

        @if (filled($spot->description))
            {{--
                Safe: HTML is stripped and unsafe links are dropped when rendering the Markdown. Its own `prose` removes the
                margin above its first element, and its images are kept to a reasonable height, centered.
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
    </article>
</x-default-layout>
