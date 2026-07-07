@extends('layouts.app')
@section('title', __('ui.auth.login.title'))
@section('content')
<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold mb-6">{{ __('ui.auth.login.title') }}</h1>
    <div class="card bg-base-100 shadow p-6">
        <form method="POST" action="{{ route('auth.login') }}" class="flex flex-col gap-4">
            @csrf
            <fieldset class="fieldset">
                <label class="fieldset-label">{{ __('ui.auth.login.email.label') }}</label>
                <input type="email" name="email" class="input input-bordered w-full"
                       placeholder="{{ __('ui.auth.login.email.placeholder') }}"
                       value="{{ old('email') }}" required autofocus>
                <label class="fieldset-label mt-3">{{ __('ui.auth.login.password.label') }}</label>
                <input type="password" name="password" class="input input-bordered w-full"
                       placeholder="{{ __('ui.auth.login.password.placeholder') }}" required>
            </fieldset>
            <button class="btn btn-primary w-full">{{ __('ui.auth.login.button') }}</button>
        </form>
        <div class="divider text-xs">ou</div>
        <div class="text-sm text-center flex flex-col gap-1">
            <a href="{{ route('auth.register') }}" class="link">
                {{ __('ui.auth.register.noAccount.label') }}
                {{ __('ui.auth.register.noAccount.register') }}
            </a>
            <a href="{{ route('auth.reset-password-request') }}" class="link link-neutral">
                {{ __('ui.auth.resetPasswordRequest.forgotPassword.label') }}
            </a>
        </div>
    </div>
</div>
@endsection
