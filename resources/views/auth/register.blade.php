@extends('layouts.app')
@section('title', 'Inscription')
@section('content')
<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold mb-6">Inscription</h1>
    <div class="card bg-base-100 shadow p-6">
        <form method="POST" action="{{ route('auth.register') }}" class="flex flex-col gap-4">
            @csrf
            <fieldset class="fieldset">
                <label class="fieldset-label">Nom d'utilisateur</label>
                <input name="username" class="input input-bordered w-full" value="{{ old('username') }}" required autofocus>
                <label class="fieldset-label mt-3">Adresse e-mail</label>
                <input type="email" name="email" class="input input-bordered w-full" value="{{ old('email') }}" required>
                <label class="fieldset-label mt-3">Mot de passe</label>
                <input type="password" name="password" class="input input-bordered w-full" required>
                <label class="fieldset-label mt-3">Confirmation du mot de passe</label>
                <input type="password" name="password_confirmation" class="input input-bordered w-full" required>
            </fieldset>
            <button class="btn btn-primary w-full">Créer le compte</button>
        </form>
        <div class="divider text-xs">ou</div>
        <a href="{{ route('auth.login') }}" class="link text-sm text-center block">Déjà un compte ? Se connecter</a>
    </div>
</div>
@endsection
