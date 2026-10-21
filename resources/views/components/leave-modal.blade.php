@props(['id', 'url'])

{{--
    A button that opens a dialog warning that the link leaves the application, with the address it leads to, before
    following it.
--}}
<button
    type="button"
    onclick="document.getElementById('{{ $id }}').showModal()"
    {{ $attributes->merge(['class' => 'btn btn-primary']) }}
>
    {{ $slot }}
    <x-lucide-external-link class="h-4 w-4" aria-hidden="true" />
</button>

<dialog id="{{ $id }}" class="modal" aria-labelledby="{{ $id }}-title">
    <div class="modal-box text-left whitespace-normal">
        <form method="dialog">
            <button
                class="btn btn-sm btn-circle btn-ghost absolute top-2 right-2"
                aria-label="{{ __('ui.common.actions.close') }}"
            >
                <x-lucide-x class="h-4 w-4" aria-hidden="true" />
            </button>
        </form>

        <h2 id="{{ $id }}-title" class="text-lg font-bold">
            {{ __('ui.common.leave.title', ['app' => config('app.name')]) }}
        </h2>
        {{-- Safe: the text is a translation, and the address and the name of the application are escaped --}}
        <p class="py-4">
            {!! __('ui.common.leave.warning', ['website' => '<strong class="break-all">'.e($url).'</strong>', 'app' => e(config('app.name'))]) !!}
        </p>

        <div class="modal-action">
            <button type="button" onclick="this.closest('dialog').close()" class="btn" autofocus>
                {{ __('ui.common.actions.cancel') }}
            </button>
            <a href="{{ $url }}" class="btn btn-primary" rel="noopener noreferrer">
                {{ __('ui.common.leave.continue') }}
                <x-lucide-external-link class="h-4 w-4" aria-hidden="true" />
            </a>
        </div>
    </div>

    {{-- Clicking outside the box closes the dialog --}}
    <form method="dialog" class="modal-backdrop">
        <button aria-label="{{ __('ui.common.actions.close') }}">{{ __('ui.common.actions.close') }}</button>
    </form>
</dialog>
