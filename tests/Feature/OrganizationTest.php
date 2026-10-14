<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Membership;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('organizations get a public uuid and are routed by it', function () {
    $organization = Organization::factory()->create();

    expect(Str::isUuid($organization->uuid))->toBeTrue()
        ->and($organization->getRouteKeyName())->toBe('uuid');
});

test('users are not admins and belong to no organization by default', function () {
    $user = User::factory()->create();

    expect($user->isAdmin())->toBeFalse()
        ->and($user->organizations)->toBeEmpty()
        ->and(User::factory()->admin()->create()->isAdmin())->toBeTrue();
});

test('users can belong to several organizations with a different role in each', function () {
    $user = User::factory()->create();
    $first = Organization::factory()->create();
    $second = Organization::factory()->create();

    $first->users()->attach($user, ['role' => OrganizationRole::Responsible]);
    $second->users()->attach($user);

    expect($user->isResponsibleOf($first))->toBeTrue()
        ->and($user->isResponsibleOf($second))->toBeFalse()
        ->and($user->belongsToOrganization($second))->toBeTrue()
        ->and($user->organizations)->toHaveCount(2)
        ->and($user->organizations->find($second)->pivot)->toBeInstanceOf(Membership::class)
        ->role->toBe(OrganizationRole::Member);
});

test('an organization lists its responsible users', function () {
    $organization = Organization::factory()->create();
    $responsible = User::factory()->create();
    $member = User::factory()->create();

    $organization->users()->attach($responsible, ['role' => OrganizationRole::Responsible]);
    $organization->users()->attach($member);

    expect($organization->responsibles->modelKeys())->toBe([$responsible->id])
        ->and($organization->users)->toHaveCount(2);
});

test('a user cannot be added twice to the same organization', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $organization->users()->attach($user);

    expect(fn () => $organization->users()->attach($user))->toThrow(QueryException::class);
});

test('the database rejects unknown roles', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    expect(fn () => DB::table('memberships')->insert([
        'organization_id' => $organization->id,
        'user_id' => $user->id,
        'role' => 'owner',
    ]))->toThrow(QueryException::class);
});

test('deleting an organization or a user removes the memberships and invitations', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    $organization->users()->attach($user);
    Invitation::factory()->for($organization)->create();

    $organization->delete();

    expect(DB::table('memberships')->count())->toBe(0)
        ->and(Invitation::count())->toBe(0);

    $other = Organization::factory()->create();
    $other->users()->attach($user);

    $user->delete();

    expect(DB::table('memberships')->count())->toBe(0);
});

test('invitations store a hashed token, hide it, and expire', function () {
    $inviter = User::factory()->create();
    $token = Invitation::generateToken();

    $invitation = Invitation::factory()->create([
        'token' => Invitation::hashToken($token),
        'invited_by' => $inviter->id,
    ]);

    expect($invitation->token)->not->toBe($token)
        ->and($invitation->toArray())->not->toHaveKey('token')
        ->and($invitation->role)->toBe(OrganizationRole::Member)
        ->and($invitation->inviter->is($inviter))->toBeTrue()
        ->and($invitation->isExpired())->toBeFalse();

    $expired = Invitation::factory()->expired()->create();

    expect($expired->isExpired())->toBeTrue()
        ->and(Invitation::active()->pluck('id')->all())->toBe([$invitation->id]);
});

test('deleting the inviter keeps the invitation', function () {
    $inviter = User::factory()->create();
    $invitation = Invitation::factory()->create(['invited_by' => $inviter->id]);

    $inviter->delete();

    expect($invitation->fresh()->invited_by)->toBeNull();
});
