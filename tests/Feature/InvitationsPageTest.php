<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->acme = Organization::factory()->create(['name' => 'Acme']);
    $this->globex = Organization::factory()->create(['name' => 'Globex']);
    $this->inviter = User::factory()->create(['name' => 'Ina Inviter']);
    $this->jane = User::factory()->create(['email' => 'jane@example.com']);

    $this->invite = fn (Organization $organization, string $email = 'jane@example.com', array $attributes = []) => Invitation::factory()
        ->for($organization)
        ->create(['email' => $email, 'role' => OrganizationRole::Member, 'invited_by' => $this->inviter->id, ...$attributes]);
});

test('people see the active invitations sent to their address, and only those', function () {
    ($this->invite)($this->acme);
    ($this->invite)($this->globex, 'jane@example.com', ['role' => OrganizationRole::Responsible]);
    Invitation::factory()->for(Organization::factory()->create(['name' => 'Initech']))->expired()->create(['email' => 'jane@example.com']);
    Invitation::factory()->for(Organization::factory()->create(['name' => 'Umbrella']))->create(['email' => 'someone.else@example.com']);

    $this->actingAs($this->jane)->get(route('invitations.index'))
        ->assertOk()
        ->assertSee('Acme')
        ->assertSee('Globex')
        ->assertSee('Ina Inviter')
        ->assertDontSee('Initech')
        ->assertDontSee('Umbrella')
        ->assertDontSee('ui.');
});

test('the page says so when there is no pending invitation', function () {
    $this->actingAs($this->jane)->get(route('invitations.index'))
        ->assertOk()
        ->assertSee(__('ui.invitations.index.table.empty'));
});

test('guests are sent to log in', function () {
    $this->get(route('invitations.index'))->assertRedirect(route('login'));
});

test('an unverified address never has pending invitations', function () {
    ($this->invite)($this->acme, 'jane@example.com');

    $unverified = User::factory()->unverified()->create(['email' => 'jane.unverified@example.com']);
    ($this->invite)($this->globex, 'jane.unverified@example.com');

    expect(Invitation::pendingFor($unverified)->count())->toBe(0)
        ->and(Invitation::pendingFor($this->jane)->count())->toBe(1);
});

test('people accept an invitation from their pending invitations', function () {
    $invitation = ($this->invite)($this->acme, 'jane@example.com', ['role' => OrganizationRole::Responsible]);

    $this->actingAs($this->jane)
        ->post(route('invitations.accept', ['invitation' => $invitation->id]))
        ->assertRedirect(route('organizations.show', ['organization' => $this->acme->uuid]))
        ->assertSessionHas('success', __('ui.invitations.feedback.invitation_accepted'));

    expect($this->jane->isResponsibleOf($this->acme))->toBeTrue()
        ->and(Invitation::count())->toBe(0);
});

test('people decline an invitation from their pending invitations', function () {
    $invitation = ($this->invite)($this->acme);

    $this->actingAs($this->jane)
        ->from(route('invitations.index'))
        ->delete(route('invitations.decline', ['invitation' => $invitation->id]))
        ->assertRedirect(route('invitations.index'))
        ->assertSessionHas('success', __('ui.invitations.feedback.invitation_declined'));

    expect(Invitation::count())->toBe(0)
        ->and($this->jane->belongsToOrganization($this->acme))->toBeFalse();
});

test('nobody can accept or decline an invitation that is not theirs or has expired', function (string $case) {
    $invitation = match ($case) {
        'another address' => ($this->invite)($this->acme, 'someone.else@example.com'),
        'expired' => ($this->invite)($this->acme, 'jane@example.com', ['expires_at' => now()->subMinute()]),
        'unknown' => new Invitation(['id' => 9999]),
    };
    $id = $case === 'unknown' ? 9999 : $invitation->id;

    $this->actingAs($this->jane);

    $this->post(route('invitations.accept', ['invitation' => $id]))->assertNotFound();
    $this->delete(route('invitations.decline', ['invitation' => $id]))->assertNotFound();

    expect($this->jane->belongsToOrganization($this->acme))->toBeFalse()
        ->and(Invitation::count())->toBe($case === 'unknown' ? 0 : 1);
})->with(['another address', 'expired', 'unknown']);
