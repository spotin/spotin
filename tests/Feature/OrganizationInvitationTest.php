<?php

use App\Actions\Organizations\AcceptInvitation;
use App\Actions\Organizations\InviteToOrganization;
use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use App\Notifications\OrganizationInvitationNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
    $this->inviter = User::factory()->create();
    $this->organization->users()->attach($this->inviter, ['role' => OrganizationRole::Responsible]);
});

/**
 * Invite an address and return the plain token that was emailed.
 */
function invite(string $email = 'jane@example.com', OrganizationRole $role = OrganizationRole::Member): string
{
    $token = null;

    Notification::fake();

    app(InviteToOrganization::class)->invite(test()->organization, $email, $role, test()->inviter);

    Notification::assertSentOnDemand(OrganizationInvitationNotification::class, function ($notification) use (&$token) {
        $token = (fn () => $this->token)->call($notification);

        return true;
    });

    return $token;
}

test('inviting stores a hashed token and emails the plain one', function () {
    $token = invite('jane@example.com', OrganizationRole::Responsible);

    $invitation = OrganizationInvitation::sole();

    expect($invitation->email)->toBe('jane@example.com')
        ->and($invitation->role)->toBe(OrganizationRole::Responsible)
        ->and($invitation->token)->toBe(OrganizationInvitation::hashToken($token))
        ->and($invitation->token)->not->toBe($token)
        ->and($invitation->inviter->is($this->inviter))->toBeTrue()
        ->and($invitation->expires_at->isSameDay(now()->addDays(7)))->toBeTrue();

    Notification::assertSentOnDemand(
        OrganizationInvitationNotification::class,
        fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'jane@example.com',
    );
});

test('the invitation email links to the acceptance page', function () {
    $token = invite();

    $mail = (new OrganizationInvitationNotification(OrganizationInvitation::sole()->load('organization'), $token))
        ->toMail(new stdClass);

    expect($mail->actionUrl)->toBe(route('organizations.invitations.show', ['token' => $token]))
        ->and($mail->subject)->toContain('Acme');
});

test('inviting an address again replaces the pending invitation', function () {
    $first = invite(role: OrganizationRole::Member);
    $second = invite(role: OrganizationRole::Responsible);

    expect(OrganizationInvitation::count())->toBe(1)
        ->and(OrganizationInvitation::sole()->role)->toBe(OrganizationRole::Responsible)
        ->and(OrganizationInvitation::findActiveByToken($first))->toBeNull()
        ->and(OrganizationInvitation::findActiveByToken($second))->not->toBeNull();
});

test('resending issues a new token and a new expiry', function () {
    $old = invite();
    $invitation = OrganizationInvitation::sole();
    $invitation->update(['expires_at' => now()->addHour()]);

    Notification::fake();
    app(InviteToOrganization::class)->resend($invitation, $this->inviter);

    expect(OrganizationInvitation::findActiveByToken($old))->toBeNull()
        ->and($invitation->fresh()->expires_at->isSameDay(now()->addDays(7)))->toBeTrue();
    Notification::assertSentOnDemandTimes(OrganizationInvitationNotification::class, 1);
});

test('inviting lowercases the address', function () {
    Notification::fake();

    $this->actingAs($this->inviter)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => ' Jane@Example.com ', 'role' => OrganizationRole::Member->value])
        ->assertSessionHasNoErrors();

    expect(OrganizationInvitation::sole()->email)->toBe('jane@example.com');
});

test('inviting rejects invalid addresses', function (string $email) {
    Notification::fake();

    $this->actingAs($this->inviter)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => $email, 'role' => OrganizationRole::Member->value])
        ->assertSessionHasErrors('email');

    expect(OrganizationInvitation::count())->toBe(0);
})->with(['not-an-email', 'invalid', '']);

test('inviting someone who already belongs to the organization is rejected', function () {
    Notification::fake();

    $this->actingAs($this->inviter)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => strtoupper($this->inviter->email), 'role' => OrganizationRole::Member->value])
        ->assertSessionHasErrors('email');

    expect(OrganizationInvitation::count())->toBe(0);
});

test('a user with the invited verified address can accept and join with the invited role', function () {
    $token = invite('jane@example.com', OrganizationRole::Responsible);
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    $organization = app(AcceptInvitation::class)->accept($token, $jane);

    expect($organization->is($this->organization))->toBeTrue()
        ->and($jane->isResponsibleOf($this->organization))->toBeTrue()
        ->and(OrganizationInvitation::count())->toBe(0);
});

test('an invitation cannot be accepted twice', function () {
    $token = invite();
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    app(AcceptInvitation::class)->accept($token, $jane);

    expect(fn () => app(AcceptInvitation::class)->accept($token, $jane))->toThrow(ValidationException::class);
});

test('an invitation is refused to other users, unverified addresses and unknown or expired tokens', function (string $case) {
    $token = invite();
    $user = match ($case) {
        'another user' => User::factory()->create(),
        'unverified address' => User::factory()->unverified()->create(['email' => 'jane@example.com']),
        'unknown token' => User::factory()->create(['email' => 'jane@example.com']),
        'expired' => User::factory()->create(['email' => 'jane@example.com']),
    };

    if ($case === 'unknown token') {
        $token = 'not-the-token';
    }

    if ($case === 'expired') {
        OrganizationInvitation::sole()->update(['expires_at' => now()->subMinute()]);
    }

    expect(fn () => app(AcceptInvitation::class)->accept($token, $user))->toThrow(ValidationException::class)
        ->and($user->belongsToOrganization($this->organization))->toBeFalse();
})->with(['another user', 'unverified address', 'unknown token', 'expired']);

test('a leftover invitation can promote an existing member but never demote a responsible user', function () {
    $member = User::factory()->create(['email' => 'jane@example.com']);
    $this->organization->users()->attach($member);

    OrganizationInvitation::factory()->for($this->organization)->create([
        'email' => 'jane@example.com',
        'role' => OrganizationRole::Responsible,
        'token' => OrganizationInvitation::hashToken('promote'),
    ]);
    app(AcceptInvitation::class)->accept('promote', $member);

    expect($member->isResponsibleOf($this->organization))->toBeTrue();

    OrganizationInvitation::factory()->for($this->organization)->create([
        'email' => 'jane@example.com',
        'role' => OrganizationRole::Member,
        'token' => OrganizationInvitation::hashToken('demote'),
    ]);
    app(AcceptInvitation::class)->accept('demote', $member);

    expect($member->isResponsibleOf($this->organization))->toBeTrue();
});

test('guests following the invitation link see the organization and can create an account or log in', function () {
    $token = invite();

    $this->get(route('organizations.invitations.show', ['token' => $token]))
        ->assertOk()
        ->assertSee('Acme')
        ->assertSee(route('register'), false)
        ->assertSee(route('login'), false)
        ->assertDontSee(route('organizations.invitations.accept', $token), false);

    expect(session('url.intended'))->toBe(route('organizations.invitations.show', ['token' => $token]))
        ->and(session(OrganizationInvitation::SESSION_KEY))->toBe($token);
});

test('guests following an invalid invitation link only see that it is invalid', function () {
    $this->get(route('organizations.invitations.show', ['token' => 'nope']))
        ->assertOk()
        ->assertSee(__('ui.organizations.invitations.feedback.invalid-invitation'))
        ->assertDontSee(__('ui.organizations.invitations.show.actions.register'));

    expect(session()->has(OrganizationInvitation::SESSION_KEY))->toBeFalse();
});

test('people with an unverified account are asked to verify it first', function () {
    $token = invite('jane@example.com');

    $this->actingAs(User::factory()->unverified()->create(['email' => 'jane@example.com']))
        ->get(route('organizations.invitations.show', ['token' => $token]))
        ->assertRedirect(route('verification.notice'));
});

test('the registration page keeps the invited address when an invitation is being followed', function () {
    $token = invite('new@example.com');

    $this->get(route('organizations.invitations.show', ['token' => $token]));

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('value="new@example.com"', false)
        ->assertSee('readonly', false)
        ->assertSee(__('ui.auth.register.invitation', ['organization' => 'Acme']));

    $this->flushSession();

    $this->get(route('register'))->assertOk()->assertDontSee('readonly', false);
});

test('the invitation page shows the organization to the invited user', function () {
    $token = invite();
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    $this->actingAs($jane)->get(route('organizations.invitations.show', ['token' => $token]))
        ->assertOk()
        ->assertSee('Acme')
        ->assertSee(route('organizations.invitations.accept', $token), false)
        ->assertDontSee('ui.');
});

test('the invitation page does not reveal anything to another user', function () {
    $token = invite();

    $this->actingAs(User::factory()->create())->get(route('organizations.invitations.show', ['token' => $token]))
        ->assertOk()
        ->assertDontSee('Acme')
        ->assertSee(__('ui.organizations.invitations.feedback.invalid-invitation'))
        ->assertDontSee(route('organizations.invitations.accept', $token), false);
});

test('users accept an invitation through the form and see a confirmation', function () {
    $token = invite();
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    $this->actingAs($jane)
        ->followingRedirects()
        ->post(route('organizations.invitations.accept', $token))
        ->assertOk()
        ->assertSee(__('ui.organizations.invitations.feedback.invitation-accepted'));

    expect($jane->belongsToOrganization($this->organization))->toBeTrue();
});

test('accepting with the wrong account is refused with an error', function () {
    $token = invite();
    $other = User::factory()->create();

    $this->actingAs($other)
        ->from(route('organizations.invitations.show', ['token' => $token]))
        ->post(route('organizations.invitations.accept', $token))
        ->assertRedirect(route('organizations.invitations.show', ['token' => $token]))
        ->assertSessionHasErrors('invitation');

    expect($other->belongsToOrganization($this->organization))->toBeFalse();
});

test('an invited person registers with the invited address, skips the verification email and accepts', function () {
    $token = invite('new@example.com');

    $this->get(route('organizations.invitations.show', ['token' => $token]));
    $this->post(route('register.store'), [
        'name' => 'New Person',
        'email' => 'new@example.com',
        'password' => 'a-long-enough-password',
        'password_confirmation' => 'a-long-enough-password',
    ])->assertRedirect(route('organizations.invitations.show', ['token' => $token]));

    $user = User::where('email', 'new@example.com')->sole();

    expect($user->hasVerifiedEmail())->toBeTrue();
    Notification::assertNotSentTo($user, VerifyEmail::class);

    $this->post(route('organizations.invitations.accept', $token))
        ->assertRedirect(route('organizations.show', ['organization' => $this->organization->uuid]));

    expect($user->belongsToOrganization($this->organization))->toBeTrue()
        ->and(session()->has(OrganizationInvitation::SESSION_KEY))->toBeFalse();
});

test('registering with another address still requires verification, even while following an invitation', function () {
    $token = invite('new@example.com');

    $this->get(route('organizations.invitations.show', ['token' => $token]));
    $this->post(route('register.store'), [
        'name' => 'Someone Else',
        'email' => 'someone.else@example.com',
        'password' => 'a-long-enough-password',
        'password_confirmation' => 'a-long-enough-password',
    ]);

    $user = User::where('email', 'someone.else@example.com')->sole();

    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);

    $this->post(route('organizations.invitations.accept', $token))->assertRedirect(route('verification.notice'));
    expect($user->belongsToOrganization($this->organization))->toBeFalse();
});
