@props(['id', 'action', 'title', 'warning', 'confirm', 'method' => 'DELETE', 'small' => true, 'ghost' => false])

{{-- `ghost` shows the button without its outline, red on a transparent background, such as among other ghost buttons --}}

<button
    type="button"
    onclick="document.getElementById('{{ $id }}').showModal()"
    {{ $attributes->class(['btn btn-error', 'btn-outline' => ! $ghost, 'btn-ghost' => $ghost, 'btn-sm' => $small]) }}
>
    {{ $slot }}
</button>

{{-- At the end of the page: the button alone stays here, so it can be joined to others --}}
@push('dialogs')
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
@endpush
