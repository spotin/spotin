<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->member = User::factory()->create();
    $this->outsider = User::factory()->create();

    $this->acme = Organization::factory()->create(['name' => 'Acme']);
    $this->globex = Organization::factory()->create(['name' => 'Globex']);
    $this->acme->users()->attach($this->member, ['role' => OrganizationRole::Member]);

    $this->abilities = ['organizations:read', 'organizations:create', 'organizations:update', 'organizations:delete'];
});

test('the organizations are listed with their spot count, sorted by name and paginated', function () {
    Spot::factory()->for($this->acme)->count(2)->create();
    Organization::factory()->count(30)->create();

    Sanctum::actingAs($this->member, ['organizations:read']);

    $this->getJson('/api/v1/organizations')
        ->assertOk()
        ->assertJsonCount(25, 'data')
        ->assertJsonPath('meta.per_page', 25)
        ->assertJsonPath('meta.total', 32)
        ->assertJsonStructure(['data' => [['id', 'name', 'spots_count', 'created_at', 'updated_at']], 'links', 'meta']);

    $this->getJson('/api/v1/organizations?query=acme')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $this->acme->uuid)
        ->assertJsonPath('data.0.name', 'Acme')
        ->assertJsonPath('data.0.spots_count', 2);
});

test('the organizations can be filtered to the ones of the user', function () {
    Sanctum::actingAs($this->member, ['organizations:read']);

    $this->getJson('/api/v1/organizations?mine=1')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Acme');
});

test('the list filters are validated', function () {
    Sanctum::actingAs($this->member, ['organizations:read']);

    $this->getJson('/api/v1/organizations?query[]=x')->assertUnprocessable()->assertJsonValidationErrors('query');
    $this->getJson('/api/v1/organizations?query='.str_repeat('a', 101))->assertUnprocessable()->assertJsonValidationErrors('query');
    $this->getJson('/api/v1/organizations?mine=maybe')->assertUnprocessable()->assertJsonValidationErrors('mine');
});

test('an organization can be shown by its uuid', function () {
    Sanctum::actingAs($this->outsider, ['organizations:read']);

    $this->getJson('/api/v1/organizations/'.$this->acme->uuid)
        ->assertOk()
        ->assertJsonPath('data.id', $this->acme->uuid)
        ->assertJsonPath('data.name', 'Acme')
        ->assertJsonPath('data.spots_count', 0);

    $this->getJson('/api/v1/organizations/unknown')->assertNotFound();
});

test('a token needs the matching ability for every action', function (string $method, string $ability) {
    Sanctum::actingAs($this->admin, array_values(array_diff($this->abilities, [$ability])));

    $url = '/api/v1/organizations'.(in_array($method, ['putJson', 'deleteJson']) ? '/'.$this->acme->uuid : '');

    $this->$method($url, ['name' => 'Whatever'])->assertForbidden();

    expect(Organization::where('name', 'Whatever')->exists())->toBeFalse()
        ->and(Organization::whereKey($this->acme->id)->exists())->toBeTrue();
})->with([
    'list' => ['getJson', 'organizations:read'],
    'create' => ['postJson', 'organizations:create'],
    'update' => ['putJson', 'organizations:update'],
    'delete' => ['deleteJson', 'organizations:delete'],
]);

test('administrators can create, rename and delete organizations', function () {
    Sanctum::actingAs($this->admin, $this->abilities);

    $uuid = $this->postJson('/api/v1/organizations', ['name' => 'Initech'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Initech')
        ->json('data.id');

    expect(Organization::firstWhere('uuid', $uuid)->name)->toBe('Initech');

    $this->putJson('/api/v1/organizations/'.$uuid, ['name' => 'Initech Corp'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Initech Corp');

    $this->patchJson('/api/v1/organizations/'.$uuid, ['name' => 'Initech Inc'])->assertOk();

    $this->deleteJson('/api/v1/organizations/'.$uuid)->assertNoContent();

    expect(Organization::firstWhere('uuid', $uuid))->toBeNull();
});

test('the abilities of a token never exceed the rights of its owner', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->postJson('/api/v1/organizations', ['name' => 'Initech'])->assertForbidden();
    $this->putJson('/api/v1/organizations/'.$this->acme->uuid, ['name' => 'Renamed'])->assertForbidden();
    $this->deleteJson('/api/v1/organizations/'.$this->acme->uuid)->assertForbidden();

    expect($this->acme->fresh()->name)->toBe('Acme')
        ->and(Organization::count())->toBe(2);
});

test('the organization input is validated', function () {
    Sanctum::actingAs($this->admin, $this->abilities);

    $this->postJson('/api/v1/organizations', [])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson('/api/v1/organizations', ['name' => 'A'])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson('/api/v1/organizations', ['name' => 'Acme'])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->putJson('/api/v1/organizations/'.$this->acme->uuid, ['name' => 'Globex'])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->putJson('/api/v1/organizations/'.$this->acme->uuid, ['name' => 'Acme'])->assertOk();
});

test('tokens are required', function () {
    $this->getJson('/api/v1/organizations')->assertUnauthorized();
    $this->postJson('/api/v1/organizations', ['name' => 'Initech'])->assertUnauthorized();
});
