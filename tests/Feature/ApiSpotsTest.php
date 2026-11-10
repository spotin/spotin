<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

test('the spots are listed, sorted by name and paginated', function () {
    Spot::factory()->for($this->globex)->count(30)->create();

    // Administrators list the spots of all the organizations
    Sanctum::actingAs($this->admin, ['spots:read']);

    $this->getJson('/api/v1/spots')
        ->assertOk()
        ->assertJsonCount(25, 'data')
        ->assertJsonPath('meta.total', 32)
        ->assertJsonStructure(['data' => [['id', 'type', 'attributes' => ['name', 'created_at', 'updated_at']]], 'links', 'meta'])
        ->assertJsonMissingPath('data.0.relationships')
        ->assertJsonMissingPath('included');
});

test('the spots listed are the ones of the organizations of the user, all of them for administrators', function (string $user, array $names) {
    Spot::factory()->for($this->acme)->hidden()->create(['name' => 'Secret cove']);

    Sanctum::actingAs($this->$user, ['spots:read']);

    expect(collect($this->getJson('/api/v1/spots')->assertOk()->json('data'))->pluck('attributes.name')->all())->toBe($names);
})->with([
    'member' => ['member', ['Harbour', 'Secret cove']],
    'outsider' => ['outsider', []],
    'admin' => ['admin', ['Harbour', 'Lighthouse', 'Secret cove']],
]);

test('the spots can be searched and filtered to the ones the user created', function () {
    Spot::factory()->for($this->acme)->create(['name' => 'Harbour Two']);

    Sanctum::actingAs($this->member, ['spots:read']);

    $names = fn (string $url) => collect($this->getJson($url)->assertOk()->json('data'))->pluck('attributes.name')->all();

    expect($names('/api/v1/spots?filter[query]=harbour'))->toBe(['Harbour', 'Harbour Two'])
        ->and($names('/api/v1/spots?filter[mine]=1'))->toBe(['Harbour'])
        ->and($names('/api/v1/spots?filter[query]=harbour&filter[mine]=1'))->toBe(['Harbour'])
        ->and($names('/api/v1/spots?filter[query]=%20two%20'))->toBe(['Harbour Two']);

    $this->getJson('/api/v1/spots?filter[query][]=x')->assertUnprocessable()->assertJsonValidationErrors('filter.query');
});

test('the spots can be filtered to the ones of an organization, with the other filters', function () {
    Spot::factory()->for($this->acme)->create(['name' => 'Harbour Two']);

    Sanctum::actingAs($this->member, ['spots:read']);

    $names = fn (string $url) => collect($this->getJson($url)->assertOk()->json('data'))->pluck('attributes.name')->all();

    expect($names('/api/v1/spots?filter[organization]='.$this->acme->uuid))->toBe(['Harbour', 'Harbour Two'])
        ->and($names('/api/v1/spots?filter[organization]='.$this->globex->uuid))->toBe([])
        ->and($names('/api/v1/spots?filter[organization]='.$this->acme->uuid.'&filter[query]=two'))->toBe(['Harbour Two'])
        ->and($names('/api/v1/spots?filter[organization]='.$this->acme->uuid.'&filter[mine]=1'))->toBe(['Harbour']);

    $this->getJson('/api/v1/spots?filter[organization]=unknown')->assertUnprocessable()->assertJsonValidationErrors('filter.organization');
    $this->getJson('/api/v1/spots?filter[organization]='.fake()->uuid())->assertUnprocessable()->assertJsonValidationErrors('filter.organization');
    $this->getJson('/api/v1/spots?filter[other]=x')->assertUnprocessable()->assertJsonValidationErrors('filter');
});

test('the organization and the creator of a spot can be included', function () {
    Sanctum::actingAs($this->member, ['spots:read']);

    $this->getJson('/api/v1/spots/'.$this->harbour->uuid.'?include=organization,creator')
        ->assertOk()
        ->assertJsonPath('data.relationships.organization.data', ['id' => $this->acme->uuid, 'type' => 'organizations'])
        ->assertJsonPath('data.relationships.creator.data', ['id' => $this->member->uuid, 'type' => 'users'])
        ->assertJsonPath('included.0.attributes.name', 'Acme')
        ->assertJsonPath('included.1.attributes.name', 'Mike Member');

    Sanctum::actingAs($this->admin, ['spots:read']);

    $this->getJson('/api/v1/spots?include=organization')
        ->assertOk()
        ->assertJsonPath('data.0.relationships.organization.data.id', $this->acme->uuid)
        ->assertJsonPath('data.1.relationships.organization.data.id', $this->globex->uuid)
        ->assertJsonCount(2, 'included');
});

test('the creator of a spot is only visible to the members of its organization and to administrators', function () {
    Sanctum::actingAs($this->member, ['spots:read']);
    $this->getJson('/api/v1/spots/'.$this->harbour->uuid.'?include=creator')->assertOk()->assertJsonPath('data.relationships.creator.data.id', $this->member->uuid);
    $this->getJson('/api/v1/spots/'.$this->lighthouse->uuid.'?include=creator')->assertOk()->assertJsonPath('data.relationships.creator.data', null);

    $this->getJson('/api/v1/spots?include=creator')->assertOk()
        ->assertJsonPath('data.0.relationships.creator.data.id', $this->member->uuid);

    Sanctum::actingAs($this->outsider, ['spots:read']);
    $this->getJson('/api/v1/spots/'.$this->harbour->uuid.'?include=creator')->assertOk()->assertJsonPath('data.relationships.creator.data', null)->assertJsonMissingPath('included');

    Sanctum::actingAs($this->admin, ['spots:read']);
    $this->getJson('/api/v1/spots/'.$this->harbour->uuid.'?include=creator')->assertOk()->assertJsonPath('data.relationships.creator.data.id', $this->member->uuid);
});

test('checking who can see the creators does not query once per spot', function () {
    $queries = function () {
        // A fresh user each time, as in a real request, without the organizations loaded by the previous one.
        Sanctum::actingAs($this->member->fresh(), ['spots:read']);
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->getJson('/api/v1/spots?include=creator')->assertOk();

        return count(DB::getQueryLog());
    };

    $few = $queries();
    Spot::factory()->for($this->acme)->count(10)->create(['created_by' => $this->member->id]);

    expect($queries())->toBe($few);
});

test('an included creator only shows their name to the other users', function () {
    Sanctum::actingAs($this->admin, ['spots:read']);

    $this->getJson('/api/v1/spots/'.$this->harbour->uuid.'?include=creator')
        ->assertOk()
        ->assertJsonPath('included.0.attributes', ['name' => 'Mike Member'])
        ->assertJsonMissingPath('included.0.meta');
});

test('a spot can be shown by its uuid', function () {
    Sanctum::actingAs($this->outsider, ['spots:read']);

    $this->getJson('/api/v1/spots/'.$this->harbour->uuid)
        ->assertOk()
        ->assertJsonPath('data.id', $this->harbour->uuid)
        ->assertJsonPath('data.type', 'spots')
        ->assertJsonPath('data.attributes.name', 'Harbour');

    $this->getJson('/api/v1/spots/unknown')->assertNotFound();
});

test('a token needs the matching ability for every action', function (string $method, string $ability) {
    Sanctum::actingAs($this->admin, array_values(array_diff($this->abilities, [$ability])));

    $url = match ($method) {
        'postJson', 'getJson' => '/api/v1/spots',
        default => '/api/v1/spots/'.$this->harbour->uuid,
    };

    $this->$method($url, ['organization' => $this->acme->uuid, 'name' => 'Whatever'])->assertForbidden();

    expect(Spot::where('name', 'Whatever')->exists())->toBeFalse()
        ->and(Spot::whereKey($this->harbour->id)->exists())->toBeTrue();
})->with([
    'list' => ['getJson', 'spots:read'],
    'create' => ['postJson', 'spots:create'],
    'update' => ['patchJson', 'spots:update'],
    'delete' => ['deleteJson', 'spots:delete'],
]);

test('a member creates a spot and becomes its creator', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $uuid = $this->postJson('/api/v1/spots?include=organization,creator', spotData(['organization' => $this->acme->uuid, 'name' => 'Pier']))
        ->assertCreated()
        ->assertJsonPath('data.attributes.name', 'Pier')
        ->assertJsonPath('data.relationships.organization.data.id', $this->acme->uuid)
        ->assertJsonPath('data.relationships.creator.data.id', $this->member->uuid)
        ->json('data.id');

    $spot = Spot::firstWhere('uuid', $uuid);

    expect($spot->organization->is($this->acme))->toBeTrue()
        ->and($spot->creator->is($this->member))->toBeTrue();
});

test('the abilities of a token never exceed the rights of its owner', function () {
    Sanctum::actingAs($this->outsider, $this->abilities);

    $this->postJson('/api/v1/spots', spotData(['organization' => $this->acme->uuid, 'name' => 'Pier']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['organization' => 'You cannot create spots in this organization.']);
    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Renamed'])->assertForbidden();
    $this->deleteJson('/api/v1/spots/'.$this->harbour->uuid)->assertForbidden();

    expect($this->harbour->fresh()->name)->toBe('Harbour')
        ->and(Spot::count())->toBe(2);
});

test('administrators can manage the spots of any organization', function () {
    Sanctum::actingAs($this->admin, $this->abilities);

    $this->postJson('/api/v1/spots', spotData(['organization' => $this->globex->uuid, 'name' => 'Pier']))->assertCreated();
    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Marina'])->assertOk()->assertJsonPath('data.attributes.name', 'Marina');
    $this->putJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Dock'])->assertMethodNotAllowed();
    $this->deleteJson('/api/v1/spots/'.$this->harbour->uuid)->assertNoContent();

    expect(Spot::firstWhere('uuid', $this->harbour->uuid))->toBeNull();
});

test('members update and delete the spots of their organization', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Marina'])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Marina');

    expect($this->harbour->fresh()->created_by)->toBe($this->member->id);

    $this->deleteJson('/api/v1/spots/'.$this->harbour->uuid)->assertNoContent();
});

test('the spot input is validated', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->postJson('/api/v1/spots', [])->assertUnprocessable()->assertJsonValidationErrors(['organization', 'name']);
    $this->postJson('/api/v1/spots', spotData(['organization' => $this->acme->uuid, 'name' => 'A']))->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson('/api/v1/spots', spotData(['organization' => $this->acme->uuid, 'name' => 'Harbour']))->assertUnprocessable()->assertJsonValidationErrors('name');
    $this->postJson('/api/v1/spots', spotData(['organization' => 'unknown', 'name' => 'Pier']))->assertUnprocessable()->assertJsonValidationErrors('organization');
    $this->postJson('/api/v1/spots', spotData(['organization' => fake()->uuid(), 'name' => 'Pier']))->assertUnprocessable()->assertJsonValidationErrors('organization');

    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['name' => 'Harbour'])->assertOk();

    // The same name is allowed in another organization.
    $this->member->organizations()->attach($this->globex);
    // As a new request would: the responses load the organizations of the user once, before this one
    Sanctum::actingAs($this->member->fresh(), $this->abilities);
    $this->postJson('/api/v1/spots', spotData(['organization' => $this->globex->uuid, 'name' => 'Harbour']))->assertCreated();
});

test('tokens are required', function () {
    $this->getJson('/api/v1/spots')->assertUnauthorized();
    $this->postJson('/api/v1/spots', spotData(['organization' => $this->acme->uuid, 'name' => 'Pier']))->assertUnauthorized();
});

test('a spot is created and returned with all its details', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->postJson('/api/v1/spots', spotData([
        'organization' => $this->acme->uuid,
        'name' => 'Pier',
        'description' => 'A **long** pier.',
        'website' => 'https://pier.example.com',
        'redirect_to_website' => true,
        'is_hidden' => true,
    ]))
        ->assertCreated()
        ->assertJsonPath('data.attributes.description', 'A **long** pier.')
        ->assertJsonPath('data.attributes.latitude', 46.5197)
        ->assertJsonPath('data.attributes.longitude', 6.6323)
        ->assertJsonPath('data.attributes.website', 'https://pier.example.com')
        ->assertJsonPath('data.attributes.redirect_to_website', true)
        ->assertJsonPath('data.attributes.is_hidden', true);
});

test('a PATCH only changes the fields it sends, and validates them', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $latitude = $this->harbour->latitude;

    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['website' => 'https://harbour.example.com'])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Harbour')
        ->assertJsonPath('data.attributes.latitude', $latitude)
        ->assertJsonPath('data.attributes.website', 'https://harbour.example.com');

    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['latitude' => 100, 'website' => 'ftp://example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['latitude', 'website']);

    // The kept longitude needs a latitude: the location is removed with both
    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['latitude' => null])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['latitude']);

    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['latitude' => null, 'longitude' => null])
        ->assertOk()
        ->assertJsonPath('data.attributes.latitude', null)
        ->assertJsonPath('data.attributes.longitude', null);
});

test('hidden spots are only listed for the members of their organization and administrators, and stay reachable', function () {
    $secret = Spot::factory()->for($this->acme)->hidden()->create(['name' => 'Secret Cove']);

    Sanctum::actingAs($this->outsider, ['spots:read']);

    $this->getJson('/api/v1/spots')->assertOk()->assertJsonMissing(['name' => 'Secret Cove']);
    $this->getJson('/api/v1/spots?filter[organization]='.$this->acme->uuid)->assertOk()->assertJsonMissing(['name' => 'Secret Cove']);
    $this->getJson('/api/v1/spots/'.$secret->uuid)->assertOk()->assertJsonPath('data.attributes.is_hidden', true);

    foreach ([$this->member, $this->admin] as $user) {
        Sanctum::actingAs($user, ['spots:read']);

        $this->getJson('/api/v1/spots')->assertOk()->assertJsonFragment(['name' => 'Secret Cove']);
    }
});

test('a PATCH cannot leave a spot redirecting its visitors without a website', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->harbour->update(['website' => null, 'redirect_to_website' => false]);

    // The redirect alone, on a spot without a website
    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['redirect_to_website' => true])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('redirect_to_website');

    // The website removed from a spot that redirects
    $this->harbour->update(['website' => 'https://harbour.example.com', 'redirect_to_website' => true]);

    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['website' => null])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('redirect_to_website');

    // Both together are fine
    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['website' => null, 'redirect_to_website' => false])->assertOk();
    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['website' => 'https://pier.example.com', 'redirect_to_website' => true])->assertOk();
});

test('a spot keeps its organization when updated', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->patchJson('/api/v1/spots/'.$this->harbour->uuid, ['organization' => $this->globex->uuid, 'name' => 'Marina'])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Marina');

    expect($this->harbour->fresh()->organization->is($this->acme))->toBeTrue();
});

test('a spot keeps the custom data it is given as a JSON object, nested ones included, and loses them with null or {}', function () {
    Sanctum::actingAs($this->member, $this->abilities);

    $customData = ['floor' => 2, 'tags' => ['entrance'], 'opening' => ['from' => '08:00'], 'empty' => (object) []];

    $uuid = $this->postJson('/api/v1/spots', ['organization' => $this->acme->uuid, 'name' => 'Pier', 'custom_data' => $customData])
        ->assertCreated()
        ->json('data.id');

    // Objects stay objects, the empty ones inside included, never lists
    expect($this->getJson("/api/v1/spots/{$uuid}")->assertOk()->getContent())
        ->toContain('"custom_data":{"floor":2,"tags":["entrance"],"opening":{"from":"08:00"},"empty":{}}');

    // A PATCH without them keeps them, a PATCH with them replaces them
    $this->patchJson("/api/v1/spots/{$uuid}", ['name' => 'Long pier'])->assertOk()->assertJsonPath('data.attributes.custom_data.floor', 2);
    $this->patchJson("/api/v1/spots/{$uuid}", ['custom_data' => ['floor' => 3]])->assertOk()->assertJsonPath('data.attributes.custom_data', ['floor' => 3]);

    foreach ([null, (object) []] as $nothing) {
        $this->patchJson("/api/v1/spots/{$uuid}", ['custom_data' => ['floor' => 3]])->assertOk();
        $this->patchJson("/api/v1/spots/{$uuid}", ['custom_data' => $nothing])->assertOk()->assertJsonPath('data.attributes.custom_data', null);

        expect(DB::table('spots')->where('uuid', $uuid)->value('custom_data'))->toBeNull();
    }

    // Stored as sent: an empty object inside stays one
    $this->patchJson("/api/v1/spots/{$uuid}", ['custom_data' => ['nested' => (object) []]])->assertOk();
    expect(DB::table('spots')->where('uuid', $uuid)->value('custom_data'))->toBe('{"nested":{}}');
});

test('the custom data are a JSON object of at most 10,000 characters', function (mixed $customData) {
    Sanctum::actingAs($this->member, $this->abilities);

    $this->patchJson("/api/v1/spots/{$this->harbour->uuid}", ['custom_data' => $customData])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('custom_data');
})->with([
    'a list' => [[1, 2]],
    'a string' => ['{"floor": 2}'],
    'a number' => [2],
    'too long' => [['text' => str_repeat('a', 10000)]],
]);

test('the custom data are only returned to who manages the spot', function (string $user, bool $returned) {
    $this->harbour->update(['custom_data' => ['floor' => 2]]);

    Sanctum::actingAs($this->$user, ['spots:read']);

    $show = $this->getJson("/api/v1/spots/{$this->harbour->uuid}")->assertOk();
    $list = $this->getJson('/api/v1/spots')->assertOk();

    if ($returned) {
        $show->assertJsonPath('data.attributes.custom_data', ['floor' => 2]);
        $list->assertJsonPath('data.0.attributes.custom_data', ['floor' => 2]);
    } else {
        $show->assertJsonMissingPath('data.attributes.custom_data');
    }
})->with([
    'member' => ['member', true],
    'admin' => ['admin', true],
    'outsider' => ['outsider', false],
]);

test('saving the web form of a spot keeps its custom data', function () {
    $this->harbour->update(['custom_data' => ['floor' => 2]]);

    $this->actingAs($this->member)
        ->patch(route('spots.update', $this->harbour), ['name' => 'Harbour', 'description' => 'By the sea.', 'is_hidden' => '0'])
        ->assertSessionHasNoErrors();

    expect($this->harbour->fresh()->custom_data)->toEqual((object) ['floor' => 2]);
});
