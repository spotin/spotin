<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

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

test('admins can create organizations', function () {
    expect($this->admin->can('create', Organization::class))->toBeTrue();
});

test('only admins can create, edit and delete organizations', function (string $user) {
    expect($this->$user->can('create', Organization::class))->toBeFalse()
        ->and($this->$user->can('update', $this->organization))->toBeFalse()
        ->and($this->$user->can('delete', $this->organization))->toBeFalse();
})->with(['responsible', 'member', 'outsider']);

test('everyone can view an organization, but only its members see its members', function () {
    expect($this->outsider->can('view', $this->organization))->toBeTrue()
        ->and(Gate::forUser(null)->allows('view', $this->organization))->toBeTrue()
        ->and($this->responsible->can('viewMembers', $this->organization))->toBeTrue()
        ->and($this->member->can('viewMembers', $this->organization))->toBeTrue()
        ->and($this->outsider->can('viewMembers', $this->organization))->toBeFalse()
        ->and($this->admin->can('viewMembers', $this->organization))->toBeTrue();
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
