<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
    $this->other = Organization::factory()->create(['name' => 'Globex']);

    $this->admin = User::factory()->admin()->create();
    $this->responsible = User::factory()->create(['name' => 'Rita Responsible', 'email' => 'rita@example.com']);
    $this->member = User::factory()->create(['name' => 'Mike Member', 'email' => 'mike@example.com']);
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->responsible, ['role' => OrganizationRole::Responsible]);
    $this->organization->users()->attach($this->member);
    $this->other->users()->attach($this->outsider);

    $this->url = fn (string $route, ?Organization $organization = null) => route($route, ['organization' => ($organization ?? $this->organization)->uuid]);
});

test('the invitations page lists the invitations and links to the invitation form', function () {
    Invitation::factory()->for($this->organization)->create(['email' => 'pending@example.com']);
    Invitation::factory()->for($this->organization)->expired()->create(['email' => 'old@example.com']);
    Invitation::factory()->for($this->other)->create(['email' => 'elsewhere@example.com']);

    $this->actingAs($this->responsible)->get(($this->url)('organizations.invitations.index'))
        ->assertOk()
        ->assertSee(emailHtml('pending@example.com'), false)
        ->assertSee(emailHtml('old@example.com'), false)
        ->assertSee(__('ui.organizations.invitations.index.table.expired'))
        ->assertDontSee('elsewhere@example.com')
        ->assertSee(($this->url)('organizations.invitations.create'), false);
});

test('the invitations page tells when there is no invitation', function () {
    $this->actingAs($this->responsible)->get(($this->url)('organizations.invitations.index'))
        ->assertSee(__('ui.organizations.invitations.index.table.empty'));
});

test('the invitation form posts to the invitation route', function () {
    $this->actingAs($this->responsible)->get(($this->url)('organizations.invitations.create'))
        ->assertOk()
        ->assertSee(route('organizations.invitations.store', ['organization' => $this->organization->uuid]), false)
        ->assertSee('name="email"', false)
        ->assertSee('name="role"', false);
});

test('inviting someone who already belongs shows the error on the invitation form', function () {
    $this->actingAs($this->responsible)
        ->followingRedirects()
        ->from(($this->url)('organizations.invitations.create'))
        ->post(route('organizations.invitations.store', ['organization' => $this->organization->uuid]), ['email' => 'mike@example.com', 'role' => 'member'])
        ->assertSee(__('ui.organizations.invitations.feedback.already_member'));
});

test('sending an invitation brings back to the list of invitations', function () {
    $this->actingAs($this->responsible)
        ->post(route('organizations.invitations.store', ['organization' => $this->organization->uuid]), ['email' => 'new@example.com', 'role' => 'member'])
        ->assertRedirect(($this->url)('organizations.invitations.index'));
});
