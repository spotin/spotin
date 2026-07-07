@extends('layouts.app')
@section('title', __('ui.profile.edit.title'))
@section('content')
<h1 class="text-2xl font-bold mb-6">{{ __('ui.profile.edit.title') }}</h1>
<div class="card bg-base-100 shadow p-6">
    <form method="POST" action="{{ route('profile.update') }}" class="flex flex-col gap-4">
        @csrf @method('PATCH')
        <fieldset class="fieldset">
            <label class="fieldset-label">{{ __('ui.profile.edit.username.label') }}</label>
            <input name="username" class="input input-bordered w-full"
                   placeholder="{{ __('ui.profile.edit.username.placeholder') }}"
                   value="{{ old('username', auth()->user()->username) }}" required>

            <label class="fieldset-label mt-3">{{ __('ui.profile.edit.bio.label') }}</label>
            <textarea name="bio" class="textarea textarea-bordered w-full" rows="3"
                      placeholder="{{ __('ui.profile.edit.bio.placeholder') }}">{{ old('bio', auth()->user()->bio) }}</textarea>

            <div class="divider text-xs">{{ __('ui.profile.edit.newPassword.label') }}</div>

            <label class="fieldset-label">{{ __('ui.profile.edit.newPassword.label') }}</label>
            <input type="password" name="new_password" class="input input-bordered w-full"
                   placeholder="{{ __('ui.profile.edit.newPassword.placeholder') }}">
            <label class="fieldset-label mt-3">{{ __('ui.auth.resetPassword.confirmPassword.label') }}</label>
            <input type="password" name="new_password_confirmation" class="input input-bordered w-full">

            <div class="divider text-xs"></div>

            <label class="fieldset-label">{{ __('ui.profile.edit.currentPassword.label') }} <span class="text-error">*</span></label>
            <input type="password" name="current_password" class="input input-bordered w-full"
                   placeholder="{{ __('ui.profile.edit.currentPassword.placeholder') }}" required>
        </fieldset>

        <div class="flex gap-2 justify-end">
            <a href="{{ route('profile.show') }}" class="btn btn-ghost">{{ __('ui.profile.edit.actions.view') }}</a>
            <button class="btn btn-primary">{{ __('ui.profile.edit.actions.update') }}</button>
        </div>
    </form>
</div>
@endsection
