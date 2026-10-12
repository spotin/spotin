<?php

use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('organizations.index');

render(function (View $view) {
    Head::title(__('ui.organizations.index.title'))
        ->description(__('ui.organizations.index.description'));

    return $view->with('organizations', auth()->user()->organizations()->orderBy('name')->paginate(25));
});
?>

<x-default-layout>
    <h1>{{ __('ui.organizations.index.title') }}</h1>

    @if (session('status') === 'organization-left')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.organizations.members.feedback.organization-left') }}</span>
        </p>
    @endif

    @if ($organizations->isEmpty())
        <p>{{ __('ui.organizations.index.empty') }}</p>
    @else
        <div class="not-prose grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($organizations as $organization)
                <a
                    href="{{ route('organizations.show', ['organization' => $organization->uuid]) }}"
                    class="card border-base-content/5 bg-base-100 hover:bg-base-200 focus-visible:outline-primary border transition-colors focus-visible:outline-2"
                >
                    <div class="card-body flex-row items-center gap-4">
                        <x-lucide-user-group class="h-8 w-8 shrink-0 opacity-70" aria-hidden="true" />
                        <div class="min-w-0">
                            <h2 class="card-title">
                                <span class="line-clamp-2 break-words" title="{{ $organization->name }}">
                                    {{ $organization->name }}
                                </span>
                            </h2>
                            <p class="text-sm opacity-70">
                                {{ __('ui.organizations.common.forms.fields.role.options.' . $organization->pivot->role->value) }}
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{ $organizations->links() }}
    @endif
</x-default-layout>
