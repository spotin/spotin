<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

function registrationData(array $overrides = []): array
{
    return [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'a-long-enough-password',
        'password_confirmation' => 'a-long-enough-password',
        ...$overrides,
    ];
}

/**
 * Invite an address to a new organization, then follow the link of the invitation, as its email would.
 */
function followInvitation(string $email = 'jane@example.com', OrganizationRole $role = OrganizationRole::Member, bool $expired = false): Organization
{
    $organization = Organization::factory()->create(['name' => 'Acme']);
    $token = Invitation::generateToken();

    $organization->invitations()->create([
        'email' => $email,
        'role' => $role,
        'token' => Invitation::hashToken($token),
        'invited_by' => User::factory()->create()->id,
        'expires_at' => now()->addDays(7),
    ]);

    test()->get(route('invitations.show', ['token' => $token]))->assertOk();

    if ($expired) {
        test()->travel(8)->days();
    }

    return $organization;
}

test('without an invitation, the registration page explains that accounts are created by invitation', function () {
    $this->get(route('register'))
        ->assertOk()
        ->assertSee(__('ui.auth.register.invitation_only.title'))
        ->assertSee(route('contact'), false)
        ->assertDontSee('action="'.route('register.store').'"', false);
});

test('following an invitation, the registration page offers the form with the invited address, which cannot be changed', function () {
    followInvitation('new@example.com');

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('action="'.route('register.store').'"', false)
        ->assertSee('value="new@example.com"', false)
        ->assertSee('readonly', false)
        ->assertSee(__('ui.auth.register.invitation', ['organization' => 'Acme']))
        ->assertDontSee(__('ui.auth.register.invitation_only.title'));
});

test('an invited person registers, joins the organization with the invited role and lands on it, without a verification email', function () {
    Notification::fake();
    $organization = followInvitation('jane@example.com', OrganizationRole::Responsible);

    $this->post(route('register.store'), registrationData(['email' => 'Jane@Example.com']))
        ->assertRedirect(route('organizations.show', ['organization' => $organization->uuid]))
        ->assertSessionHas('success', __('ui.invitations.feedback.invitation_accepted'));

    $user = User::where('email', 'jane@example.com')->sole();

    $this->assertAuthenticatedAs($user);
    expect($user->hasVerifiedEmail())->toBeTrue()
        ->and($user->isResponsibleOf($organization))->toBeTrue()
        ->and(Invitation::count())->toBe(0)
        ->and(session()->has(Invitation::SESSION_KEY))->toBeFalse();
    Notification::assertNotSentTo($user, VerifyEmail::class);
});

test('no account is created without an invitation, with an expired one or with another address than the invited one', function (string $case) {
    match ($case) {
        'no invitation' => null,
        'expired invitation' => followInvitation(expired: true),
        'another address' => followInvitation('someone.else@example.com'),
    };

    $this->from(route('register'))
        ->post(route('register.store'), registrationData())
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors(['email' => __('ui.auth.register.invitation_only.required')]);

    $this->assertGuest();
    expect(User::where('email', 'jane@example.com')->exists())->toBeFalse();
})->with(['no invitation', 'expired invitation', 'another address']);

test('registration rejects invalid input', function (array $overrides, string $field) {
    followInvitation();

    $this->from(route('register'))
        ->post(route('register.store'), registrationData($overrides))
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors($field);

    $this->assertGuest();
    expect(Invitation::count())->toBe(1);
})->with([
    'too short name' => [['name' => 'J'], 'name'],
    'too short password' => [['password' => 'short', 'password_confirmation' => 'short'], 'password'],
    'mismatched confirmation' => [['password_confirmation' => 'something-else-entirely'], 'password'],
]);

test('registration rejects an invited address that already has an account, in any case', function () {
    User::factory()->create(['email' => 'jane@example.com']);
    followInvitation('jane@example.com');

    $this->post(route('register.store'), registrationData(['email' => 'JANE@example.com']))
        ->assertSessionHasErrors('email');

    expect(User::where('email', 'jane@example.com')->count())->toBe(1);
});

test('validation errors are shown on the registration form', function () {
    followInvitation();

    $this->followingRedirects()
        ->from(route('register'))
        ->post(route('register.store'), registrationData(['password_confirmation' => 'something-else-entirely']))
        ->assertOk()
        ->assertSee(__('validation.confirmed', ['attribute' => 'password']))
        ->assertSee('aria-invalid="true"', false);
});
