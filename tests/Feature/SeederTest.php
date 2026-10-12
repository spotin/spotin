<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use Database\Seeders\DevelopmentSeeder;
use Database\Seeders\ProductionSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    config()->set('admin', ['name' => 'Jane Admin', 'email' => 'Jane@Example.com', 'password' => 'a-strong-password']);
});

test('the production seeder creates the administrator from the environment', function () {
    $this->seed(ProductionSeeder::class);

    $admin = User::sole();

    expect($admin->name)->toBe('Jane Admin')
        ->and($admin->email)->toBe('jane@example.com')
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->hasVerifiedEmail())->toBeTrue()
        ->and(Hash::check('a-strong-password', $admin->password))->toBeTrue();
});

test('the production seeder can run again without changing the administrator', function () {
    $this->seed(ProductionSeeder::class);

    config()->set('admin', ['name' => 'Someone Else', 'email' => 'jane@example.com', 'password' => 'another-password']);
    $this->seed(ProductionSeeder::class);

    $admin = User::sole();

    expect($admin->name)->toBe('Jane Admin')
        ->and(Hash::check('a-strong-password', $admin->password))->toBeTrue();
});

test('the production seeder promotes an existing account', function () {
    User::factory()->unverified()->create(['email' => 'jane@example.com', 'name' => 'Existing']);

    $this->seed(ProductionSeeder::class);

    $user = User::sole();

    expect($user->name)->toBe('Existing')
        ->and($user->isAdmin())->toBeTrue()
        ->and($user->hasVerifiedEmail())->toBeTrue();
});

test('the development seeder seeds the environment administrator and the named accounts', function () {
    $this->seed(DevelopmentSeeder::class);

    expect(User::where('is_admin', true)->sole()->email)->toBe('jane@example.com')
        ->and(User::whereIn('email', ['responsible@example.com', 'member@example.com'])->count())->toBe(2)
        ->and(Organization::where('name', 'Organization')->count())->toBe(1);
});

test('the development seeder creates enough data for every list to have several pages', function () {
    $this->seed(DevelopmentSeeder::class);

    $perPage = 25;
    $largest = fn (string $table, string $column) => DB::table($table)->select($column, DB::raw('count(*) as total'))->groupBy($column)->orderByDesc('total')->first()->total;

    // The administration list of organizations.
    expect(Organization::count())->toBeGreaterThan($perPage)
        // The list of organizations of the named users.
        ->and(User::where('email', 'responsible@example.com')->sole()->organizations()->count())->toBeGreaterThan($perPage)
        ->and(User::where('email', 'member@example.com')->sole()->organizations()->count())->toBeGreaterThan($perPage)
        // The members and the invitations of an organization.
        ->and($largest('memberships', 'organization_id'))->toBeGreaterThan($perPage)
        ->and($largest('organization_invitations', 'organization_id'))->toBeGreaterThan($perPage);
});

test('every organization of the development seeder with members has a responsible user', function () {
    $this->seed(DevelopmentSeeder::class);

    expect(Organization::has('users')->doesntHave('responsibles')->count())->toBe(0);
});

test('the development seeder creates a mix of roles, expired invitations and unverified users', function () {
    $this->seed(DevelopmentSeeder::class);

    expect(OrganizationInvitation::where('expires_at', '<', now())->exists())->toBeTrue()
        ->and(OrganizationInvitation::where('role', OrganizationRole::Responsible)->exists())->toBeTrue()
        ->and(OrganizationInvitation::where('role', OrganizationRole::Member)->exists())->toBeTrue()
        ->and(User::whereNull('email_verified_at')->exists())->toBeTrue();
});
