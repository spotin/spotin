@extends('layouts.app')
@section('title', 'Mon profil')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Mon profil</h1>
    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Modifier</a>
</div>
<div class="card bg-base-100 shadow p-6">
    <div class="flex flex-col gap-4 text-sm">
        <div class="flex justify-between border-b border-base-200 pb-3">
            <span class="font-semibold text-base-content/60">Nom d'utilisateur</span>
            <span>{{ auth()->user()->username }}</span>
        </div>
        <div class="flex justify-between border-b border-base-200 pb-3">
            <span class="font-semibold text-base-content/60">Adresse e-mail</span>
            <span>{{ auth()->user()->email }}</span>
        </div>
        <div class="flex justify-between border-b border-base-200 pb-3">
            <span class="font-semibold text-base-content/60">Rôle</span>
            <span class="badge badge-neutral">{{ auth()->user()->role }}</span>
        </div>
        <div class="flex justify-between border-b border-base-200 pb-3">
            <span class="font-semibold text-base-content/60">Bio</span>
            <span>{{ auth()->user()->bio ?? '—' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="font-semibold text-base-content/60">Membre depuis</span>
            <span>{{ auth()->user()->created_at->format('d.m.Y') }}</span>
        </div>
    </div>
</div>
@endsection
