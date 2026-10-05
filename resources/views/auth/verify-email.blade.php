<x-default-layout>
    <h1>{{ __('ui.auth.verify_email.title') }}</h1>
    <p>{{ __('ui.auth.verify_email.explanation') }}</p>

    @if (session('status') == 'verification-link-sent')
        <p role="alert" class="alert alert-success">
            <x-lucide-check-circle class="h-6 w-6" />
            <span>{{ __('ui.auth.verify_email.success') }}</span>
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf

        <button type="submit" class="btn btn-block">{{ __('ui.auth.verify_email.form.actions.resend') }}</button>
    </form>
</x-default-layout>
