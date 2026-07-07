@extends('layouts.app')
@section('title', 'Tokens API')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Tokens API</h1>
    <a href="{{ route('tokens.create') }}" class="btn btn-primary btn-sm">Nouveau token</a>
</div>
<div class="card bg-base-100 shadow overflow-x-auto">
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Créé le</th>
                <th>Dernière utilisation</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tokens as $token)
                <tr class="hover">
                    <td class="font-medium">{{ $token->name }}</td>
                    <td class="text-sm text-base-content/60">{{ $token->created_at->format('d.m.Y') }}</td>
                    <td class="text-sm text-base-content/60">{{ $token->last_used_at?->format('d.m.Y') ?? '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('tokens.destroy', $token->id) }}" onsubmit="return confirm('Révoquer ce token ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-xs text-error">Révoquer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-base-content/50 py-8">Aucun token API.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@if (session('plain_token'))
    <div role="alert" class="alert alert-success mt-4">
        <div>
            <p class="font-bold">Copiez ce token maintenant — il ne sera plus affiché :</p>
            <code class="text-xs break-all bg-success/20 px-2 py-1 rounded mt-1 block">{{ session('plain_token') }}</code>
        </div>
    </div>
@endif
@endsection
