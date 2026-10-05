<?php

namespace App\Http\Controllers;

class ProfileController extends Controller
{
    /**
     * Display the resource.
     */
    public function show()
    {
        return view('profile.show');
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(): never
    {
        abort(404);
    }
}
