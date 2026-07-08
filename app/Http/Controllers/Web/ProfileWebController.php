<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileWebController extends Controller
{
    public function show(): View
    {
        return view('profile.show');
    }

    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'bio' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $data = array_filter([
            'username' => $validated['username'],
            'bio' => $validated['bio'] ?? null,
            'password' => $validated['new_password'] ?? null,
        ], fn ($v) => ! is_null($v));

        $request->user()->update($data);

        return redirect()->route('profile.show')->with('success', 'Profil mis à jour.');
    }
}
