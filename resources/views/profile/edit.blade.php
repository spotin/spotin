@extends('layouts.app')
@section('title', 'Modifier le profil')
@section('content')
<h1 class="text-2xl font-bold mb-6">Modifier le profil</h1>
<div class="card bg-base-100 shadow p-6">
    <form method="POST" action="{{ route('profile.update') }}" class="flex flex-col gap-4">
        @csrf @method('PATCH')
        <fieldset class="fieldset">
            <label class="fieldset-label">Nom d'utilisateur</label>
            <input name="username" class="input input-bordered w-full" value="{{ old('username', auth()->user()->username) }}" required>

            <label class="fieldset-label mt-3">Bio</label>
            <textarea name="bio" class="textarea textarea-bordered w-full" rows="3">{{ old('bio', auth()->user()->bio) }}</textarea>

            <div class="divider text-xs">Changer le mot de passe (optionnel)</div>

            <label class="fieldset-label">Nouveau mot de passe</label>
            <input type="password" name="new_password" class="input input-bordered w-full" placeholder="Laisser vide pour ne pas changer">
            <label class="fieldset-label mt-3">Confirmation</label>
            <input type="password" name="new_password_confirmation" class="input input-bordered w-full">

            <div class="divider text-xs">Confirmation requise</div>

            <label class="fieldset-label">Mot de passe actuel <span class="text-error">*</span></label>
            <input type="password" name="current_password" class="input input-bordered w-full" required>
        </fieldset>

        <div class="flex gap-2 justify-end">
            <a href="{{ route('profile.show') }}" class="btn btn-ghost">Annuler</a>
            <button class="btn btn-primary">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
