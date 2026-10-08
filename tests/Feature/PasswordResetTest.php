<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('the forgot password page renders', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('name="email"', false);
});

test('users can request a reset link with their email in any case', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'jane@example.com']);

    $this->from(route('password.request'))
        ->post(route('password.email'), ['email' => 'Jane@Example.com'])
        ->assertRedirect(route('password.request'))
        ->assertSessionHas('status', __('passwords.sent'));

    Notification::assertSentTo($user, ResetPassword::class);
});

test('users can reset their password with the emailed token', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'jane@example.com']);

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        $this->get(route('password.reset', ['token' => $notification->token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('value="'.$notification->token.'"', false);

        $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'a-brand-new-password',
            'password_confirmation' => 'a-brand-new-password',
        ])->assertRedirect(route('login'));

        return true;
    });

    expect(Hash::check('a-brand-new-password', $user->fresh()->password))->toBeTrue();

    $this->get(route('login'))->assertSee(__('passwords.reset'));
});

test('the reset link in the email points to the reset page', function () {
    Notification::fake();
    $user = User::factory()->create(['email' => 'jane@example.com']);

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        $url = $notification->toMail($user)->actionUrl;

        expect($url)->toBe(url("/auth/reset-password/{$notification->token}?email=jane%40example.com"));

        $this->get($url)
            ->assertOk()
            ->assertSee('value="jane@example.com"', false);

        return true;
    });
});

test('the password cannot be reset with an invalid token', function () {
    $user = User::factory()->create();

    $this->post(route('password.update'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'a-brand-new-password',
        'password_confirmation' => 'a-brand-new-password',
    ])->assertSessionHasErrors('email');

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});
