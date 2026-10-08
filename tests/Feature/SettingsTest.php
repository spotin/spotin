<?php

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

test('the profile settings page shows the current profile', function () {
    $this->actingAs($this->user)
        ->get(route('settings.profile'))
        ->assertOk()
        ->assertSee('action="'.route('user-profile-information.update').'"', false)
        ->assertSee('value="jane@example.com"', false);
});

test('the security settings page shows the password form', function () {
    $this->actingAs($this->user)
        ->get(route('settings.security'))
        ->assertOk()
        ->assertSee('action="'.route('user-password.update').'"', false)
        ->assertSee('name="current_password"', false);
});

test('users can update their profile and see a confirmation', function () {
    $this->actingAs($this->user)
        ->followingRedirects()
        ->from(route('settings.profile'))
        ->put(route('user-profile-information.update'), [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ])
        ->assertOk()
        ->assertSee(__('ui.settings.profile.form.feedback.success'))
        ->assertDontSee('profile-information-updated');

    expect($this->user->fresh())
        ->name->toBe('Jane Smith')
        ->hasVerifiedEmail()->toBeTrue();
});

test('changing the email requires verifying the new address', function () {
    Notification::fake();

    $this->actingAs($this->user)
        ->put(route('user-profile-information.update'), [
            'name' => 'Jane Doe',
            'email' => 'Jane.Smith@Example.com',
        ])
        ->assertSessionHasNoErrors();

    $user = $this->user->fresh();

    expect($user->email)->toBe('jane.smith@example.com')
        ->and($user->hasVerifiedEmail())->toBeFalse();

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('users cannot take an email that is already used', function (array $overrides, string $field) {
    User::factory()->create(['email' => 'john@example.com']);

    $this->actingAs($this->user)
        ->put(route('user-profile-information.update'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            ...$overrides,
        ])
        ->assertSessionHasErrorsIn('updateProfileInformation', $field);
})->with([
    'email' => [['email' => 'John@Example.com'], 'email'],
]);

test('users can change their password and see a confirmation', function () {
    $this->actingAs($this->user)
        ->followingRedirects()
        ->from(route('settings.security'))
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'a-brand-new-password',
            'password_confirmation' => 'a-brand-new-password',
        ])
        ->assertOk()
        ->assertSee(__('ui.settings.security.form.feedback.success'))
        ->assertDontSee('password-updated');

    expect(Hash::check('a-brand-new-password', $this->user->fresh()->password))->toBeTrue();
});

test('the password is not changed when the current password is wrong', function () {
    $this->actingAs($this->user)
        ->put(route('user-password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'a-brand-new-password',
            'password_confirmation' => 'a-brand-new-password',
        ])
        ->assertSessionHasErrorsIn('updatePassword', 'current_password');

    expect(Hash::check('password', $this->user->fresh()->password))->toBeTrue();
});
