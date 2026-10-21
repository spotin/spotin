<?php

use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth']);
name('verification.notice');

render(function (View $view, Request $request) {
    Head::title(__('ui.auth.verify_email.title'))
        ->description(__('ui.auth.verify_email.description'));

    return $request->user()->hasVerifiedEmail()
        ? redirect()->intended(config('fortify.home'))
        : $view;
});
?>

<x-default-layout>
    <h1>{{ __('ui.auth.verify_email.title') }}</h1>
    <p>{{ __('ui.auth.verify_email.explanation') }}</p>

    @if (session('status') == 'verification-link-sent')
        <x-alert type="success" closable>{{ __('ui.auth.feedback.verification_link_sent') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <button type="submit" class="btn btn-block">{{ __('ui.auth.verify_email.form.actions.resend') }}</button>
    </form>
</x-default-layout>
