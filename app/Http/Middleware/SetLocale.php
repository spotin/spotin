<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supportedLocales = ['en', 'fr'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Session locale detection
        $sessionLocale = $request->session()->get('locale');

        if ($this->isValidLocale($sessionLocale)) {
            app()->setLocale($sessionLocale);

            return $next($request);
        }

        // Browser locale detection
        $browserLocale = $request->getPreferredLanguage($this->supportedLocales);

        if ($this->isValidLocale($browserLocale)) {
            app()->setLocale($browserLocale);

            return $next($request);
        }

        // Default locale
        $defaultLocale = config('app.locale');
        app()->setLocale($defaultLocale);

        return $next($request);
    }

    /**
     * Check if the given locale is valid.
     */
    protected function isValidLocale(?string $locale): bool
    {
        return $locale && in_array($locale, $this->supportedLocales);
    }
}
