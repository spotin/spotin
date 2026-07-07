@extends('layouts.app')
@section('title', __('ui.auth.register.title'))
@section('content')
<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold mb-6">{{ __('ui.auth.register.title') }}</h1>
    <div class="card bg-base-100 shadow p-6">
        <form method="POST" action="{{ route('auth.register') }}" class="flex flex-col gap-4">
            @csrf
            <fieldset class="fieldset">
                <label class="fieldset-label">{{ __('ui.auth.register.username.label') }}</label>
                <input name="username" class="input input-bordered w-full"
                       placeholder="{{ __('ui.auth.register.username.placeholder') }}"
                       value="{{ old('username') }}" required autofocus>
                <p class="text-xs text-base-content/50">{{ __('ui.auth.register.username.warning') }}</p>
                <label class="fieldset-label mt-3">{{ __('ui.auth.register.email.label') }}</label>
                <input type="email" name="email" class="input input-bordered w-full"
                       placeholder="{{ __('ui.auth.register.email.placeholder') }}"
                       value="{{ old('email') }}" required>
                <label class="fieldset-label mt-3">{{ __('ui.auth.login.password.label') }}</label>
                <input type="password" name="password" class="input input-bordered w-full" required>
                <label class="fieldset-label mt-3">{{ __('ui.auth.resetPassword.confirmPassword.label') }}</label>
                <input type="password" name="password_confirmation" class="input input-bordered w-full" required>
            </fieldset>
            <button class="btn btn-primary w-full">{{ __('ui.auth.register.button') }}</button>
        </form>
        <div class="divider text-xs">ou</div>
        <a href="{{ route('auth.login') }}" class="link text-sm text-center block">
            {{ __('ui.auth.login.haveAccount.label') }}
            {{ __('ui.auth.login.haveAccount.login') }}
        </a>
    </div>
</div>
@endsection
