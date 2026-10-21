@props(['organization', 'spotsCount', 'membersCount' => null, 'invitationsCount' => null])

{{--
    The organization on its page, under the map of its spots, as a spot on its page: its name, its details leading to
    their pages, and its actions.
--}}
<article {{ $attributes }}>
    <div class="flex flex-col gap-2">
        <header class="flex flex-col items-center gap-2 text-center">
            <h1 class="card-title m-0! max-w-full justify-center text-3xl font-extrabold break-words">
                {{ $organization->name }}
            </h1>

            {{--
                The details on one line, each with its icon, leading to their page: the names are read by screen
                readers
            --}}
            <dl class="not-prose flex flex-wrap justify-center gap-x-4 gap-y-1 text-sm">
                <div class="flex items-center gap-1">
                    <dt class="sr-only">{{ __('ui.organizations.show.sections.spots.title') }}</dt>
                    <x-lucide-map-pin class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                    <dd>
                        <a
                            href="{{ route('organizations.spots.index', ['organization' => $organization->uuid]) }}"
                            class="link link-hover"
                        >
                            {{ trans_choice('ui.organizations.show.sections.spots.summary', $spotsCount) }}
                        </a>
                    </dd>
                </div>
                @can('viewMembers', $organization)
                    <div class="flex items-center gap-1">
                        <dt class="sr-only">{{ __('ui.organizations.show.sections.members.title') }}</dt>
                        <x-lucide-users class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                        <dd>
                            <a
                                href="{{ route('organizations.members.index', ['organization' => $organization->uuid]) }}"
                                class="link link-hover"
                            >
                                {{ trans_choice('ui.organizations.show.sections.members.summary', $membersCount) }}
                            </a>
                        </dd>
                    </div>
                @endcan
                @can('manageMembers', $organization)
                    <div class="flex items-center gap-1">
                        <dt class="sr-only">{{ __('ui.organizations.show.sections.invitations.title') }}</dt>
                        <x-lucide-mail class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                        <dd>
                            <a
                                href="{{ route('organizations.invitations.index', ['organization' => $organization->uuid]) }}"
                                class="link link-hover"
                            >
                                {{ trans_choice('ui.organizations.show.sections.invitations.summary', $invitationsCount) }}
                            </a>
                        </dd>
                    </div>
                @endcan
                <div class="flex items-center gap-1">
                    <dt class="sr-only">{{ __('ui.organizations.show.fields.created') }}</dt>
                    <x-lucide-calendar class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                    <dd>{{ $organization->created_at->isoFormat('LL') }}</dd>
                </div>
            </dl>
        </header>
    </div>

    {{-- The actions on the organization: on one line when they fit, on more otherwise --}}
    <footer class="mt-6">
        <div
            role="group"
            aria-label="{{ __('ui.common.actions.label') }}"
            class="not-prose flex flex-wrap justify-center gap-2"
        >
            {{-- TODO: Like, report and share the organization --}}
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

            @can('update', $organization)
                <a
                    href="{{ route('organizations.edit', ['organization' => $organization->uuid]) }}"
                    class="btn btn-ghost"
                >
                    <x-lucide-pencil class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.organizations.show.actions.edit') }}
                </a>
            @endcan

            @can('delete', $organization)
                <x-confirm-modal
                    :id="'delete-organization-'.$organization->uuid.'-modal'"
                    :action="route('organizations.destroy', ['organization' => $organization->uuid])"
                    :title="__('ui.organizations.delete.title')"
                    :warning="__('ui.organizations.delete.warning')"
                    :confirm="__('ui.organizations.delete.actions.submit')"
                    :small="false"
                    :ghost="true"
                >
                    <x-lucide-trash-2 class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.organizations.show.actions.delete') }}
                </x-confirm-modal>
            @endcan
        </div>
    </footer>
</article>
