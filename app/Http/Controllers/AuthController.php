<?php

namespace App\Http\Controllers;

class AuthController extends Controller
{
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function showResetPassword()
    {
        return view('auth.reset-password');
    }

    public function showSignIn()
    {
        return view('auth.sign-in');
    }

    public function showSignOut()
    {
        return view('auth.sign-out');
    }

    public function showSignUp()
    {
        return view('auth.sign-up');
    }
}
