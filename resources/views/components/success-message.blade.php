@props(['closable' => true])

{{--
    The message of an action that succeeded, for the request after it. Shown by `x-default-layout` above the page, or by
    a page that places it itself, next to what the action changed. Closable, unless it goes with something that stays,
    such as a new token. With `undo` in the session, the address that undoes the action, such as restoring what was just
    deleted: a button to do it at once.
--}}
@if (session('success'))
    <x-alert type="success" :closable="$closable" class="alert-vertical sm:alert-horizontal">
        {{ session('success') }}

        @if (session('undo'))
            <x-slot:actions>
                <button type="submit" form="undo-form" class="btn btn-sm">
                    <x-lucide-undo-2 class="h-4 w-4" aria-hidden="true" />
                    {{ __('ui.common.actions.undo') }}
                </button>
            </x-slot:actions>
        @endif
    </x-alert>

    {{-- Out of the alert, a paragraph, which cannot hold a form: its button sends it by its id --}}
    @if (session('undo'))
        <form id="undo-form" method="POST" action="{{ session('undo') }}" hidden>@csrf</form>
    @endif
@endif
