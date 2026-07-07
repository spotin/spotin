<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['fr', 'en', 'de'];
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, $supported)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
