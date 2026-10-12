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
                            <a href="{{ route('organizations.index') }}">
                                <x-lucide-building class="h-4 w-4" />
                                {{ __('ui.organizations.index.title') }}
                            </a>
                        </li>
                        @if (auth()->user()->isAdmin())
                            <li>
                                <a href="{{ route('admin.index') }}">
                                    <x-lucide-shield class="h-4 w-4" />
                                    {{ __('ui.admin.title') }}
                                </a>
                                <ul>
                                    <li>
                                        <a href="{{ route('admin.organizations.index') }}">
                                            {{ __('ui.admin.organizations.index.title') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('settings.index') }}">
                                <x-lucide-settings class="h-4 w-4" />
                                {{ __('ui.settings.title') }}
                            </a>
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
                                            {{ __('ui.auth.logout') }}
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
        <div class="group/nav flex flex-none items-center gap-2">
            <ul class="menu menu-horizontal hidden flex-nowrap items-center px-1 whitespace-nowrap lg:flex">
                @auth
                    <li>
                        <a href="{{ route('organizations.index') }}">
                            <x-lucide-building-complex class="h-4 w-4" />
                            {{ __('ui.organizations.index.title') }}
                        </a>
                    </li>
                    @if (auth()->user()->isAdmin())
                        <li>
                            <button type="button" popovertarget="admin-popover" style="anchor-name: --admin-anchor">
                                <x-lucide-shield class="h-4 w-4" />
                                {{ __('ui.admin.title') }}
                                <x-lucide-chevron-down class="h-3 w-3 transition-transform group-has-[#admin-popover:popover-open]/nav:rotate-180" />
                            </button>
                        </li>
                    @endif
                    <li>
                        <button type="button" popovertarget="settings-popover" style="anchor-name: --settings-anchor">
                            <x-lucide-settings class="h-4 w-4" />
                            {{ __('ui.settings.title') }}
                            <x-lucide-chevron-down class="h-3 w-3 transition-transform group-has-[#settings-popover:popover-open]/nav:rotate-180" />
                        </button>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}" class="btn btn-ghost">{{ __('ui.auth.login.title') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('ui.auth.register.title') }}</a>
                    </li>
                @endauth
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
                        <x-lucide-moon class="swap-on h-5 w-5" />
                        <x-lucide-sun class="swap-off h-5 w-5" />
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
            </ul>

            @auth
                @if (auth()->user()->isAdmin())
                    <ul
                        popover
                        id="admin-popover"
                        style="position-anchor: --admin-anchor"
                        class="dropdown dropdown-end menu bg-base-100 rounded-box w-52 rounded-t-none p-2 shadow-sm"
                    >
                        <li>
                            <a href="{{ route('admin.organizations.index') }}">
                                {{ __('ui.admin.organizations.index.title') }}
                            </a>
                        </li>
                    </ul>
                @endif
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
        <div class="prose max-w-none">{{ $slot }}</div>
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
            <p>{{ config('app.name') }}<sup>®</sup> 2021-{{ date('Y') }}</p>
        </aside>
    </footer>
</body>
</html>
