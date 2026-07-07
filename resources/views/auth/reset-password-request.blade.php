@extends('layouts.app')
@section('title', __('ui.auth.resetPasswordRequest.title'))
@section('content')
<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold mb-6">{{ __('ui.auth.resetPasswordRequest.title') }}</h1>
    <div class="card bg-base-100 shadow p-6">
        <form method="POST" action="{{ route('auth.reset-password-request') }}" class="flex flex-col gap-4">
            @csrf
            <fieldset class="fieldset">
                <label class="fieldset-label">{{ __('ui.auth.resetPasswordRequest.email.label') }}</label>
                <input type="email" name="email" class="input input-bordered w-full"
                       placeholder="{{ __('ui.auth.resetPasswordRequest.email.placeholder') }}"
                       required autofocus>
            </fieldset>
            <button class="btn btn-primary w-full">{{ __('ui.auth.resetPasswordRequest.button') }}</button>
        </form>
        <div class="mt-4 text-center">
            <a href="{{ route('auth.login') }}" class="link text-sm">
                {{ __('ui.auth.resetPasswordRequest.rememberedPassword.label') }}
                {{ __('ui.auth.resetPasswordRequest.rememberedPassword.login') }}
            </a>
        </div>
    </div>
</div>
@endsection
