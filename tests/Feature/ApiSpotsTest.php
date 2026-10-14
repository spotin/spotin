<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->member = User::factory()->create(['name' => 'Mike Member']);
    $this->outsider = User::factory()->create();

    $this->acme = Organization::factory()->create(['name' => 'Acme']);
    $this->globex = Organization::factory()->create(['name' => 'Globex']);
    $this->acme->users()->attach($this->member, ['role' => OrganizationRole::Member]);

    $this->harbour = Spot::factory()->for($this->acme)->create(['name' => 'Harbour', 'created_by' => $this->member->id]);
    $this->lighthouse = Spot::factory()->for($this->globex)->create(['name' => 'Lighthouse']);

    $this->abilities = ['spots:read', 'spots:create', 'spots:update', 'spots:delete'];
});

test('the spots are listed with their organization, sorted by name and paginated', function () {
    Spot::factory()->for($this->globex)->count(30)->create();

    Sanctum::actingAs($this->member, ['spots:read']);

    $this->getJson('/api/v1/spots')
        ->assertOk()
        ->assertJsonCount(25, 'data')
        ->assertJsonPath('meta.total', 32)
        ->assertJsonStructure(['data' => [['id', 'name', 'organization' => ['id', 'name'], 'created_at', 'updated_at']], 'links', 'meta']);
});

test('the spots can be searched and filtered to the ones the user created', function () {
    Spot::factory()->for($this->acme)->create(['name' => 'Harbour Two']);

    Sanctum::actingAs($this->member, ['spots:read']);

    $names = fn (string $url) => collect($this->getJson($url)->assertOk()->json('data'))->pluck('name')->all();

    expect($names('/api/v1/spots?query=harbour'))->toBe(['Harbour', 'Harbour Two'])
        ->and($names('/api/v1/spots?mine=1'))->toBe(['Harbour'])
        ->and($names('/api/v1/spots?query=harbour&mine=1'))->toBe(['Harbour']);

    $this->getJson('/api/v1/spots?query[]=x')->assertUnprocessable()->assertJsonValidationErrors('query');
});

test('the spots of an organization are listed under the organization, with the same filters', function () {
    Spot::factory()->for($this->acme)->create(['name' => 'Harbour Two']);

    Sanctum::actingAs($this->member, ['spots:read']);

    $names = fn (string $url) => collect($this->getJson($url)->assertOk()->json('data'))->pluck('name')->all();

    expect($names('/api/v1/organizations/'.$this->acme->uuid.'/spots'))->toBe(['Harbour', 'Harbour Two'])
        ->and($names('/api/v1/organizations/'.$this->globex->uuid.'/spots'))->toBe(['Lighthouse'])
        ->and($names('/api/v1/organizations/'.$this->acme->uuid.'/spots'.'?query=two'))->toBe(['Harbour Two'])
        ->and($names('/api/v1/organizations/'.$this->acme->uuid.'/spots'.'?mine=1'))->toBe(['Harbour']);

    $this->getJson('/api/v1/organizations/unknown/spots')->assertNotFound();
    $this->getJson('/api/v1/organizations/'.$this->acme->uuid.'/spots'.'?query[]=x')->assertUnprocessable()->assertJsonValidationErrors('query');
});

test('the creator of a spot is only visible to the members of its organization and to administrators', function () {
    Sanctum::actingAs($this->member, ['spots:read']);
    $this->getJson('/api/v1/spots/'.$this->harbour->uuid)->assertOk()->assertJsonPath('data.creator.name', 'Mike Member');
    $this->getJson('/api/v1/spots/'.$this->lighthouse->uuid)->assertOk()->assertJsonPath('data.creator', null);

    $this->getJson('/api/v1/spots')->assertOk()->assertJsonPath('data.0.creator.name', 'Mike Member')->assertJsonMissingPath('data.1.creator');

    Sanctum::actingAs($this->outsider, ['spots:read']);
    $this->getJson('/api/v1/spots/'.$this->harbour->uuid)->assertOk()->assertJsonMissingPath('data.creator');
    $this->getJson('/api/v1/spots')->assertOk()->assertJsonMissingPath('data.0.creator');

    Sanctum::actingAs($this->admin, ['spots:read']);
    $this->getJson('/api/v1/spots/'.$this->harbour->uuid)->assertOk()->assertJsonPath('data.creator.name', 'Mike Member');
});

test('a spot can be shown by its uuid', function () {
    Sanctum::actingAs($this->outsider, ['spots:read']);

    $this->getJson('/api/v1/spots/'.$this->harbour->uuid)
        ->assertOk()
        ->assertJsonPath('data.id', $this->harbour->uuid)
        ->assertJsonPath('data.name', 'Harbour')
        ->assertJsonPath('data.organization.id', $this->acme->uuid)
        ->assertJsonPath('data.organization.name', 'Acme');

    $this->getJson('/api/v1/spots/unknown')->assertNotFound();
});

test('a token needs the matching ability for every action', function (string $method, string $ability) {
    Sanctum::actingAs($this->admin, array_values(array_diff($this->abilities, [$ability])));

    $url = match ($method) {
        'postJson' => '/api/v1/organizations/'.$this->acme->uuid.'/spots',
        'getJson' => '/api/v1/spots',
        default => '/api/v1/spots/'.$this->harbour->uuid,
    };

    $this->$method($url, ['name' => 'Whatever'])->assertForbidden();

    expect(Spot::where('name', 'Whatever')->exists())->toBeFalse()
        ->and(Spot::whereKey($this->harbour->id)->exists())->toBeTrue();
})->with([
    'list' => ['getJson', 'spots:read'],
    'create' => ['postJson', 'spots:create'],
    'update' => ['putJson', 'spots:update'],
    'delete' => ['deleteJson', 'spots:delete'],
]);

test('a member creates a spot and becomes its creator', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $uuid = $this->postJson('/api/v1/organizations/'.$this->acme->uuid.'/spots', ['name' => 'Pier'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Pier')
        ->assertJsonPath('data.organization.id', $this->acme->uuid)
        ->assertJsonPath('data.creator.name', 'Mike Member')
        ->json('data.id');

    $spot = Spot::firstWhere('uuid', $uuid);

    expect($spot->organization->is($this->acme))->toBeTrue()
        ->and($spot->creator->is($this->member))->toBeTrue();
});

test('the abilities of a token never exceed the rights of its owner', function () {
    Sanctum::actingAs($this->outsider, $this->abilities);

    $this->postJson('/api/v1/organizations/'.$this->acme->uuid.'/spots', ['name' => 'Pier'])->assertForbidden();
    $this->putJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Renamed'])->assertForbidden();
    $this->deleteJson('/api/v1/spots/'.$this->harbour->uuid)->assertForbidden();

    expect($this->harbour->fresh()->name)->toBe('Harbour')
        ->and(Spot::count())->toBe(2);
});

test('administrators can manage the spots of any organization', function () {
    Sanctum::actingAs($this->admin, $this->abilities);

    $this->postJson('/api/v1/organizations/'.$this->globex->uuid.'/spots', ['name' => 'Pier'])->assertCreated();
    $this->putJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Marina'])->assertOk()->assertJsonPath('data.name', 'Marina');
    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Dock'])->assertOk();
    $this->deleteJson('/api/v1/spots/'.$this->harbour->uuid)->assertNoContent();

    expect(Spot::firstWhere('uuid', $this->harbour->uuid))->toBeNull();
});

test('members update and delete the spots of their organization', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->putJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Marina'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Marina');

    expect($this->harbour->fresh()->created_by)->toBe($this->member->id);

    $this->deleteJson('/api/v1/spots/'.$this->harbour->uuid)->assertNoContent();
});

test('the spot input is validated', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->postJson('/api/v1/organizations/'.$this->acme->uuid.'/spots', [])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson('/api/v1/organizations/'.$this->acme->uuid.'/spots', ['name' => 'A'])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson('/api/v1/organizations/'.$this->acme->uuid.'/spots', ['name' => 'Harbour'])->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson('/api/v1/organizations/unknown/spots', ['name' => 'Pier'])->assertNotFound();

    $this->putJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Harbour'])->assertOk();

    // The same name is allowed in another organization.
    $this->member->organizations()->attach($this->globex);
    $this->postJson('/api/v1/organizations/'.$this->globex->uuid.'/spots', ['name' => 'Harbour'])->assertCreated();
});

test('tokens are required', function () {
    $this->getJson('/api/v1/spots')->assertUnauthorized();
    $this->getJson('/api/v1/organizations/'.$this->acme->uuid.'/spots')->assertUnauthorized();
    $this->postJson('/api/v1/organizations/'.$this->acme->uuid.'/spots', ['name' => 'Pier'])->assertUnauthorized();
});
