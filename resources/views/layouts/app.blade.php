<!DOCTYPE html>
<html lang="fr" data-theme="spotin">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Spot in') | Spot in®</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-base-200">

<div class="navbar bg-base-100 shadow-sm sticky top-0 z-50 px-4">
    <div class="navbar-start">
        <a href="/" class="text-xl font-bold">Spot <span class="text-primary">in</span></a>
    </div>
    <div class="navbar-end gap-2">
        @auth
            <a href="{{ route('spots.index') }}" class="btn btn-ghost btn-sm">Spots</a>
            <a href="{{ route('tokens.index') }}" class="btn btn-ghost btn-sm">Tokens</a>
            <a href="{{ route('profile.show') }}" class="btn btn-ghost btn-sm">Profil</a>
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button class="btn btn-ghost btn-sm">Déconnexion</button>
            </form>
        @else
            <a href="{{ route('auth.login') }}" class="btn btn-ghost btn-sm">Connexion</a>
            <a href="{{ route('auth.register') }}" class="btn btn-primary btn-sm rounded-full">S'inscrire</a>
        @endauth
    </div>
</div>

<main class="container mx-auto px-4 py-10 grow max-w-4xl mt-6">
    @if (session('success'))
        <div role="alert" class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div role="alert" class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="alert alert-error mb-4">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @yield('content')
</main>

<footer class="bg-neutral text-neutral-content text-sm flex items-center justify-center gap-4 p-4">
    <span>Spot in® 2021-{{ date('Y') }}</span>
		<span>·</span>
    <a href="/" class="link link-hover">À propos</a>
		<span>·</span>
    <a href="/privacy-policy" class="link link-hover">Confidentialité</a>
</footer>

</body>
</html>
