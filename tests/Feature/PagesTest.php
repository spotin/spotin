<?php

use App\Models\User;

test('the home page renders with its title and description', function () {
    $this->get(route('index'))
        ->assertOk()
        ->assertSee('<title>'.e(__('ui.welcome.title')).' - '.e(config('app.name')).'</title>', false)
        ->assertSee('<meta name="description" content="'.e(__('ui.welcome.description')).'">', false)
        ->assertSee(e(config('app.name')).'<sup>®</sup>', false);
});

test('an unknown page returns a not found response', function () {
    $this->get('/this-page-does-not-exist')->assertNotFound();
});

test('guests are redirected to the login page from protected pages', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
})->with([
    'dashboard' => '/dashboard',
    'profile settings' => '/settings/profile',
    'security settings' => '/settings/security',
    'email verification notice' => '/auth/verify-email',
    'password confirmation' => '/auth/confirm-password',
]);

test('unverified users are redirected to the verification notice from the dashboard', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('verification.notice'));
});

test('verified users can see the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee(__('ui.dashboard.title'));
});

test('authentication pages are served at their Fortify paths only', function (string $url) {
    $this->get($url)->assertNotFound();
})->with([
    'reset password without a token' => '/auth/reset-password',
    'default verification notice path' => '/auth/email/verify',
    'default password confirmation path' => '/auth/user/confirm-password',
]);
