<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    @head
    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <header>
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-2 hover:opacity-60">
                    <x-icon-logo class="h-16 w-16" />
                    <span class="text-lg font-bold">{{ config('app.name') }}</span>
                </a>

                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('profile.show') }}" class="avatar">
                            <div class="bg-base-200 rounded-full hover:opacity-80">
                                <x-lucide-user class="h-8 w-8" />
                            </div>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost"> {{ __('ui.auth.login.title') }} </a>
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            {{ __('ui.auth.register.title') }}
                        </a>
                    @endauth
                    <form method="POST" action="{{ route('locale.update') }}">
                        @csrf
                        @method('PATCH')

                        <select id="locale" name="locale" onchange="this.form.submit()" class="select">
                            <option value="en" @selected(app()->getLocale() === 'en')>EN</option>
                            <option value="fr" @selected(app()->getLocale() === 'fr')>FR</option>
                        </select>
                        </label>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main class="prose container mx-auto max-w-2xl flex-grow px-4 py-8 sm:px-6 lg:px-8">{{ $slot }}</main>

    <footer class="text-sm">
        <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex h-16 flex-col items-center justify-between gap-4 sm:flex-row">
                <p class="text-center sm:text-left">Left</p>
                <p class="text-center sm:text-left">
                <div class="tooltip" data-tip="{{ __('ui.common.version', ['version' => config('app.version')]) }}">
                    Spot in<sup>®</sup> 2021-{{ date('Y') }}
                </div>
                </p>
                <a href="{{ url('/about') }}" class="block transition hover:opacity-80"> Right </a>
            </div>
        </div>
    </footer>
</body>
</html>
