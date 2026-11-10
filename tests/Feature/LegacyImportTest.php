<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\PendingCommand;

// A hash of the old application: argon2id, with its 4 threads, more than the ones of this application. Of `secret-password`.
const LEGACY_HASH = '$argon2id$v=19$m=65536,t=3,p=4$SUZQYjJHaUJuRm5mRWFVYQ$c9AYDssYIFmuy+iAgBGvY5NWg8mvn5eYGIx8vZgsPdY';

/**
 * A user of the old application, as exported by PostgreSQL.
 *
 * @param  array<string, mixed>  $attributes
 * @return array<string, mixed>
 */
function legacyUser(array $attributes = []): array
{
    return [
        'id' => fake()->uuid(),
        'username' => fake()->unique()->userName(),
        'email' => fake()->unique()->safeEmail(),
        'password' => LEGACY_HASH,
        'bio' => null,
        'role' => 'STANDARD_USER',
        'enabled' => true,
        'created_at' => '2024-06-11T10:57:51.123+02:00',
        'updated_at' => '2024-06-12T10:00:00+02:00',
        'deleted_at' => null,
        ...$attributes,
    ];
}

/**
 * A spot of the old application, as exported by PostgreSQL.
 *
 * @param  array<string, mixed>  $attributes
 * @return array<string, mixed>
 */
function legacySpot(array $user, array $attributes = []): array
{
    return [
        'id' => fake()->uuid(),
        'user_id' => $user['id'],
        'name' => fake()->unique()->words(2, true),
        'description' => 'A **spot**.',
        'latitude' => 46.5228601234,
        'longitude' => 6.635480,
        'website_target' => null,
        'direct_access_to_website_target' => false,
        'configured' => true,
        'public' => true,
        'payload' => null,
        'created_at' => '2024-06-11T10:57:51+02:00',
        'updated_at' => '2024-06-12T10:00:00+02:00',
        'deleted_at' => null,
        ...$attributes,
    ];
}

/**
 * Write the exported tables and the mapping in a new directory, and run the import on it.
 *
 * @param  list<array<string, mixed>>  $users
 * @param  list<array<string, mixed>>  $spots
 * @param  list<string>  $mapping  The rows of the mapping, after its header
 */
function importLegacy(array $users, array $spots, array $mapping, array $options = []): PendingCommand
{
    $directory = sys_get_temp_dir().'/legacy-'.Str::random();
    mkdir($directory);

    file_put_contents("{$directory}/users.json", json_encode($users));
    file_put_contents("{$directory}/spots.json", json_encode($spots));
    file_put_contents("{$directory}/organizations.csv", implode("\n", ['email,organization,role', ...$mapping])."\n");

    return test()->artisan('spotin:import-legacy', ['directory' => $directory, ...$options]);
}

beforeEach(function () {
    $this->alice = legacyUser(['username' => 'alice', 'email' => 'Alice@Example.com']);
    $this->bob = legacyUser(['username' => 'bob', 'email' => 'bob@example.com']);
    $this->root = legacyUser(['username' => 'root', 'email' => 'root@example.com', 'role' => 'ADMIN']);

    $this->mapping = [
        'alice@example.com,Museum,responsible',
        'bob@example.com,Museum,member',
        'bob@example.com,Library,responsible',
        'root@example.com,,',
    ];
});

test('the users come with their passwords, verified, administrators as such', function () {
    importLegacy([$this->alice, $this->bob, $this->root], [], $this->mapping)->assertSuccessful();

    $alice = User::firstWhere('email', 'alice@example.com');

    expect($alice)
        ->uuid->toBe($this->alice['id'])
        ->name->toBe('alice')
        ->is_admin->toBeFalse()
        // To the second, as stored
        ->email_verified_at->toEqual(Carbon::parse('2024-06-11T08:57:51Z'))
        ->and(User::firstWhere('email', 'root@example.com')->is_admin)->toBeTrue();

    // Logged in with the old password, which is hashed again with the options of this application
    $this->post(route('login.store'), ['email' => 'alice@example.com', 'password' => 'secret-password'])->assertRedirect();
    $this->assertAuthenticatedAs($alice);

    expect($alice->fresh()->password)->not->toBe(LEGACY_HASH)
        ->and(Hash::check('secret-password', $alice->fresh()->password))->toBeTrue();
});

test('the organizations of the mapping are created with their members and roles', function () {
    importLegacy([$this->alice, $this->bob, $this->root], [], $this->mapping)->assertSuccessful();

    $museum = Organization::firstWhere('name', 'Museum');
    $bob = User::firstWhere('email', 'bob@example.com');

    expect(Organization::pluck('name')->sort()->values()->all())->toBe(['Library', 'Museum'])
        ->and($museum->responsibles()->pluck('email')->all())->toBe(['alice@example.com'])
        ->and($bob->hasRoleIn($museum, OrganizationRole::Member))->toBeTrue()
        ->and($bob->isResponsibleOf(Organization::firstWhere('name', 'Library')))->toBeTrue()
        ->and(User::firstWhere('email', 'root@example.com')->organizations)->toBeEmpty();
});

test('the spots keep their UUID, in the first organization of their user, with their details converted', function () {
    $spot = legacySpot($this->bob, [
        'name' => '  Kultursäule Neumarktplatz ',
        'website_target' => 'https://example.com',
        'direct_access_to_website_target' => true,
        'public' => false,
        'payload' => ['floor' => 2, 'nested' => (object) []],
    ]);
    $unconfigured = legacySpot($this->bob, ['name' => '', 'configured' => false]);

    importLegacy([$this->alice, $this->bob, $this->root], [$spot, $unconfigured], $this->mapping)->assertSuccessful();

    $imported = Spot::firstWhere('uuid', $spot['id']);

    expect($imported)
        ->name->toBe('Kultursäule Neumarktplatz')
        ->organization->name->toBe('Museum')
        ->creator->email->toBe('bob@example.com')
        ->description->toBe('A **spot**.')
        ->latitude->toBe(46.522860)
        ->website->toBe('https://example.com')
        ->redirect_to_website->toBeTrue()
        ->is_hidden->toBeTrue()
        ->needs_setup->toBeFalse()
        ->qr_code_downloaded_at->toEqual(Carbon::parse('2024-06-11T08:57:51Z'))
        ->created_at->toEqual(Carbon::parse('2024-06-11T08:57:51Z'))
        ->updated_at->toEqual(Carbon::parse('2024-06-12T08:00:00Z'))
        ->and(DB::table('spots')->where('uuid', $spot['id'])->value('custom_data'))->toBe('{"floor":2,"nested":{}}')
        ->and(Spot::firstWhere('uuid', $unconfigured['id']))->needs_setup->toBeTrue()->name->toBeNull();

    // Their printed QR codes lead where they did
    $this->get("/spots/{$spot['id']}/redirect")->assertRedirect('https://example.com');
    $this->get("/spots/{$unconfigured['id']}/redirect")->assertRedirect(route('spots.edit', ['spot' => $unconfigured['id']]));
});

test('the descriptions written in HTML are converted to Markdown, as their HTML would be stripped', function () {
    $spot = legacySpot($this->alice, ['description' => '<p>Immer nutzbar. <br />Feuerwerkerstrasse 39</p><p><b>Einheit:</b> armasuisse</p>']);

    importLegacy([$this->alice, $this->bob, $this->root], [$spot, legacySpot($this->alice)], $this->mapping)
        ->expectsOutputToContain('Descriptions converted from HTML')
        ->assertSuccessful();

    $imported = Spot::firstWhere('uuid', $spot['id']);

    expect($imported->description)->not->toContain('<')->toContain("\n\n**Einheit:** armasuisse")
        ->and($imported->descriptionHtml())->toContain('Immer nutzbar.<br />')->toContain('<strong>Einheit:</strong> armasuisse');
});

test('the spots with a name in common in an organization are numbered, in the order they were created', function () {
    $spots = [
        legacySpot($this->alice, ['name' => 'Fountain', 'created_at' => '2024-01-03T10:00:00+01:00']),
        legacySpot($this->alice, ['name' => ' fountain', 'created_at' => '2024-01-01T10:00:00+01:00']),
        // Deleted, numbered too: restored as it is
        legacySpot($this->alice, ['name' => 'Fountain', 'created_at' => '2024-01-02T10:00:00+01:00', 'deleted_at' => '2025-01-01T10:00:00+01:00']),
        // Its number is taken: passed over
        legacySpot($this->alice, ['name' => 'Fountain (2)']),
        // In another organization: kept as it is
        legacySpot($this->bob, ['name' => 'Fountain']),
        // The name of a spot already in the organization
        legacySpot($this->alice, ['name' => 'Harbour']),
        legacySpot($this->alice, ['name' => null]),
        legacySpot($this->alice, ['name' => null]),
    ];
    $this->mapping[1] = 'bob@example.com,Library,responsible';
    $this->mapping[2] = 'bob@example.com,Museum,member';
    Spot::factory()->for(Organization::factory()->create(['name' => 'Museum']))->create(['name' => 'Harbour']);

    importLegacy([$this->alice, $this->bob, $this->root], $spots, $this->mapping)
        ->expectsOutputToContain('Spots renamed')
        ->expectsOutputToContain('3 spots of Museum were named fountain: they were numbered, up to Fountain (4).')
        ->assertSuccessful();

    $name = fn (array $spot) => Spot::withTrashed()->firstWhere('uuid', $spot['id'])->name;

    expect(array_map($name, $spots))->toBe(['Fountain (4)', 'fountain (1)', 'Fountain (3)', 'Fountain (2)', 'Fountain', 'Harbour (1)', null, null]);
});

test('a website without a scheme gets https://, so that its QR code does not lead to a page of the application', function () {
    $spot = legacySpot($this->alice, ['website_target' => 'www.example.com', 'direct_access_to_website_target' => true]);
    $unnamed = legacySpot($this->alice, ['name' => null]);

    importLegacy([$this->alice, $this->bob, $this->root], [$spot, $unnamed, legacySpot($this->alice, ['website_target' => 'http://example.org'])], $this->mapping)
        ->expectsOutputToContain('www.example.com, had no scheme: https:// was added')
        ->expectsOutputToContain('is set up, but has no name')
        ->assertSuccessful();

    expect(Spot::where('website', 'http://example.org')->exists())->toBeTrue();

    $this->get("/spots/{$spot['id']}/redirect")->assertRedirect('https://www.example.com');
});

test('the deleted spots are kept deleted: their QR codes tell they are no longer in use', function () {
    $spot = legacySpot($this->alice, ['deleted_at' => '2025-01-01T12:00:00+01:00']);

    importLegacy([$this->alice, $this->bob, $this->root], [$spot], $this->mapping)->assertSuccessful();

    expect(Spot::withTrashed()->firstWhere('uuid', $spot['id'])->deleted_at)->toEqual(Carbon::parse('2025-01-01T11:00:00Z'));

    $this->get("/spots/{$spot['id']}/redirect")->assertGone()->assertSee(__('ui.qr_codes.inactive.title'));
});

test('the disabled and deleted users are imported as they were, and reported', function () {
    $this->alice['enabled'] = false;
    $this->bob['deleted_at'] = '2025-01-01T12:00:00+01:00';

    importLegacy([$this->alice, $this->bob, $this->root], [legacySpot($this->alice)], $this->mapping)
        ->expectsOutputToContain('alice@example.com was disabled in the old application')
        ->expectsOutputToContain('bob@example.com was deleted in the old application')
        ->assertSuccessful();

    expect(User::whereIn('email', ['alice@example.com', 'bob@example.com'])->count())->toBe(2)
        ->and(Spot::count())->toBe(1);
});

test('the import can be run again: nothing is created twice, and an existing account is kept', function () {
    // The administrator of the production seeder, with the address of an old one
    $admin = User::factory()->create(['email' => 'root@example.com', 'name' => 'Admin', 'is_admin' => false]);
    $spots = [legacySpot($this->alice), legacySpot($this->bob)];

    importLegacy([$this->alice, $this->bob, $this->root], $spots, $this->mapping)->assertSuccessful();
    importLegacy([$this->alice, $this->bob, $this->root], $spots, $this->mapping)
        ->expectsOutputToContain('Spots already there')
        ->assertSuccessful();

    expect(User::count())->toBe(3)
        ->and(Organization::count())->toBe(2)
        ->and(DB::table('memberships')->count())->toBe(3)
        ->and(Spot::count())->toBe(2)
        ->and($admin->fresh())->name->toBe('Admin')->is_admin->toBeTrue();
});

test('the users without spots can be skipped, the ones with spots cannot', function () {
    $this->mapping[3] = 'root@example.com,,skip';

    importLegacy([$this->alice, $this->bob, $this->root], [legacySpot($this->alice)], $this->mapping)
        ->expectsOutputToContain('Users skipped')
        ->assertSuccessful();

    expect(User::pluck('email')->sort()->values()->all())->toBe(['alice@example.com', 'bob@example.com']);
});

test('a user skipped with their spots leaves them out: their QR codes lead to a page not found', function () {
    $spot = legacySpot($this->root);
    $this->mapping[3] = 'root@example.com,,skip';

    importLegacy([$this->alice, $this->bob, $this->root], [$spot, legacySpot($this->alice)], $this->mapping)
        ->expectsOutputToContain('Spots skipped')
        ->expectsOutputToContain('root@example.com was skipped with their 1 spot(s)')
        ->assertSuccessful();

    expect(User::where('email', 'root@example.com')->exists())->toBeFalse()
        ->and(Spot::withTrashed()->where('uuid', $spot['id'])->exists())->toBeFalse()
        ->and(Spot::count())->toBe(1);

    $this->get("/spots/{$spot['id']}/redirect")->assertNotFound();
});

test('a user skipped without their spots leaves them in the organization of the line, without a creator', function () {
    $spot = legacySpot($this->root, ['deleted_at' => '2025-01-01T12:00:00+01:00']);
    $this->mapping[3] = 'root@example.com,Library,skip-user';

    importLegacy([$this->alice, $this->bob, $this->root], [$spot], $this->mapping)
        ->doesntExpectOutputToContain('was skipped with their')
        ->assertSuccessful();

    $imported = Spot::withTrashed()->firstWhere('uuid', $spot['id']);

    expect(User::where('email', 'root@example.com')->exists())->toBeFalse()
        ->and($imported->organization()->withTrashed()->first()->name)->toBe('Library')
        ->and($imported->created_by)->toBeNull()
        ->and(Organization::firstWhere('name', 'Library')->users()->pluck('email')->all())->toBe(['bob@example.com']);

    $this->get("/spots/{$spot['id']}/redirect")->assertGone();
});

test('a dry run imports nothing, but tells what the import would do', function () {
    importLegacy([$this->alice, $this->bob, $this->root], [legacySpot($this->alice)], $this->mapping, ['--dry-run' => true])
        ->expectsOutputToContain('Dry run')
        ->expectsOutputToContain('Spots created')
        ->assertSuccessful();

    expect(User::count())->toBe(0)
        ->and(Organization::count())->toBe(0)
        ->and(Spot::withTrashed()->count())->toBe(0);
});

test('nothing is imported while a user is not in the mapping, or the mapping is wrong', function (Closure $break, string $problem) {
    // The users and the mapping, broken by the data set
    [$users, $mapping] = $break([$this->alice, $this->bob, $this->root], $this->mapping);

    importLegacy($users, [legacySpot($this->alice)], $mapping)
        ->expectsOutputToContain($problem)
        ->assertFailed();

    expect(User::count())->toBe(0)->and(Spot::count())->toBe(0);
})->with([
    'an unmapped user' => [
        fn (array $users, array $mapping) => [$users, array_values(array_filter($mapping, fn (string $row) => ! str_starts_with($row, 'root@')))],
        'The old user root (root@example.com) is not in the mapping.',
    ],
    'an unknown user' => [
        fn (array $users, array $mapping) => [$users, [...$mapping, 'nobody@example.com,Museum,member']],
        'no old user has the email address nobody@example.com',
    ],
    'a user with spots and no organization' => [
        fn (array $users, array $mapping) => [$users, array_replace($mapping, [0 => 'alice@example.com,,'])],
        'has spots, but no organization in the mapping',
    ],
    'an organization without a responsible' => [
        fn (array $users, array $mapping) => [$users, array_replace($mapping, [2 => 'bob@example.com,Library,member'])],
        'The organization Library has no responsible user in the mapping.',
    ],
    'an unknown role' => [
        fn (array $users, array $mapping) => [$users, array_replace($mapping, [1 => 'bob@example.com,Museum,owner'])],
        'the role must be `responsible`, `member` or `skip-user`, not `owner`',
    ],
    'a user twice in an organization' => [
        fn (array $users, array $mapping) => [$users, [...$mapping, 'bob@example.com,Museum,responsible']],
        'bob@example.com is already in Museum',
    ],
    'a skipped user in an organization' => [
        fn (array $users, array $mapping) => [$users, [...$mapping, 'bob@example.com,,skip']],
        'is skipped, but also on other lines of the mapping',
    ],
    'a user skipped with their spots, with an organization' => [
        fn (array $users, array $mapping) => [$users, array_replace($mapping, [3 => 'root@example.com,Museum,skip'])],
        'a user skipped with their spots has no organization, leave it empty',
    ],
    'a user skipped without their spots, without an organization' => [
        fn (array $users, array $mapping) => [$users, array_replace($mapping, [3 => 'root@example.com,,skip-user'])],
        'needs the organization to import their spots in',
    ],
    'two addresses the same once lowercased' => [
        fn (array $users, array $mapping) => [[...$users, legacyUser(['email' => 'ALICE@example.com'])], $mapping],
        'Several old users have the email address alice@example.com once lowercased',
    ],
]);

test('the payloads that are not objects and the single coordinates are left out, and reported', function () {
    $spots = [
        legacySpot($this->alice, ['name' => 'Fountain', 'payload' => [1, 2]]),
        legacySpot($this->alice, ['name' => 'Fountain']),
        // As long as the old names could be, as the forms accept
        legacySpot($this->alice, ['name' => str_repeat('a', 255), 'latitude' => null]),
    ];

    importLegacy([$this->alice, $this->bob, $this->root], $spots, $this->mapping)
        ->expectsOutputToContain('is not a JSON object, and was left out: [1,2]')
        ->doesntExpectOutputToContain('has the name of another spot')
        ->doesntExpectOutputToContain('has a name longer')
        ->expectsOutputToContain('had a longitude without a latitude: it was imported without a location')
        ->assertSuccessful();

    expect(Spot::count())->toBe(3)
        ->and(Spot::firstWhere('uuid', $spots[0]['id'])->custom_data)->toBeNull()
        // Its single coordinate left out
        ->and(Spot::firstWhere('uuid', $spots[2]['id']))->latitude->toBeNull()->longitude->toBeNull();
});

test('the mapping can be split into parts by blank lines and comments, its errors telling their line in the file', function () {
    importLegacy([$this->alice, $this->bob, $this->root], [legacySpot($this->alice)], [
        '',
        '# Museum',
        'alice@example.com,Museum,responsible',
        '   ',
        'bob@example.com,Museum,owner',
        '# Administrators, # inside too',
        'root@example.com,,',
    ])
        // The header on line 1, then line 6
        ->expectsOutputToContain('Line 6 of the mapping: the role must be')
        ->doesntExpectOutputToContain('no old user has the email address .')
        ->assertFailed();
});

test('missing files are told, without importing anything', function () {
    $this->artisan('spotin:import-legacy', ['directory' => sys_get_temp_dir().'/missing-'.Str::random()])
        ->expectsOutputToContain('users.json is missing')
        ->assertFailed();
});
