<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

function registrationData(array $overrides = []): array
{
    return [
        'username' => 'jane-doe',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'a-long-enough-password',
        'password_confirmation' => 'a-long-enough-password',
        ...$overrides,
    ];
}

test('the registration page renders', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee('name="password_confirmation"', false);
});

test('users can register and receive a verification email', function () {
    Notification::fake();

    $this->post(route('register.store'), registrationData([
        'username' => 'Jane-Doe',
        'email' => 'Jane@Example.com',
    ]))->assertRedirect(config('fortify.home'));

    $user = User::sole();

    expect($user->username)->toBe('jane-doe')
        ->and($user->email)->toBe('jane@example.com')
        ->and($user->hasVerifiedEmail())->toBeFalse();

    $this->assertAuthenticatedAs($user);
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('registration rejects invalid input', function (array $overrides, string $field) {
    $this->from(route('register'))
        ->post(route('register.store'), registrationData($overrides))
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors($field);

    $this->assertGuest();
})->with([
    'missing username' => [['username' => ''], 'username'],
    'too short username' => [['username' => 'ab'], 'username'],
    'username with spaces' => [['username' => 'jane doe'], 'username'],
    'too short name' => [['name' => 'J'], 'name'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'too short password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'mismatched confirmation' => [['password_confirmation' => 'something-else-entirely'], 'password'],
]);

test('registration rejects a username or email that is already taken, in any case', function (array $overrides, string $field) {
    User::factory()->create(['username' => 'jane-doe', 'email' => 'jane@example.com']);

    $this->post(route('register.store'), registrationData([
        'username' => 'someone-else',
        'email' => 'someone@example.com',
        ...$overrides,
    ]))->assertSessionHasErrors($field);

    expect(User::count())->toBe(1);
})->with([
    'username' => [['username' => 'JANE-DOE'], 'username'],
    'email' => [['email' => 'JANE@example.com'], 'email'],
]);

test('validation errors are shown on the registration form', function () {
    $this->followingRedirects()
        ->from(route('register'))
        ->post(route('register.store'), registrationData(['password_confirmation' => 'something-else-entirely']))
        ->assertOk()
        ->assertSee(__('validation.confirmed', ['attribute' => 'password']))
        ->assertSee('aria-invalid="true"', false);
});
