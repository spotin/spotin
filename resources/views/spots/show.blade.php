@extends('layouts.app')
@section('title', $spot->name ?? 'Spot sans nom')
@section('content')
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold">
            {{ $spot->name ?? '<em>Sans nom</em>' }}
        </h1>
        <div class="flex gap-2 mt-2">
            @if ($spot->public) <span class="badge badge-success">Public</span> @endif
            @if (!$spot->configured) <span class="badge badge-warning">Non configuré</span> @endif
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('spots.edit', $spot) }}" class="btn btn-sm btn-ghost">Modifier</a>
        <form method="POST" action="{{ route('spots.destroy', $spot) }}" onsubmit="return confirm('Supprimer ?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-error btn-outline">Supprimer</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 flex flex-col gap-4">
        @if ($spot->description)
            <div class="card bg-base-100 shadow p-5">
                <h2 class="font-semibold mb-2 text-sm uppercase tracking-wide text-base-content/50">Description</h2>
                <p class="text-sm leading-relaxed">{{ $spot->description }}</p>
            </div>
        @endif

        @if ($spot->website_target)
            <div class="card bg-base-100 shadow p-5">
                <h2 class="font-semibold mb-2 text-sm uppercase tracking-wide text-base-content/50">Lien cible</h2>
                <a href="{{ $spot->website_target }}" target="_blank" class="link text-primary break-all">{{ $spot->website_target }}</a>
                @if ($spot->direct_access_to_website_target)
                    <span class="badge badge-info badge-sm ml-2">Accès direct</span>
                @endif
            </div>
        @endif

        @if ($spot->latitude && $spot->longitude)
            <div class="card bg-base-100 shadow p-5">
                <h2 class="font-semibold mb-2 text-sm uppercase tracking-wide text-base-content/50">Position</h2>
                <p class="text-sm">{{ $spot->latitude }}, {{ $spot->longitude }}</p>
            </div>
        @endif

        @if ($spot->payload)
            <div class="card bg-base-100 shadow p-5">
                <h2 class="font-semibold mb-2 text-sm uppercase tracking-wide text-base-content/50">Payload</h2>
                <pre class="text-xs bg-base-200 rounded p-3 overflow-x-auto">{{ json_encode($spot->payload, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>

    <div class="flex flex-col gap-4">
        <div class="card bg-base-100 shadow p-5 text-center">
            <h2 class="font-semibold mb-3 text-sm uppercase tracking-wide text-base-content/50">QR Code</h2>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(config('app.url') . '/s/' . $spot->id) }}" alt="QR Code" class="mx-auto rounded" />
            <p class="text-xs text-base-content/40 mt-2 break-all">{{ config('app.url') }}/s/{{ $spot->id }}</p>
        </div>
    </div>
</div>
@endsection
