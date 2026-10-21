<?php

use App\Enums\TokenAbility;
use App\Support\Breadcrumbs;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('settings.developer');

render(function (View $view) {
    $user = auth()->user();

    Head::title(__('ui.settings.developer.index.title'))
        ->description(__('ui.settings.developer.index.description'));

    $breadcrumbs = Breadcrumbs::developer();

    return $view->with([
        'breadcrumbs' => $breadcrumbs,
        'tokens' => $user->tokens()->latest('id')->get(),
    ]);
});
?>

<x-default-layout :breadcrumbs="$breadcrumbs">
    <h1>{{ __('ui.settings.developer.index.title') }}</h1>

    <p>{{ __('ui.settings.developer.index.warning') }}</p>

    <p>
        {!!
            __('ui.settings.developer.index.access_the_api', [
                'ui' => '<a href="'.e(route('api.ui')).'" target="_blank">/api</a>',
                'document' => '<a href="'.e(route('api.document')).'" target="_blank">/api.json</a>',
            ])
        !!}
    </p>

    <div class="mb-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
        <h2 class="m-0! min-w-0 break-words">{{ __('ui.settings.developer.index.your_tokens') }}</h2>

        <a href="{{ route('settings.developer.tokens.create') }}" class="btn btn-primary btn-sm shrink-0">
            {{ __('ui.settings.developer.create.title') }}
        </a>
    </div>

    @if (session('new-token'))
        <code class="bg-base-100 rounded-field not-prose mb-4 block w-full p-3 break-all select-all">{{ session('new-token') }}</code>
    @endif

    @if ($tokens->isEmpty())
        <p>{{ __('ui.settings.developer.index.table.empty') }}</p>
    @else
        <div class="not-prose rounded-box border-base-content/5 bg-base-100 overflow-x-auto border">
            <table class="table-zebra table-sm table">
                <thead>
                    <tr>
                        <th>{{ __('ui.settings.developer.index.table.columns.name') }}</th>
                        <th>{{ __('ui.settings.developer.index.table.columns.abilities') }}</th>
                        <th>{{ __('ui.settings.developer.index.table.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tokens as $token)
                        <tr>
                            <td>
                                <div class="break-words">{{ $token->name }}</div>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs">
                                    <span class="opacity-60">
                                        {{ $token->last_used_at ? __('ui.settings.developer.index.table.last_used', ['when' => $token->last_used_at->diffForHumans()]) : __('ui.settings.developer.index.table.never_used') }}
                                    </span>
                                    <span
                                        class="opacity-60"
                                        @if ($token->expires_at) title="{{ $token->expires_at->isoFormat('LLL') }}" @endif
                                    >
                                        {{ $token->expires_at ? __('ui.settings.developer.index.table.expires', ['date' => $token->expires_at->isoFormat('LL')]) : __('ui.settings.developer.index.table.never_expires') }}
                                    </span>
                                    @if ($token->expires_at?->isPast())
                                        <span class="badge badge-warning badge-sm">
                                            {{ __('ui.settings.developer.index.table.expired') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($token->abilities as $value)
                                        @php($ability = TokenAbility::tryFrom($value))
                                        <code
                                            class="badge badge-sm not-prose font-mono"
                                            @if ($ability) title="{{ $ability->label() }}" @endif
                                        >
                                            {{ $value }}
                                        </code>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <x-confirm-modal
                                    :id="'revoke-token-'.$token->id.'-modal'"
                                    :action="route('settings.developer.tokens.destroy', ['token' => $token->id])"
                                    :title="__('ui.settings.developer.index.table.actions.revoke.title')"
                                    :warning="__('ui.settings.developer.index.table.actions.revoke.warning')"
                                    :confirm="__('ui.settings.developer.index.table.actions.revoke.actions.submit')"
                                    class="w-full sm:w-auto"
                                >
                                    {{ __('ui.settings.developer.index.table.actions.revoke.label') }}
                                </x-confirm-modal>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-default-layout>
