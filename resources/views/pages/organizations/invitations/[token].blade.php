<?php

use App\Models\OrganizationInvitation;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\name;
use function Laravel\Folio\render;

name('organizations.invitations.show');

render(function (View $view, string $token) {
    Head::title(__('ui.organizations.invitations.show.title'))
        ->description(__('ui.organizations.invitations.show.description'));

    $user = auth()->user();

    if ($user && ! $user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

    $invitation = OrganizationInvitation::findActiveByToken($token);

    if (! $user) {
        // A visitor can create an account or log in, then come back here.
        if ($invitation) {
            session([OrganizationInvitation::SESSION_KEY => $token, 'url.intended' => url()->current()]);
        }

        return $view->with(['invitation' => $invitation, 'guest' => true]);
    }

    return $view->with(['invitation' => $invitation?->isFor($user) ? $invitation : null, 'guest' => false]);
});
?>

<x-default-layout>
    <h1>{{ __('ui.organizations.invitations.show.title') }}</h1>

    @if ($invitation)
        <p>
            {{ __('ui.organizations.invitations.show.explanation', ['organization' => $invitation->organization->name, 'role' => __('ui.organizations.common.forms.fields.role.options.' . $invitation->role->value)]) }}
        </p>

        @if ($guest)
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('register') }}" class="btn btn-primary">
                    {{ __('ui.organizations.invitations.show.actions.register') }}
                </a>
                <a href="{{ route('login') }}" class="btn">
                    {{ __('ui.organizations.invitations.show.actions.login') }}
                </a>
            </div>
        @else
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
        @endif
    @else
        <p role="alert" class="alert alert-error">
            <x-lucide-circle-alert class="h-6 w-6" />
            <span>{{ __('ui.organizations.invitations.feedback.invalid-invitation') }}</span>
        </p>
    @endif
</x-default-layout>
