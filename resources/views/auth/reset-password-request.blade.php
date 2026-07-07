@extends('layouts.app')
@section('title', 'Mot de passe oublié')
@section('content')
<div class="max-w-sm mx-auto">
    <h1 class="text-2xl font-bold mb-6">Mot de passe oublié</h1>
    <div class="card bg-base-100 shadow p-6">
        <p class="text-sm text-base-content/70 mb-4">Entrez votre adresse e-mail pour recevoir un lien de réinitialisation.</p>
        <form method="POST" action="{{ route('auth.reset-password-request') }}" class="flex flex-col gap-4">
            @csrf
            <fieldset class="fieldset">
                <label class="fieldset-label">Adresse e-mail</label>
                <input type="email" name="email" class="input input-bordered w-full" required autofocus>
            </fieldset>
            <button class="btn btn-primary w-full">Envoyer le lien</button>
        </form>
        <div class="mt-4 text-center">
            <a href="{{ route('auth.login') }}" class="link text-sm">Retour à la connexion</a>
        </div>
    </div>
</div>
@endsection
