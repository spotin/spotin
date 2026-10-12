<?php

use App\Models\OrganizationInvitation;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth', 'verified']);
name('organizations.invitations.show');

render(function (View $view, string $token) {
    Head::title(__('ui.organizations.invitations.show.title'))
        ->description(__('ui.organizations.invitations.show.description'));

    $invitation = OrganizationInvitation::findActiveByToken($token);

    return $view->with([
        'invitation' => $invitation?->isFor(auth()->user()) ? $invitation : null,
    ]);
});
?>

<x-default-layout>
    <h1>{{ __('ui.organizations.invitations.show.title') }}</h1>

    @if ($invitation)
        <p>
            {{ __('ui.organizations.invitations.show.explanation', ['organization' => $invitation->organization->name, 'role' => __('ui.organizations.common.forms.fields.role.options.' . $invitation->role->value)]) }}
        </p>

        <form method="POST" action="{{ route('organizations.invitations.accept', $token) }}">
            @csrf

            @error('invitation')
                <p role="alert" class="alert alert-error mb-4">
                    <x-lucide-circle-alert class="h-6 w-6" />
                    <span>{{ $message }}</span>
                </p>
            @enderror

            <button type="submit" class="btn btn-block">
                {{ __('ui.organizations.invitations.show.actions.accept') }}
            </button>
        </form>
    @else
        <p role="alert" class="alert alert-error">
            <x-lucide-circle-alert class="h-6 w-6" />
            <span>{{ __('ui.organizations.invitations.show.feedback.invalid') }}</span>
        </p>
    @endif
</x-default-layout>
