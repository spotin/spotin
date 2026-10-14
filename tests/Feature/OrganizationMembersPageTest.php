<?php

use App\Enums\OrganizationRole;
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

test('responsible users see the members with their emails and the management forms', function () {
    $content = $this->actingAs($this->responsible)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee('Rita Responsible')
        ->assertSee(emailHtml('mike@example.com'), false)
        ->getContent();

    $memberUrl = route('organizations.members.destroy', ['organization' => $this->organization->uuid, 'user' => $this->member->id]);
    $ownUrl = route('organizations.members.destroy', ['organization' => $this->organization->uuid, 'user' => $this->responsible->id]);

    // The role switch and the remove button of another member share a URL. One's own row has
    // neither, since they are the only responsible user and cannot remove themselves.
    expect(substr_count($content, $memberUrl))->toBe(2)
        ->and(substr_count($content, $ownUrl))->toBe(0);
});

test('managers change a role with a select in the Role column and a Save button in the Actions column', function () {
    $second = User::factory()->create(['name' => 'Sam Second']);
    $this->organization->users()->attach($second, ['role' => OrganizationRole::Responsible]);

    $content = $this->actingAs($this->responsible)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee(__('ui.organizations.members.index.table.actions.save'))
        ->getContent();

    // The select and its Save button are tied together by the id of the form, even though they sit in different cells.
    expect($content)->toMatch('/<select\s[^>]*form="role-'.$this->member->id.'"/')
        ->and($content)->toMatch('/<form\s+id="role-'.$this->member->id.'"/');

    // Every row of a member who can change role has both, with the same id.
    expect(substr_count($content, 'form="role-'.$this->member->id.'"'))->toBe(1)
        ->and(substr_count($content, 'id="role-'.$this->member->id.'"'))->toBe(1)
        ->and(substr_count($content, 'id="role-'.$second->id.'"'))->toBe(1);
});

test('the select of the only responsible user is disabled and has no Save button', function () {
    $content = $this->actingAs($this->admin)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->getContent();

    expect($content)->toMatch('/<select\s[^>]*form="role-'.$this->responsible->id.'"[^>]*disabled/s')
        ->and($content)->not->toContain('id="role-'.$this->responsible->id.'"')
        ->and($content)->toContain('id="role-'.$this->member->id.'"');
});

test('changing the role with the select and the Save button works end to end', function () {
    $this->actingAs($this->responsible)
        ->put(route('organizations.members.update', ['organization' => $this->organization->uuid, 'user' => $this->member->id]), ['role' => 'responsible'])
        ->assertRedirect();

    expect($this->member->isResponsibleOf($this->organization))->toBeTrue();
});

test('members see the names and roles only, without emails or management forms', function () {
    $this->actingAs($this->member)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee('Rita Responsible')
        ->assertSee(__('ui.organizations.common.forms.fields.role.options.responsible'))
        ->assertDontSee('rita@example.com')
        ->assertDontSee('name="role"', false);
});

test('admins can see and manage the members of any organization', function () {
    $this->actingAs($this->admin)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee(emailHtml('mike@example.com'), false)
        ->assertSee('name="role"', false);
});

test('every member gets a Leave button on their own row and a Remove button on the others', function () {
    $leave = route('organizations.leave', ['organization' => $this->organization->uuid]);
    $remove = fn (User $user) => route('organizations.members.destroy', ['organization' => $this->organization->uuid, 'user' => $user->id]);

    // A plain member can only leave.
    $this->actingAs($this->member)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee('id="leave-organization-'.$this->organization->uuid.'-modal"', false)
        ->assertSee($leave, false)
        ->assertDontSee($remove($this->responsible), false);

    // A responsible user can also remove the others, but not themselves.
    $this->organization->users()->updateExistingPivot($this->member->id, ['role' => OrganizationRole::Responsible]);

    $this->actingAs($this->responsible)->get(($this->url)('organizations.members.index'))
        ->assertSee('id="leave-organization-'.$this->organization->uuid.'-modal"', false)
        ->assertSee($leave, false)
        ->assertSee($remove($this->member), false);
});

test('the last responsible user has a disabled Leave button and is told why', function () {
    $this->actingAs($this->responsible)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertDontSee('leave-organization-', false)
        ->assertDontSee(route('organizations.leave', ['organization' => $this->organization->uuid]), false)
        ->assertSee('aria-describedby="last-responsible-hint"', false)
        ->assertSee(__('ui.organizations.members.index.table.actions.leave.last_responsible'));
});

test('admins who are not members have no Leave button', function () {
    $this->actingAs($this->admin)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertDontSee('leave-organization-', false)
        ->assertDontSee(route('organizations.leave', ['organization' => $this->organization->uuid]), false);
});
