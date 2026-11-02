<?php

use App\Models\Invitation;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('invitations.show');

render(function (View $view, string $token) {
    Head::title(__('ui.invitations.show.title'))
        ->description(__('ui.invitations.show.description'));

    $user = auth()->user();

    // People with an account accept their invitations from the list of pending invitations.
    if ($user) {
        return $user->hasVerifiedEmail()
            ? redirect()->route('invitations.index')
            : redirect()->route('verification.notice');
    }

    $invitation = Invitation::findActiveByToken($token);

    // A visitor can create an account or log in, then come back here.
    if ($invitation) {
        session([Invitation::SESSION_KEY => $token, 'url.intended' => url()->current()]);
    }

    return $view->with('invitation', $invitation);
});
?>

<x-default-layout>
    <h1>{{ __('ui.invitations.show.title') }}</h1>

    @if ($invitation)
        <p>
            {{ __('ui.invitations.show.explanation', ['organization' => $invitation->organization->name, 'role' => __('ui.organizations.common.forms.fields.role.options.' . $invitation->role->value)]) }}
        </p>

        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('register') }}" class="btn btn-primary">
                {{ __('ui.invitations.show.actions.register') }}
            </a>
            <a href="{{ route('login') }}" class="btn"> {{ __('ui.invitations.show.actions.login') }} </a>
        </div>
    @else
        <x-alert type="error">{{ __('ui.invitations.feedback.invalid_invitation') }}</x-alert>
    @endif
</x-default-layout>
