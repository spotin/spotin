<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->other = Organization::factory()->create();

    $this->admin = User::factory()->admin()->create();
    $this->responsible = User::factory()->create();
    $this->member = User::factory()->create();
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->responsible, ['role' => OrganizationRole::Responsible]);
    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);
    $this->other->users()->attach($this->outsider);
});

test('admins can manage every organization', function (string $ability) {
    expect($this->admin->can($ability, $this->organization))->toBeTrue();
})->with(['view', 'update', 'delete', 'manageMembers']);

test('admins can list and create organizations', function () {
    expect($this->admin->can('viewAny', Organization::class))->toBeTrue()
        ->and($this->admin->can('create', Organization::class))->toBeTrue();
});

test('only admins can list, create, edit and delete organizations', function (string $user) {
    expect($this->$user->can('viewAny', Organization::class))->toBeFalse()
        ->and($this->$user->can('create', Organization::class))->toBeFalse()
        ->and($this->$user->can('update', $this->organization))->toBeFalse()
        ->and($this->$user->can('delete', $this->organization))->toBeFalse();
})->with(['responsible', 'member', 'outsider']);

test('members of an organization can view it, others cannot', function () {
    expect($this->responsible->can('view', $this->organization))->toBeTrue()
        ->and($this->member->can('view', $this->organization))->toBeTrue()
        ->and($this->outsider->can('view', $this->organization))->toBeFalse();
});

test('only responsible users manage the members of their own organization', function () {
    expect($this->responsible->can('manageMembers', $this->organization))->toBeTrue()
        ->and($this->responsible->can('manageMembers', $this->other))->toBeFalse()
        ->and($this->member->can('manageMembers', $this->organization))->toBeFalse()
        ->and($this->outsider->can('manageMembers', $this->organization))->toBeFalse();
});

test('members and responsible users can leave, admins and outsiders cannot', function () {
    expect($this->responsible->can('leave', $this->organization))->toBeTrue()
        ->and($this->member->can('leave', $this->organization))->toBeTrue()
        ->and($this->outsider->can('leave', $this->organization))->toBeFalse()
        ->and($this->admin->can('leave', $this->organization))->toBeFalse();
});
