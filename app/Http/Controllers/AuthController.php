<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthSignUpRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function resetPassword()
    {
        return view('auth.reset-password');
    }

    public function signIn()
    {
        return view('auth.sign-in');
    }

    public function signInSubmit()
    {
        // Handle sign-in logic here
    }

    public function signOut()
    {
        return view('auth.sign-out');
    }

    public function signUp()
    {
        return view('auth.sign-up');
    }

    public function signUpSubmit(AuthSignUpRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create($validated);

        return redirect()->route('auth.sign_in');
    }
}
