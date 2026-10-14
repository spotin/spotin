@props(['organization'])

@php($modalId = 'leave-organization-'.$organization->uuid.'-modal')

<button
    type="button"
    onclick="document.getElementById('{{ $modalId }}').showModal()"
    {{ $attributes->merge(['class' => 'btn btn-sm btn-error btn-outline']) }}
>
    {{ $slot->isEmpty() ? __('ui.organizations.members.index.table.actions.leave') : $slot }}
</button>

<dialog id="{{ $modalId }}" class="modal">
    <div class="modal-box text-left whitespace-normal">
        <form method="dialog">
            <button
                class="btn btn-sm btn-circle btn-ghost absolute top-2 right-2"
                aria-label="{{ __('ui.common.actions.close') }}"
            >
                <x-lucide-x class="h-4 w-4" aria-hidden="true" />
            </button>
        </form>

        <h2 class="text-lg font-bold">{{ __('ui.organizations.members.leave.title') }}</h2>
        <p class="py-4">{{ __('ui.organizations.members.leave.warning') }}</p>

        <form
            method="POST"
            action="{{ route('organizations.leave', ['organization' => $organization->uuid]) }}"
            class="modal-action"
        >
            @csrf
            @method('DELETE')
            <button type="button" onclick="this.closest('dialog').close()" class="btn" autofocus>
                {{ __('ui.common.actions.cancel') }}
            </button>
            <button type="submit" class="btn btn-error">{{ __('ui.organizations.members.leave.confirm') }}</button>
        </form>
    </div>

    <form method="dialog" class="modal-backdrop">
        <button aria-label="{{ __('ui.common.actions.close') }}">{{ __('ui.common.actions.close') }}</button>
    </form>
</dialog>
