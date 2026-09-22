<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
	/**
	 * Update the user's preferred locale.
	 */
	public function update(Request $request): RedirectResponse
	{
		$validated = $request->validate([
			"locale" => ["required", "in:en,fr"],
		]);

		$locale = $validated["locale"];

		$request->session()->put("locale", $locale);

		return back();
	}
}
