<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'jane@example.com',
    ]);
});

test('the login page renders', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false);
});

test('users can log in with their email in any case', function (string $login) {
    $this->post(route('login.store'), ['email' => $login, 'password' => 'password'])
        ->assertRedirect(config('fortify.home'));

    $this->assertAuthenticatedAs($this->user);
})->with(['jane@example.com', 'Jane@Example.com']);

test('users cannot log in with a wrong password', function () {
    $this->from(route('login'))
        ->post(route('login.store'), ['email' => 'jane@example.com', 'password' => 'wrong-password'])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('login attempts are rate limited', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post(route('login.store'), ['email' => 'jane@example.com', 'password' => 'wrong-password']);
    }

    $this->post(route('login.store'), ['email' => 'jane@example.com', 'password' => 'password'])
        ->assertTooManyRequests();

    $this->assertGuest();
});

test('users can log out', function () {
    $this->actingAs($this->user)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
});

test('authenticated users are redirected away from guest pages', function (string $route, array $parameters) {
    $this->actingAs($this->user)
        ->get(route($route, $parameters))
        ->assertRedirect(config('fortify.home'));
})->with([
    'login' => ['login', []],
    'register' => ['register', []],
    'forgot password' => ['password.request', []],
    'reset password' => ['password.reset', ['token' => 'some-token']],
]);

test('users who ask to be remembered get a long-lived login cookie', function (?string $remember, bool $expectCookie) {
    $response = $this->post(route('login.store'), array_filter([
        'email' => 'jane@example.com',
        'password' => 'password',
        'remember' => $remember,
    ]));

    $cookie = auth()->guard()->getRecallerName();

    $expectCookie ? $response->assertCookie($cookie) : $response->assertCookieMissing($cookie);
})->with([
    'checked' => ['on', true],
    'unchecked' => [null, false],
]);
