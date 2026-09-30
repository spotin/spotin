<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @isset($description)
        <meta name="description" content="{{ $description }}" />
    @endisset

    @isset($title)
        <title>{{ $title }} - {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endisset

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <header>
        <nav class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ url('/') }}" class="block transition hover:opacity-80"> {{ config('app.name') }} </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('auth.sign_in') }}" class="btn btn-ghost"> {{ __('ui.auth.sign_in.title') }} </a>
                    <a href="{{ route('auth.sign_up') }}" class="btn btn-primary">
                        {{ __('ui.auth.sign_up.title') }}
                    </a>
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
                <p class="text-center sm:text-left">Version {{ config('app.version') }}</p>
                <a href="{{ url('/about') }}" class="block transition hover:opacity-80"> Right </a>
            </div>
        </div>
    </footer>
</body>
</html>
