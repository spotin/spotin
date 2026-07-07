@extends('layouts.app')
@section('title', 'Mes spots')
@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Mes spots</h1>
    <a href="{{ route('spots.create') }}" class="btn btn-primary btn-sm">Nouveau spot</a>
</div>
<p class="text-base-content/60 mb-6 text-sm">Gérez vos spots QR. Chaque spot est un point d'accès unique vers vos contenus.</p>
<div class="card bg-base-100 shadow overflow-x-auto">
    <table class="table">
        <thead>
            <tr>
                <th class="grow">Nom</th>
                <th class="text-center">Public</th>
                <th class="text-center">Configuré</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($spots as $spot)
                <tr class="hover">
                    <td>
                        <a href="{{ route('spots.show', $spot) }}" class="link font-medium">
                            {{ $spot->name ?? '<sans nom>' }}
                        </a>
                    </td>
                    <td class="text-center">
                        @if ($spot->public)
                            <span class="badge badge-success badge-sm">Oui</span>
                        @else
                            <span class="badge badge-ghost badge-sm">Non</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($spot->configured)
                            <span class="text-success"></span>
                        @else
                            <span class="text-warning">–</span>
                        @endif
                    </td>
                    <td class="flex gap-2 justify-end">
                        <a href="{{ route('spots.show', $spot) }}" class="btn btn-ghost btn-xs">Voir</a>
                        <a href="{{ route('spots.edit', $spot) }}" class="btn btn-ghost btn-xs"></a>
                        <form method="POST" action="{{ route('spots.destroy', $spot) }}" onsubmit="return confirm('Supprimer ce spot ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-xs text-error"></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-base-content/50 py-8">Aucun spot pour l'instant.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
