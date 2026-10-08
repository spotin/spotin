<?php

use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Head\Facades\Head;

use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

middleware(['auth']);
name('verification.notice');

Head::title(__('ui.auth.verify_email.title'))
    ->description(__('ui.auth.verify_email.description'));

render(function (View $view, Request $request) {
    return $request->user()->hasVerifiedEmail()
        ? redirect()->intended(config('fortify.home'))
        : $view;
});
?>

<x-default-layout>
    <div class="prose max-w-none">
        <h1>{{ __('ui.auth.verify_email.title') }}</h1>
        <p>{{ __('ui.auth.verify_email.explanation') }}</p>

        @if (session('status') == 'verification-link-sent')
            <p role="alert" class="alert alert-success">
                <x-lucide-check-circle class="h-6 w-6" />
                <span>{{ __('ui.auth.verify_email.form.feedback.success') }}</span>
            </p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="btn btn-block">{{ __('ui.auth.verify_email.form.actions.resend') }}</button>
        </form>
    </div>
</x-default-layout>
