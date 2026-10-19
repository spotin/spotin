<?php

use App\Actions\Organizations\ChangeMemberRole;
use App\Actions\Organizations\RemoveMember;
use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\InvitationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->other = Organization::factory()->create();

    $this->admin = User::factory()->admin()->create();
    $this->responsible = User::factory()->create();
    $this->member = User::factory()->create();
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->responsible, ['role' => OrganizationRole::Responsible]);
    $this->organization->users()->attach($this->member);
    $this->other->users()->attach($this->outsider, ['role' => OrganizationRole::Responsible]);
});

test('the last responsible user cannot be removed or demoted', function () {
    expect(fn () => app(RemoveMember::class)->remove($this->organization, $this->responsible))
        ->toThrow(ValidationException::class)
        ->and(fn () => app(ChangeMemberRole::class)->change($this->organization, $this->responsible, OrganizationRole::Member))
        ->toThrow(ValidationException::class)
        ->and($this->responsible->isResponsibleOf($this->organization))->toBeTrue();
});

test('a responsible user can be removed or demoted when another one remains', function () {
    $this->organization->users()->attach(User::factory()->create(), ['role' => OrganizationRole::Responsible]);

    app(ChangeMemberRole::class)->change($this->organization, $this->responsible, OrganizationRole::Member);

    expect($this->responsible->isResponsibleOf($this->organization))->toBeFalse();

    $second = $this->organization->responsibles()->sole();

    expect(fn () => app(RemoveMember::class)->remove($this->organization, $second))->toThrow(ValidationException::class);

    app(RemoveMember::class)->remove($this->organization, $this->responsible);

    expect($this->responsible->belongsToOrganization($this->organization))->toBeFalse();
});

test('members who do not belong to the organization cannot be removed or changed', function () {
    expect(fn () => app(RemoveMember::class)->remove($this->organization, $this->outsider))->toThrow(ValidationException::class)
        ->and(fn () => app(ChangeMemberRole::class)->change($this->organization, $this->outsider, OrganizationRole::Member))->toThrow(ValidationException::class);
});

test('responsible users can change the role of and remove members', function () {
    $this->actingAs($this->responsible)
        ->put(route('organizations.members.update', [$this->organization, $this->member]), ['role' => 'responsible'])
        ->assertRedirect()
        ->assertSessionHas('success', __('ui.organizations.members.feedback.member_role_updated'));

    expect($this->member->isResponsibleOf($this->organization))->toBeTrue();

    $this->actingAs($this->responsible)
        ->delete(route('organizations.members.destroy', [$this->organization, $this->member]))
        ->assertRedirect()
        ->assertSessionHas('success', __('ui.organizations.members.feedback.member_removed'));

    expect($this->member->belongsToOrganization($this->organization))->toBeFalse();
});

test('changing a role requires a valid role', function () {
    $this->actingAs($this->responsible)
        ->put(route('organizations.members.update', [$this->organization, $this->member]), ['role' => 'owner'])
        ->assertSessionHasErrors('role');
});

test('the last responsible user is protected through the routes too', function () {
    $this->actingAs($this->responsible)
        ->from('/back')
        ->delete(route('organizations.members.destroy', [$this->organization, $this->responsible]))
        ->assertRedirect('/back')
        ->assertSessionHasErrors('member');

    $this->actingAs($this->admin)
        ->from('/back')
        ->put(route('organizations.members.update', [$this->organization, $this->responsible]), ['role' => 'member'])
        ->assertSessionHasErrors('member');

    expect($this->responsible->isResponsibleOf($this->organization))->toBeTrue();
});

test('members and outsiders cannot manage members or invitations', function (string $user) {
    $invitation = Invitation::factory()->for($this->organization)->create();

    $this->actingAs($this->$user)->put(route('organizations.members.update', [$this->organization, $this->member]), ['role' => 'responsible'])->assertForbidden();
    $this->actingAs($this->$user)->delete(route('organizations.members.destroy', [$this->organization, $this->member]))->assertForbidden();
    $this->actingAs($this->$user)->post(route('organizations.invitations.store', $this->organization), ['email' => 'a@example.com', 'role' => 'member'])->assertForbidden();
    $this->actingAs($this->$user)->post(route('organizations.invitations.resend', [$this->organization, $invitation]))->assertForbidden();
    $this->actingAs($this->$user)->delete(route('organizations.invitations.destroy', [$this->organization, $invitation]))->assertForbidden();

    expect($this->member->belongsToOrganization($this->organization))->toBeTrue()
        ->and($invitation->fresh())->not->toBeNull();
})->with(['member', 'outsider']);

test('a responsible user of another organization cannot manage this one', function () {
    $this->actingAs($this->outsider)
        ->delete(route('organizations.members.destroy', [$this->organization, $this->member]))
        ->assertForbidden();
});

test('members are resolved within the organization only', function () {
    $this->actingAs($this->responsible)
        ->delete(route('organizations.members.destroy', [$this->organization, $this->outsider]))
        ->assertNotFound();
});

test('admins can manage the members of any organization', function () {
    $this->actingAs($this->admin)
        ->delete(route('organizations.members.destroy', [$this->organization, $this->member]))
        ->assertRedirect();

    expect($this->member->belongsToOrganization($this->organization))->toBeFalse();
});

test('responsible users can invite, resend and revoke', function () {
    Notification::fake();

    $this->actingAs($this->responsible)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => 'New@Example.com', 'role' => 'member'])
        ->assertRedirect()
        ->assertSessionHas('success', __('ui.organizations.invitations.feedback.invitation_sent'));

    $invitation = Invitation::sole();
    expect($invitation->email)->toBe('new@example.com');
    Notification::assertSentOnDemandTimes(InvitationNotification::class, 1);

    $this->actingAs($this->responsible)
        ->post(route('organizations.invitations.resend', [$this->organization, $invitation]))
        ->assertSessionHas('success', __('ui.organizations.invitations.feedback.invitation_sent'));
    Notification::assertSentOnDemandTimes(InvitationNotification::class, 2);

    $this->actingAs($this->responsible)
        ->delete(route('organizations.invitations.destroy', [$this->organization, $invitation]))
        ->assertSessionHas('success', __('ui.organizations.invitations.feedback.invitation_revoked'));

    expect(Invitation::count())->toBe(0);
});

test('invitations of another organization cannot be touched', function () {
    $invitation = Invitation::factory()->for($this->other)->create();

    $this->actingAs($this->responsible)
        ->delete(route('organizations.invitations.destroy', [$this->organization, $invitation]))
        ->assertNotFound();

    expect($invitation->fresh())->not->toBeNull();
});

test('inviting requires a valid role and email', function () {
    $this->actingAs($this->responsible)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => 'a@example.com', 'role' => 'owner'])
        ->assertSessionHasErrors('role');

    $this->actingAs($this->responsible)
        ->post(route('organizations.invitations.store', $this->organization), ['email' => 'nope', 'role' => 'member'])
        ->assertSessionHasErrors('email');
});

test('members can leave an organization, the last responsible user cannot', function () {
    $this->actingAs($this->member)
        ->delete(route('organizations.leave', $this->organization))
        ->assertRedirect(route('organizations.index', ['mine' => 1]))
        ->assertSessionHas('success', __('ui.organizations.members.feedback.organization_left'));

    expect($this->member->belongsToOrganization($this->organization))->toBeFalse();

    $this->actingAs($this->responsible)
        ->from('/back')
        ->delete(route('organizations.leave', $this->organization))
        ->assertSessionHasErrors('member');

    expect($this->responsible->belongsToOrganization($this->organization))->toBeTrue();
});

test('people who do not belong to an organization cannot leave it', function () {
    $this->actingAs($this->outsider)->delete(route('organizations.leave', $this->organization))->assertForbidden();
    $this->actingAs($this->admin)->delete(route('organizations.leave', $this->organization))->assertForbidden();
});

test('guests are sent to log in', function () {
    $this->delete(route('organizations.leave', $this->organization))->assertRedirect(route('login'));
});

test('only people who can manage members get an invite button on the members page', function () {
    $invitePage = route('organizations.invitations.create', ['organization' => $this->organization->uuid]);

    $this->actingAs($this->responsible)->get("/organizations/{$this->organization->uuid}/members")
        ->assertOk()
        ->assertSee($invitePage, false);

    $this->actingAs($this->member)->get("/organizations/{$this->organization->uuid}/members")
        ->assertOk()
        ->assertDontSee($invitePage, false);
});

test('the members page says so when the organization has no members', function () {
    $empty = Organization::factory()->create();

    $this->actingAs($this->admin)->get("/organizations/{$empty->uuid}/members")
        ->assertOk()
        ->assertSee(__('ui.organizations.members.index.table.empty'));

    $this->get("/organizations/{$this->organization->uuid}/members")
        ->assertOk()
        ->assertDontSee(__('ui.organizations.members.index.table.empty'));
});

test('removing a member and revoking an invitation are confirmed in a modal', function () {
    $invitation = Invitation::factory()->for($this->organization)->create();

    $this->actingAs($this->responsible)->get("/organizations/{$this->organization->uuid}/members")
        ->assertOk()
        ->assertSee('id="remove-member-'.$this->member->id.'-modal"', false)
        ->assertDontSee('id="remove-member-'.$this->responsible->id.'-modal"', false);

    $this->get("/organizations/{$this->organization->uuid}/invitations")
        ->assertOk()
        ->assertSee('id="revoke-invitation-'.$invitation->id.'-modal"', false);
});
