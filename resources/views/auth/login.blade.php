@extends('layouts.app')
@section('title', 'Connexion')
@section('content')
<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold mb-6">Connexion</h1>
    <div class="card bg-base-100 shadow p-6">
        <form method="POST" action="{{ route('auth.login') }}" class="flex flex-col gap-4">
            @csrf
            <fieldset class="fieldset">
                <label class="fieldset-label">Adresse e-mail</label>
                <input type="email" name="email" class="input input-bordered w-full" value="{{ old('email') }}" required autofocus>
                <label class="fieldset-label mt-3">Mot de passe</label>
                <input type="password" name="password" class="input input-bordered w-full" required>
            </fieldset>
            <button class="btn btn-primary w-full">Se connecter</button>
        </form>
        <div class="divider text-xs">ou</div>
        <div class="text-sm text-center flex flex-col gap-1">
            <a href="{{ route('auth.register') }}" class="link">Pas encore de compte ? S'inscrire</a>
            <a href="{{ route('auth.reset-password-request') }}" class="link link-neutral">Mot de passe oublié ?</a>
        </div>
    </div>
</div>
@endsection
