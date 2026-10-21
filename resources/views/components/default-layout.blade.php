@props(['breadcrumbs' => []])

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
        <div class="flex flex-1 items-center">
            <div class="dropdown">
                <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                    <x-lucide-menu class="h-5 w-5" />
                </div>
                <ul
                    tabindex="-1"
                    class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow"
                >
                    @auth
                        <li>
                            <a href="{{ route('dashboard') }}">
                                <x-lucide-columns-3-cog class="h-4 w-4" />
                                {{ __('ui.dashboard.title') }}
                            </a>
                        </li>
                    @endauth
                    <li>
                        <a href="{{ route('spots.index') }}">
                            <x-lucide-map-pin class="h-4 w-4" />
                            {{ __('ui.spots.index.title') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('organizations.index') }}">
                            <x-lucide-building-2 class="h-4 w-4" />
                            {{ __('ui.organizations.index.title') }}
                        </a>
                    </li>
                    @guest
                        <li><a href="{{ route('login') }}">{{ __('ui.auth.login.title') }}</a></li>
                        <li><a href="{{ route('register') }}">{{ __('ui.auth.register.title') }}</a></li>
                    @endguest
                </ul>
            </div>
            <a href="{{ route('index') }}" aria-label="{{ config('app.name') }}">
                <x-icon-logo class="text-logo h-20 w-20" aria-hidden="true" />
            </a>
        </div>
        <div class="group/nav flex flex-none items-center gap-2">
            <ul class="menu menu-horizontal hidden flex-nowrap items-center gap-2 px-1 whitespace-nowrap lg:flex">
                @auth
                    <li>
                        <a href="{{ route('dashboard') }}">
                            <x-lucide-columns-3-cog class="h-4 w-4" aria-hidden="true" />
                            {{ __('ui.dashboard.title') }}
                        </a>
                    </li>
                @endauth
                <li>
                    <a href="{{ route('spots.index') }}">
                        <x-lucide-map-pin class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.spots.index.title') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('organizations.index') }}">
                        <x-lucide-building-2 class="h-4 w-4" aria-hidden="true" />
                        {{ __('ui.organizations.index.title') }}
                    </a>
                </li>
                @guest
                    <li>
                        <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('ui.auth.login.title') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('ui.auth.register.title') }}</a>
                    </li>
                @endguest
            </ul>

            <ul class="menu menu-horizontal items-center gap-2 px-1">
                <li>
                    <label class="swap swap-rotate btn btn-ghost btn-circle">
                        <input
                            type="checkbox"
                            class="theme-controller"
                            value="dark"
                            @checked($theme === 'dark')
                            aria-label="{{ __('ui.common.theme') }}"
                        />
                        <x-lucide-moon class="swap-on h-5 w-5" aria-hidden="true" />
                        <x-lucide-sun class="swap-off h-5 w-5" aria-hidden="true" />
                    </label>
                </li>
                <li>
                    <button type="button" popovertarget="locale-popover" style="anchor-name: --locale-anchor">
                        <x-lucide-globe class="h-4 w-4" aria-hidden="true" />
                        <span class="sr-only">{{ __('ui.common.locale.label') }}</span>
                        {{ strtoupper(app()->getLocale()) }}
                        <x-lucide-chevron-down class="h-3 w-3 transition-transform group-has-[#locale-popover:popover-open]/nav:rotate-180" />
                    </button>
                </li>
                @auth
                    <li>
                        <button
                            type="button"
                            popovertarget="account-popover"
                            style="anchor-name: --account-anchor"
                            class="btn btn-ghost btn-circle p-0"
                            aria-label="{{ __('ui.common.account') }}"
                        >
                            <span class="avatar avatar-placeholder" aria-hidden="true">
                                <span class="bg-neutral text-neutral-content flex aspect-square w-8 items-center justify-center overflow-hidden rounded-full">
                                    <span class="text-xs">{{ auth()->user()->initials() }}</span>
                                </span>
                            </span>
                        </button>
                    </li>
                @endauth
            </ul>

            @auth
                <ul
                    popover
                    id="account-popover"
                    style="position-anchor: --account-anchor"
                    class="dropdown dropdown-end menu bg-base-100 rounded-box w-56 rounded-t-none p-2 shadow-sm"
                >
                    <li class="menu-title">
                        <span class="text-base-content line-clamp-1 break-all">{{ auth()->user()->name }}</span>
                        <span class="line-clamp-1 text-xs font-normal break-all">{{ auth()->user()->email }}</span>
                    </li>
                    <li><a href="{{ route('invitations.index') }}">{{ __('ui.invitations.index.title') }}</a></li>
                    <li><a href="{{ route('settings.index') }}">{{ __('ui.settings.title') }}</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="grid-cols-1 p-0">
                            @csrf
                            <button type="submit" class="w-full cursor-pointer px-3 py-1.5 text-left">
                                {{ __('ui.auth.logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            @endauth
            <ul
                popover
                id="locale-popover"
                style="position-anchor: --locale-anchor"
                class="dropdown dropdown-end menu bg-base-100 rounded-box w-52 rounded-t-none p-2 shadow-sm"
            >
                @foreach (config('app.supported_locales') as $locale)
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

    <main class="container mx-auto max-w-2xl flex-grow px-4 py-8 sm:px-6 lg:px-8">
        <x-breadcrumbs :items="$breadcrumbs" />
        <div class="prose max-w-none">
            @if (session('success'))
                <p role="alert" class="alert alert-success">
                    <x-lucide-check-circle class="h-6 w-6" />
                    <span>{{ session('success') }}</span>
                </p>
            @endif

            {{ $slot }}
        </div>
    </main>

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
            <p>{{ config('app.name') }} 2021-{{ date('Y') }}</p>
        </aside>
    </footer>

    {{-- The dialogs of the page, out of the elements whose buttons open them, such as joined buttons --}}
    @stack('dialogs')
</body>
</html>
