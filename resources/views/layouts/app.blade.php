<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="spotin">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Spot in') | Spot in®</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-base-200">

<div class="navbar bg-base-100 shadow-sm sticky top-0 z-50 px-4">
    <div class="navbar-start">
        <a href="/" class="text-xl font-bold">Spot in</a>
    </div>
    <div class="navbar-end gap-2">
        @auth
            <a href="{{ route('spots.index') }}" class="btn btn-ghost btn-sm">{{ __('ui.header.spots') }}</a>
            <a href="{{ route('tokens.index') }}" class="btn btn-ghost btn-sm">{{ __('ui.header.tokens') }}</a>
            <a href="{{ route('profile.show') }}" class="btn btn-ghost btn-sm">{{ __('ui.header.profile') }}</a>
            <form method="POST" action="{{ route('auth.logout') }}">
                @csrf
                <button class="btn btn-ghost btn-sm">{{ __('ui.header.logout') }}</button>
            </form>
        @else
            <a href="{{ route('auth.login') }}" class="btn btn-ghost btn-sm">{{ __('ui.header.login') }}</a>
            <a href="{{ route('auth.register') }}" class="btn btn-primary btn-sm rounded-full">{{ __('ui.header.register') }}</a>
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

<footer class="bg-neutral text-neutral-content text-sm flex items-center justify-center gap-4 p-4 flex-wrap">
    <span>{{ __('ui.footer.copyright') }}</span>
    <span class="opacity-30">·</span>
    <a href="/about" class="link link-hover">{{ __('ui.footer.about') }}</a>
    <span class="opacity-30">·</span>
    <a href="/privacy-policy" class="link link-hover">{{ __('ui.footer.privacyPolicy') }}</a>
    <span class="opacity-30">|</span>
    @foreach (['fr' => 'FR', 'en' => 'EN', 'de' => 'DE'] as $locale => $label)
        <a href="{{ route('lang.switch', $locale) }}"
           class="link link-hover {{ app()->getLocale() === $locale ? 'font-bold text-primary' : 'opacity-60' }}">
            {{ $label }}
        </a>
    @endforeach
</footer>

</body>
</html>
