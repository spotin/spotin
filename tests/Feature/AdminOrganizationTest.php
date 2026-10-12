<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
});

test('admin pages render in every language without untranslated keys', function (string $locale) {
    $this->actingAs($this->admin);
    $this->withUnencryptedCookie('locale', $locale);

    $this->get(route('admin.organizations.index'))->assertOk()->assertSee('Acme')->assertDontSee('ui.');
    $this->get(route('admin.organizations.create'))->assertOk()->assertDontSee('ui.');
    $this->get(route('admin.organizations.edit', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee('value="Acme"', false)
        ->assertDontSee('ui.');
})->with(['en', 'fr']);

test('the administration menu is shown to admins only, in both navigations', function () {
    $this->actingAs($this->admin)->get(route('dashboard'))
        ->assertSee(__('ui.admin.title'))
        ->assertSee('id="admin-popover"', false)
        ->assertSeeInOrder(['id="admin-popover"', route('admin.organizations.index')], false)
        ->assertSee(route('admin.organizations.index'), false);
    $this->actingAs($this->user)->get(route('dashboard'))
        ->assertDontSee(__('ui.admin.title'))
        ->assertDontSee('admin-popover', false)
        ->assertDontSee(route('admin.organizations.index'), false);
});

test('the index shows an empty state and the member count', function () {
    $this->organization->users()->attach($this->user);

    $this->actingAs($this->admin)->get(route('admin.organizations.index'))->assertSee('Acme');

    $this->organization->delete();

    $this->get(route('admin.organizations.index'))->assertSee(__('ui.admin.organizations.index.table.empty'));
});

test('other users cannot reach the admin pages or actions', function () {
    $this->actingAs($this->user);

    $this->get(route('admin.organizations.index'))->assertForbidden();
    $this->get(route('admin.organizations.create'))->assertForbidden();
    $this->get(route('admin.organizations.edit', ['organization' => $this->organization->uuid]))->assertForbidden();
    $this->post(route('admin.organizations.store'), ['name' => 'Nope'])->assertForbidden();
    $this->put(route('admin.organizations.update', $this->organization), ['name' => 'Nope'])->assertForbidden();
    $this->delete(route('admin.organizations.destroy', $this->organization))->assertForbidden();

    expect(Organization::pluck('name')->all())->toBe(['Acme']);
});

test('responsible users cannot edit or delete their own organization', function () {
    $this->organization->users()->attach($this->user, ['role' => OrganizationRole::Responsible]);

    $this->actingAs($this->user)->put(route('admin.organizations.update', $this->organization), ['name' => 'Nope'])->assertForbidden();
    $this->delete(route('admin.organizations.destroy', $this->organization))->assertForbidden();
});

test('guests are sent to log in', function () {
    $this->get(route('admin.organizations.index'))->assertRedirect(route('login'));
    $this->post(route('admin.organizations.store'), ['name' => 'Nope'])->assertRedirect(route('login'));
});

test('admins can create an organization and land on its page', function () {
    $response = $this->actingAs($this->admin)
        ->post(route('admin.organizations.store'), ['name' => '  Globex ']);

    $organization = Organization::where('name', 'Globex')->sole();

    $response->assertRedirect(route('organizations.show', ['organization' => $organization->uuid]))
        ->assertSessionHas('status', 'organization-created');

    $this->get(route('organizations.show', ['organization' => $organization->uuid]))->assertOk()->assertSee(__('ui.admin.organizations.feedback.organization-created'));

    expect($organization->users)->toBeEmpty()
        ->and($organization->invitations)->toBeEmpty();
});

test('creating an organization validates the input and keeps nothing on failure', function (array $input) {
    $this->actingAs($this->admin)
        ->from(route('admin.organizations.create'))
        ->post(route('admin.organizations.store'), $input)
        ->assertRedirect(route('admin.organizations.create'))
        ->assertSessionHasErrors('name');

    expect(Organization::count())->toBe(1);
})->with([
    'missing name' => [['name' => '']],
    'too short name' => [['name' => 'A']],
    'too long name' => [['name' => str_repeat('a', 101)]],
]);

test('organization names must be unique', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.organizations.create'))
        ->post(route('admin.organizations.store'), ['name' => ' Acme '])
        ->assertRedirect(route('admin.organizations.create'))
        ->assertSessionHasErrors('name');

    expect(Organization::count())->toBe(1);
});

test('an organization can be saved again under its own name but not under another one', function () {
    Organization::factory()->create(['name' => 'Globex']);

    $this->actingAs($this->admin)
        ->put(route('admin.organizations.update', $this->organization), ['name' => 'Acme'])
        ->assertSessionHasNoErrors();

    $this->put(route('admin.organizations.update', $this->organization), ['name' => 'Globex'])
        ->assertSessionHasErrors('name');

    expect($this->organization->fresh()->name)->toBe('Acme');
});

test('admins can rename an organization', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.organizations.update', $this->organization), ['name' => ' Acme Corp '])
        ->assertRedirect()
        ->assertSessionHas('status', 'organization-updated');

    expect($this->organization->fresh()->name)->toBe('Acme Corp');
});

test('renaming validates the name', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.organizations.update', $this->organization), ['name' => 'A'])
        ->assertSessionHasErrors('name');

    expect($this->organization->fresh()->name)->toBe('Acme');
});

test('deleting an organization removes everything linked to it', function () {
    $this->organization->users()->attach($this->user);
    OrganizationInvitation::factory()->for($this->organization)->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.organizations.destroy', $this->organization))
        ->assertRedirect(route('admin.organizations.index'))
        ->assertSessionHas('status', 'organization-deleted');

    expect(Organization::count())->toBe(0)
        ->and(OrganizationInvitation::count())->toBe(0)
        ->and($this->user->fresh()->organizations)->toBeEmpty();
});

test('admins get edit and delete buttons on the list and on the organization page', function () {
    $uuid = $this->organization->uuid;

    $this->actingAs($this->admin)->get('/admin/organizations')
        ->assertOk()
        ->assertSee('id="delete-organization-'.$uuid.'-modal"', false)
        ->assertSee(route('admin.organizations.destroy', $this->organization), false);

    $this->get('/organizations/'.$uuid)
        ->assertOk()
        ->assertSee(route('admin.organizations.edit', ['organization' => $uuid]), false)
        ->assertSee('id="delete-organization-'.$uuid.'-modal"', false);
});

test('responsible users do not get edit and delete buttons on their organization page', function () {
    $this->organization->users()->attach($this->user, ['role' => OrganizationRole::Responsible]);

    $this->actingAs($this->user)->get('/organizations/'.$this->organization->uuid)
        ->assertOk()
        ->assertDontSee('delete-organization-', false)
        ->assertDontSee('/edit', false);
});
