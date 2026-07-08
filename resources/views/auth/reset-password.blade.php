@extends('layouts.app')
@section('title', __('ui.auth.resetPassword.title'))
@section('content')
<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold mb-6">{{ __('ui.auth.resetPassword.title') }}</h1>
    <div class="card bg-base-100 shadow p-6">
        <form method="POST" action="{{ route('auth.reset-password') }}" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <fieldset class="fieldset">
                <label class="fieldset-label">{{ __('ui.auth.login.email.label') }}</label>
                <input type="email" name="email" class="input input-bordered w-full"
                       value="{{ $email ?? old('email') }}" required>
                <label class="fieldset-label mt-3">{{ __('ui.auth.resetPassword.newPassword.label') }}</label>
                <input type="password" name="password" class="input input-bordered w-full"
                       placeholder="{{ __('ui.auth.resetPassword.newPassword.placeholder') }}" required>
                <label class="fieldset-label mt-3">{{ __('ui.auth.resetPassword.confirmPassword.label') }}</label>
                <input type="password" name="password_confirmation" class="input input-bordered w-full"
                       placeholder="{{ __('ui.auth.resetPassword.confirmPassword.placeholder') }}" required>
            </fieldset>
            <button class="btn btn-primary w-full">{{ __('ui.auth.resetPassword.button') }}</button>
        </form>
    </div>
</div>
@endsection
