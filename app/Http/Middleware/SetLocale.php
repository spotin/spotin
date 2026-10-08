<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales');
        $storedLocale = $request->cookie('locale');

        app()->setLocale(in_array($storedLocale, $supportedLocales, true)
            ? $storedLocale
            : $request->getPreferredLanguage($supportedLocales));

        return $next($request);
    }
}
