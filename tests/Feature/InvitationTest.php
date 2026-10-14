<?php

use App\Actions\Invitations\AcceptInvitation;
use App\Actions\Organizations\InviteToOrganization;
use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\InvitationNotification;
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

    Notification::assertSentOnDemand(InvitationNotification::class, function ($notification) use (&$token) {
        $token = (fn () => $this->token)->call($notification);

        return true;
    });

    return $token;
}

test('inviting stores a hashed token and emails the plain one', function () {
    $token = invite('jane@example.com', OrganizationRole::Responsible);

    $invitation = Invitation::sole();

    expect($invitation->email)->toBe('jane@example.com')
        ->and($invitation->role)->toBe(OrganizationRole::Responsible)
        ->and($invitation->token)->toBe(Invitation::hashToken($token))
        ->and($invitation->token)->not->toBe($token)
        ->and($invitation->inviter->is($this->inviter))->toBeTrue()
        ->and($invitation->expires_at->isSameDay(now()->addDays(7)))->toBeTrue();

    Notification::assertSentOnDemand(
        InvitationNotification::class,
        fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'jane@example.com',
    );
});

test('the invitation email links to the acceptance page', function () {
    $token = invite();

    $mail = (new InvitationNotification(Invitation::sole()->load('organization'), $token))
        ->toMail(new stdClass);

    expect($mail->actionUrl)->toBe(route('invitations.show', ['token' => $token]))
        ->and($mail->subject)->toContain('Acme');
});

test('inviting an address again replaces the pending invitation', function () {
    $first = invite(role: OrganizationRole::Member);
    $second = invite(role: OrganizationRole::Responsible);

    expect(Invitation::count())->toBe(1)
        ->and(Invitation::sole()->role)->toBe(OrganizationRole::Responsible)
        ->and(Invitation::findActiveByToken($first))->toBeNull()
        ->and(Invitation::findActiveByToken($second))->not->toBeNull();
});

test('resending issues a new token and a new expiry', function () {
    $old = invite();
    $invitation = Invitation::sole();
    $invitation->update(['expires_at' => now()->addHour()]);

    Notification::fake();
    app(InviteToOrganization::class)->resend($invitation, $this->inviter);

    expect(Invitation::findActiveByToken($old))->toBeNull()
        ->and($invitation->fresh()->expires_at->isSameDay(now()->addDays(7)))->toBeTrue();
    Notification::assertSentOnDemandTimes(InvitationNotification::class, 1);
});

test('inviting lowercases the address', function () {
    Notification::fake();

    $this->actingAs($this->inviter)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => ' Jane@Example.com ', 'role' => OrganizationRole::Member->value])
        ->assertSessionHasNoErrors();

    expect(Invitation::sole()->email)->toBe('jane@example.com');
});

test('inviting rejects invalid addresses', function (string $email) {
    Notification::fake();

    $this->actingAs($this->inviter)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => $email, 'role' => OrganizationRole::Member->value])
        ->assertSessionHasErrors('email');

    expect(Invitation::count())->toBe(0);
})->with(['not-an-email', 'invalid', '']);

test('inviting someone who already belongs to the organization is rejected', function () {
    Notification::fake();

    $this->actingAs($this->inviter)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => strtoupper($this->inviter->email), 'role' => OrganizationRole::Member->value])
        ->assertSessionHasErrors('email');

    expect(Invitation::count())->toBe(0);
});

test('a user with the invited verified address can accept and join with the invited role', function () {
    invite('jane@example.com', OrganizationRole::Responsible);
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    $organization = app(AcceptInvitation::class)->acceptInvitation(Invitation::sole(), $jane);

    expect($organization->is($this->organization))->toBeTrue()
        ->and($jane->isResponsibleOf($this->organization))->toBeTrue()
        ->and(Invitation::count())->toBe(0);
});

test('an invitation cannot be accepted twice', function () {
    invite();
    $invitation = Invitation::sole();
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    app(AcceptInvitation::class)->acceptInvitation($invitation, $jane);

    expect(fn () => app(AcceptInvitation::class)->acceptInvitation($invitation, $jane))->toThrow(ValidationException::class);
});

test('an invitation is refused to other users, unverified addresses and expired invitations', function (string $case) {
    invite();
    $user = match ($case) {
        'another user' => User::factory()->create(),
        'unverified address' => User::factory()->unverified()->create(['email' => 'jane@example.com']),
        'expired' => User::factory()->create(['email' => 'jane@example.com']),
    };

    if ($case === 'expired') {
        Invitation::sole()->update(['expires_at' => now()->subMinute()]);
    }

    expect(fn () => app(AcceptInvitation::class)->acceptInvitation(Invitation::sole(), $user))->toThrow(ValidationException::class)
        ->and($user->belongsToOrganization($this->organization))->toBeFalse();
})->with(['another user', 'unverified address', 'expired']);

test('a leftover invitation can promote an existing member but never demote a responsible user', function () {
    $member = User::factory()->create(['email' => 'jane@example.com']);
    $this->organization->users()->attach($member);

    $promotion = Invitation::factory()->for($this->organization)->create([
        'email' => 'jane@example.com',
        'role' => OrganizationRole::Responsible,
    ]);
    app(AcceptInvitation::class)->acceptInvitation($promotion, $member);

    expect($member->isResponsibleOf($this->organization))->toBeTrue();

    $demotion = Invitation::factory()->for($this->organization)->create([
        'email' => 'jane@example.com',
        'role' => OrganizationRole::Member,
    ]);
    app(AcceptInvitation::class)->acceptInvitation($demotion, $member);

    expect($member->isResponsibleOf($this->organization))->toBeTrue();
});

test('guests following the invitation link see the organization and can create an account or log in', function () {
    $token = invite();

    $this->get(route('invitations.show', ['token' => $token]))
        ->assertOk()
        ->assertSee('Acme')
        ->assertSee(route('register'), false)
        ->assertSee(route('login'), false);

    expect(session('url.intended'))->toBe(route('invitations.show', ['token' => $token]))
        ->and(session(Invitation::SESSION_KEY))->toBe($token);
});

test('guests following an invalid invitation link only see that it is invalid', function () {
    $this->get(route('invitations.show', ['token' => 'nope']))
        ->assertOk()
        ->assertSee(__('ui.invitations.feedback.invalid_invitation'))
        ->assertDontSee(__('ui.invitations.show.actions.register'));

    expect(session()->has(Invitation::SESSION_KEY))->toBeFalse();
});

test('people with an unverified account are asked to verify it first', function () {
    $token = invite('jane@example.com');

    $this->actingAs(User::factory()->unverified()->create(['email' => 'jane@example.com']))
        ->get(route('invitations.show', ['token' => $token]))
        ->assertRedirect(route('verification.notice'));
});

test('the registration page keeps the invited address when an invitation is being followed', function () {
    $token = invite('new@example.com');

    $this->get(route('invitations.show', ['token' => $token]));

    $this->get(route('register'))
        ->assertOk()
        ->assertSee('value="new@example.com"', false)
        ->assertSee('readonly', false)
        ->assertSee(__('ui.auth.register.invitation', ['organization' => 'Acme']));

    $this->flushSession();

    $this->get(route('register'))->assertOk()->assertDontSee('readonly', false);
});

test('people with an account are taken to their pending invitations, whoever the invitation is for', function (bool $invited) {
    $token = invite();
    $user = User::factory()->create($invited ? ['email' => 'jane@example.com'] : []);

    $this->actingAs($user)->get(route('invitations.show', ['token' => $token]))
        ->assertRedirect(route('invitations.index'));
})->with([true, false]);

test('people see a confirmation once they have accepted an invitation', function () {
    invite();
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    $this->actingAs($jane)
        ->followingRedirects()
        ->post(route('invitations.accept', ['invitation' => Invitation::sole()->id]))
        ->assertOk()
        ->assertSee(__('ui.invitations.feedback.invitation_accepted'));

    expect($jane->belongsToOrganization($this->organization))->toBeTrue();
});

test('an invited person registers with the invited address, skips the verification email and accepts', function () {
    $token = invite('new@example.com');

    $this->get(route('invitations.show', ['token' => $token]));
    $this->post(route('register.store'), [
        'name' => 'New Person',
        'email' => 'new@example.com',
        'password' => 'a-long-enough-password',
        'password_confirmation' => 'a-long-enough-password',
    ])->assertRedirect(route('invitations.show', ['token' => $token]));

    $user = User::where('email', 'new@example.com')->sole();

    expect($user->hasVerifiedEmail())->toBeTrue();
    Notification::assertNotSentTo($user, VerifyEmail::class);

    $this->get(route('invitations.show', ['token' => $token]))
        ->assertRedirect(route('invitations.index'));

    $this->post(route('invitations.accept', ['invitation' => Invitation::sole()->id]))
        ->assertRedirect(route('organizations.show', ['organization' => $this->organization->uuid]));

    expect($user->belongsToOrganization($this->organization))->toBeTrue()
        ->and(session()->has(Invitation::SESSION_KEY))->toBeFalse();
});

test('registering with another address still requires verification, even while following an invitation', function () {
    $token = invite('new@example.com');

    $this->get(route('invitations.show', ['token' => $token]));
    $this->post(route('register.store'), [
        'name' => 'Someone Else',
        'email' => 'someone.else@example.com',
        'password' => 'a-long-enough-password',
        'password_confirmation' => 'a-long-enough-password',
    ]);

    $user = User::where('email', 'someone.else@example.com')->sole();

    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);

    $this->post(route('invitations.accept', ['invitation' => Invitation::sole()->id]))
        ->assertRedirect(route('verification.notice'));
    expect($user->belongsToOrganization($this->organization))->toBeFalse();
});

test('the invitations of the user live at /invitations', function () {
    expect(route('invitations.index', absolute: false))->toBe('/invitations')
        ->and(route('invitations.show', ['token' => 'abc'], absolute: false))->toBe('/invitations/abc')
        ->and(route('invitations.accept', ['invitation' => 1], absolute: false))->toBe('/invitations/1/accept')
        ->and(route('invitations.decline', ['invitation' => 1], absolute: false))->toBe('/invitations/1');
});
