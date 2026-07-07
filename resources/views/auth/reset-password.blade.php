@extends('layouts.app')
@section('title', 'Réinitialiser le mot de passe')
@section('content')
<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold mb-6">Nouveau mot de passe</h1>
    <div class="card bg-base-100 shadow p-6">
        <form method="POST" action="{{ route('auth.reset-password') }}" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <fieldset class="fieldset">
                <label class="fieldset-label">Adresse e-mail</label>
                <input type="email" name="email" class="input input-bordered w-full" value="{{ $email ?? old('email') }}" required>
                <label class="fieldset-label mt-3">Nouveau mot de passe</label>
                <input type="password" name="password" class="input input-bordered w-full" required>
                <label class="fieldset-label mt-3">Confirmation</label>
                <input type="password" name="password_confirmation" class="input input-bordered w-full" required>
            </fieldset>
            <button class="btn btn-primary w-full">Réinitialiser</button>
        </form>
    </div>
</div>
@endsection
