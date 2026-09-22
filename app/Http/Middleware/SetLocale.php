<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
	protected array $supportedLocales = ["en", "fr"];

	/**
	 * Handle an incoming request.
	 *
	 * @param  Closure(Request): (Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		$locale = config("app.locale");

		$browserLocale = $request->getPreferredLanguage($this->supportedLocales);

		if ($this->isValidLocale($browserLocale)) {
			$locale = $browserLocale;
		}

		app()->setLocale($locale);

		return $next($request);
	}

	/**
	 * Check if the given locale is valid.
	 *
	 * @param  string|null  $locale
	 * @return bool
	 */
	protected function isValidLocale(?string $locale): bool
	{
		return $locale && in_array($locale, $this->supportedLocales);
	}
}
