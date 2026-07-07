@extends('layouts.app')
@section('title', 'Nouveau token')
@section('content')
<h1 class="text-2xl font-bold mb-6">Nouveau token API</h1>
<div class="card bg-base-100 shadow p-6 max-w-sm">
    <form method="POST" action="{{ route('tokens.store') }}" class="flex flex-col gap-4">
        @csrf
        <fieldset class="fieldset">
            <label class="fieldset-label">Nom du token</label>
            <input name="name" class="input input-bordered w-full" placeholder="Ex: mon-app" required autofocus>
        </fieldset>
        <div class="flex gap-2 justify-end">
            <a href="{{ route('tokens.index') }}" class="btn btn-ghost">Annuler</a>
            <button class="btn btn-primary">Créer le token</button>
        </div>
    </form>
</div>
@endsection
