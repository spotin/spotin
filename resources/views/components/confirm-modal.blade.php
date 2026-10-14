@props(['id', 'action', 'title', 'warning', 'confirm', 'method' => 'DELETE'])

<button
    type="button"
    onclick="document.getElementById('{{ $id }}').showModal()"
    {{ $attributes->merge(['class' => 'btn btn-sm btn-error btn-outline']) }}
>
    {{ $slot }}
</button>

<dialog id="{{ $id }}" class="modal">
    <div class="modal-box text-left whitespace-normal">
        <form method="dialog">
            <button
                class="btn btn-sm btn-circle btn-ghost absolute top-2 right-2"
                aria-label="{{ __('ui.common.actions.close') }}"
            >
                <x-lucide-x class="h-4 w-4" aria-hidden="true" />
            </button>
        </form>

        <h2 class="text-lg font-bold">{{ $title }}</h2>
        <p class="py-4">{{ $warning }}</p>

        <form method="POST" action="{{ $action }}" class="modal-action">
            @csrf
            @method($method)
            <button type="button" onclick="this.closest('dialog').close()" class="btn" autofocus>
                {{ __('ui.common.actions.cancel') }}
            </button>
            <button type="submit" class="btn btn-error">{{ $confirm }}</button>
        </form>
    </div>

    {{-- Clicking outside the box closes the dialog --}}
    <form method="dialog" class="modal-backdrop">
        <button aria-label="{{ __('ui.common.actions.close') }}">{{ __('ui.common.actions.close') }}</button>
    </form>
</dialog>
