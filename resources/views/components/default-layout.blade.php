@php
    $theme = in_array(request()->cookie('theme'), ['light', 'dark'], true) ? request()->cookie('theme') : null;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if ($theme) data-theme="{{ $theme }}" @endif>
<head>
    <meta charset="utf-8" />
    @head
    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <header class="navbar bg-base-100 shadow-sm">
        <div class="navbar-start">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <x-lucide-menu class="h-5 w-5" />
                </div>
                <ul
                    tabindex="-1"
                    class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow"
                >
                    @auth
                        {{-- <li>
                            <a href=""><x-lucide-map-pin class="h-4 w-4" />Mes spots</a>
                        </li>
                        <li>
                            <a href=""><x-lucide-building class="h-4 w-4" />Mes organisations</a>
                        </li>
                        <li>
                            <a href=""><x-lucide-shield class="h-4 w-4" />Administration</a>
                        </li> --}}
                        <li>
                            <span><x-lucide-settings class="h-4 w-4" />{{ __('ui.settings.title') }}</span>
                            <ul>
                                <li>
                                    <a href="{{ route('settings.profile') }}">{{ __('ui.settings.profile.title') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('settings.security') }}">{{ __('ui.settings.security.title') }}</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="grid-cols-1 p-0">
                                        @csrf
                                        <button type="submit" class="w-full cursor-pointer px-2 py-1 text-left">
                                            {{ __('ui.common.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li><a href="{{ route('login') }}">{{ __('ui.auth.login.title') }}</a></li>
                        <li><a href="{{ route('register') }}">{{ __('ui.auth.register.title') }}</a></li>
                    @endauth
                </ul>
            </div>
            <a href="{{ route('index') }}" aria-label="{{ config('app.name') }}"
                ><x-icon-logo class="text-logo h-20 w-20" aria-hidden="true"
            /></a>
        </div>
        <div class="navbar-end group/nav gap-2">
            @auth
                <ul class="menu menu-horizontal hidden px-1 lg:flex">
                    {{-- <li>
                        <a href=""><x-lucide-map-pin class="h-4 w-4" />Mes spots</a>
                    </li>
                    <li>
                        <a href=""><x-lucide-building class="h-4 w-4" />Mes organisations</a>
                    </li>
                    <li>
                        <a href=""><x-lucide-shield class="h-4 w-4" />Administration</a>
                    </li> --}}
                    <li>
                        <button type="button" popovertarget="settings-popover" style="anchor-name: --settings-anchor">
                            <x-lucide-settings class="h-4 w-4" />
                            {{ __('ui.settings.title') }}
                            <x-lucide-chevron-down class="h-3 w-3 transition-transform group-has-[#settings-popover:popover-open]/nav:rotate-180" />
                        </button>
                    </li>
                </ul>
                <ul
                    popover
                    id="settings-popover"
                    style="position-anchor: --settings-anchor"
                    class="dropdown dropdown-end menu bg-base-100 rounded-box w-52 rounded-t-none p-2 shadow-sm"
                >
                    <li><a href="{{ route('settings.profile') }}">{{ __('ui.settings.profile.title') }}</a></li>
                    <li><a href="{{ route('settings.security') }}">{{ __('ui.settings.security.title') }}</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="grid-cols-1 p-0">
                            @csrf
                            <button type="submit" class="w-full cursor-pointer px-3 py-1.5 text-left">
                                {{ __('ui.common.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost"> {{ __('ui.auth.login.title') }} </a>
                <a href="{{ route('register') }}" class="btn btn-primary"> {{ __('ui.auth.register.title') }} </a>
            @endauth

            <label class="swap swap-rotate btn btn-ghost btn-circle">
                <input
                    type="checkbox"
                    class="theme-controller"
                    value="dark"
                    @checked($theme === 'dark')
                    aria-label="{{ __('ui.common.theme') }}"
                />
                <x-lucide-moon class="swap-on h-5 w-5" />
                <x-lucide-sun class="swap-off h-5 w-5" />
            </label>

            <ul class="menu menu-horizontal px-1">
                <li>
                    <button type="button" popovertarget="locale-popover" style="anchor-name: --locale-anchor">
                        <x-lucide-globe class="h-4 w-4" /> {{ strtoupper(app()->getLocale()) }}
                        <x-lucide-chevron-down class="h-3 w-3 transition-transform group-has-[#locale-popover:popover-open]/nav:rotate-180" />
                    </button>
                </li>
            </ul>
            <ul
                popover
                id="locale-popover"
                style="position-anchor: --locale-anchor"
                class="dropdown dropdown-end menu bg-base-100 rounded-box w-52 rounded-t-none p-2 shadow-sm"
            >
                @foreach (['en', 'fr'] as $locale)
                    <li>
                        <form method="POST" action="{{ route('locale.update') }}" class="grid-cols-1 p-0">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="locale" value="{{ $locale }}" />

                            <button
                                type="submit"
                                class="w-full cursor-pointer px-3 py-1.5 text-left"
                                @if (app()->getLocale() === $locale) aria-current="true" @endif
                            >
                                {{ __('ui.common.locale.options.' . $locale) }}
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    </header>

    <main class="prose container mx-auto max-w-2xl flex-grow px-4 py-8 sm:px-6 lg:px-8">{{ $slot }}</main>

    <footer class="footer footer-horizontal footer-center bg-base-200 text-base-content rounded p-10">
        <nav class="grid grid-flow-col gap-4">
            <a class="link link-hover">About us</a>
            <a class="link link-hover">Contact</a>
            <a class="link link-hover">Privacy Policy</a>
            <a class="link link-hover">Terms of Service</a>
        </nav>
        <nav>
            <div class="grid grid-flow-col gap-4">
                <a
                    href="https://github.com/spotin/spotin"
                    class="tooltip"
                    data-tip="{{ __('ui.common.version', ['version' => config('app.version')]) }}"
                >
                    <x-simpleicon-github class="h-6 w-6" />
                </a>
            </div>
        </nav>
        <aside>
            <p>Spot in<sup>®</sup> 2021-{{ date('Y') }}</p>
        </aside>
    </footer>
</body>
</html>
