<?php

use App\Models\User;

// The URLs are written out on purpose: generating them with route() would load the Folio routes
// before the request, which hides the problem these tests guard against.
test('guest pages show their own title', function (string $url, string $title) {
    $this->get($url)->assertOk()->assertSee("<title>$title - Spot in®</title>", false);
})->with([
    'login' => ['/auth/login', 'Login'],
    'register' => ['/auth/register', 'Register'],
    'forgot password' => ['/auth/forgot-password', 'Forgot your password?'],
]);

test('pages that link to other pages show their own title', function (bool $admin, string $url, string $title) {
    $this->actingAs(User::factory()->create(['is_admin' => $admin]))
        ->get($url)
        ->assertOk()
        ->assertSee("<title>$title - Spot in®</title>", false);
})->with([
    'organizations' => [false, '/organizations', 'Organizations'],
    'spots' => [false, '/spots', 'Spots'],
    'new organization' => [true, '/organizations/create', 'New organization'],
    'profile settings' => [false, '/settings/profile', 'Profile settings'],
]);
