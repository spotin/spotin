@extends('layouts.app')
@section('title', __('ui.profile.view.title'))
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">{{ __('ui.profile.view.title') }}</h1>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">{{ __('ui.profile.edit.actions.update') }}</a>
</div>
<div class="card bg-base-100 shadow p-6">
    <div class="flex flex-col gap-4 text-sm">
        <div class="flex justify-between border-b border-base-200 pb-3">
            <span class="font-semibold text-base-content/60">{{ __('ui.profile.edit.username.label') }}</span>
            <span>{{ auth()->user()->username }}</span>
        </div>
        <div class="flex justify-between border-b border-base-200 pb-3">
            <span class="font-semibold text-base-content/60">{{ __('ui.profile.edit.email.label') }}</span>
            <span>{{ auth()->user()->email }}</span>
        </div>
        <div class="flex justify-between border-b border-base-200 pb-3">
            <span class="font-semibold text-base-content/60">{{ __('ui.profile.edit.bio.label') }}</span>
            <span>{{ auth()->user()->bio ?? '—' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-semibold text-base-content/60">{{ __('ui.profile.view.memberSince') }}</span>
            <span>{{ auth()->user()->created_at->format('d.m.Y') }}</span>
        </div>
    </div>
</div>
@endsection
