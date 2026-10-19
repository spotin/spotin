<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
});

test('the management pages render in every language without untranslated keys', function (string $locale) {
    $this->actingAs($this->admin);
    $this->withUnencryptedCookie('locale', $locale);

    $this->get(route('organizations.index'))->assertOk()->assertSee('Acme')->assertSee(route('organizations.create'), false)->assertDontSee('ui.');
    $this->get(route('organizations.create'))->assertOk()->assertDontSee('ui.');
    $this->get(route('organizations.edit', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee('value="Acme"', false)
        ->assertDontSee('ui.');
})->with(['en', 'fr']);

test('the list of organizations offers the creation to admins only', function () {
    $this->actingAs($this->user)->get(route('organizations.index'))->assertOk()->assertDontSee(route('organizations.create'), false);
    $this->actingAs($this->admin)->get(route('organizations.index'))->assertOk()->assertSee(route('organizations.create'), false);
});

test('other users cannot reach the admin pages or actions', function () {
    $this->actingAs($this->user);

    $this->get(route('organizations.create'))->assertForbidden();
    $this->get(route('organizations.edit', ['organization' => $this->organization->uuid]))->assertForbidden();
    $this->post(route('organizations.store'), ['name' => 'Nope'])->assertForbidden();
    $this->patch(route('organizations.update', $this->organization), ['name' => 'Nope'])->assertForbidden();
    $this->delete(route('organizations.destroy', $this->organization))->assertForbidden();

    expect(Organization::pluck('name')->all())->toBe(['Acme']);
});

test('responsible users cannot edit or delete their own organization', function () {
    $this->organization->users()->attach($this->user, ['role' => OrganizationRole::Responsible]);

    $this->actingAs($this->user)->patch(route('organizations.update', $this->organization), ['name' => 'Nope'])->assertForbidden();
    $this->delete(route('organizations.destroy', $this->organization))->assertForbidden();
});

test('guests are sent to log in', function () {
    $this->get(route('organizations.create'))->assertRedirect(route('login'));
    $this->post(route('organizations.store'), ['name' => 'Nope'])->assertRedirect(route('login'));
});

test('admins can create an organization and land on its page', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('organizations.store'), ['name' => '  Globex ']);

    $organization = Organization::where('name', 'Globex')->sole();

    $response->assertRedirect(route('organizations.show', ['organization' => $organization->uuid]))
        ->assertSessionHas('success', __('ui.organizations.feedback.organization_created'));

    $this->get(route('organizations.show', ['organization' => $organization->uuid]))->assertOk()->assertSee(__('ui.organizations.feedback.organization_created'));

    expect($organization->users)->toBeEmpty()
        ->and($organization->invitations)->toBeEmpty();
});

test('creating an organization validates the input and keeps nothing on failure', function (array $input) {
    $this->actingAs($this->admin)
        ->from(route('organizations.create'))
        ->post(route('organizations.store'), $input)
        ->assertRedirect(route('organizations.create'))
        ->assertSessionHasErrors('name');

    expect(Organization::count())->toBe(1);
})->with([
    'missing name' => [['name' => '']],
    'too short name' => [['name' => 'A']],
    'too long name' => [['name' => str_repeat('a', 101)]],
]);

test('organization names must be unique', function () {
    $this->actingAs($this->admin)
        ->from(route('organizations.create'))
        ->post(route('organizations.store'), ['name' => ' Acme '])
        ->assertRedirect(route('organizations.create'))
        ->assertSessionHasErrors('name');

    expect(Organization::count())->toBe(1);
});

test('an organization can be saved again under its own name but not under another one', function () {
    Organization::factory()->create(['name' => 'Globex']);

    $this->actingAs($this->admin)
        ->patch(route('organizations.update', $this->organization), ['name' => 'Acme'])
        ->assertSessionHasNoErrors();

    $this->patch(route('organizations.update', $this->organization), ['name' => 'Globex'])
        ->assertSessionHasErrors('name');

    expect($this->organization->fresh()->name)->toBe('Acme');
});

test('admins can rename an organization', function () {
    $this->actingAs($this->admin)
        ->patch(route('organizations.update', $this->organization), ['name' => ' Acme Corp '])
        ->assertRedirect()
        ->assertSessionHas('success', __('ui.organizations.feedback.organization_updated'));

    expect($this->organization->fresh()->name)->toBe('Acme Corp');
});

test('renaming validates the name', function () {
    $this->actingAs($this->admin)
        ->patch(route('organizations.update', $this->organization), ['name' => 'A'])
        ->assertSessionHasErrors('name');

    expect($this->organization->fresh()->name)->toBe('Acme');
});

test('deleting an organization removes everything linked to it', function () {
    $this->organization->users()->attach($this->user);
    Invitation::factory()->for($this->organization)->create();

    $this->actingAs($this->admin)
        ->delete(route('organizations.destroy', $this->organization))
        ->assertRedirect(route('organizations.index'))
        ->assertSessionHas('success', __('ui.organizations.feedback.organization_deleted'));

    $this->get(route('organizations.index'))->assertSee(__('ui.organizations.feedback.organization_deleted'));

    expect(Organization::count())->toBe(0)
        ->and(Invitation::count())->toBe(0)
        ->and($this->user->fresh()->organizations)->toBeEmpty();
});

test('admins get edit and delete buttons on the organization page', function () {
    $uuid = $this->organization->uuid;

    $this->actingAs($this->admin)->get('/organizations/'.$uuid)
        ->assertOk()
        ->assertSee(route('organizations.edit', ['organization' => $uuid]), false)
        ->assertSee('id="delete-organization-'.$uuid.'-modal"', false)
        ->assertSee(route('organizations.destroy', $this->organization), false);
});

test('responsible users do not get edit and delete buttons on their organization page', function () {
    $this->organization->users()->attach($this->user, ['role' => OrganizationRole::Responsible]);

    $this->actingAs($this->user)->get('/organizations/'.$this->organization->uuid)
        ->assertOk()
        ->assertDontSee('delete-organization-', false)
        ->assertDontSee('/edit', false);
});
