<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->other = Organization::factory()->create();
    $this->spot = Spot::factory()->for($this->organization)->create();

    $this->admin = User::factory()->admin()->create();
    $this->responsible = User::factory()->create();
    $this->member = User::factory()->create();
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->responsible, ['role' => OrganizationRole::Responsible]);
    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);
    $this->other->users()->attach($this->outsider);
});

test('admins and members can manage the spots of an organization', function (string $user) {
    expect($this->$user->can('viewAny', [Spot::class, $this->organization]))->toBeTrue()
        ->and($this->$user->can('create', [Spot::class, $this->organization]))->toBeTrue()
        ->and($this->$user->can('view', $this->spot))->toBeTrue()
        ->and($this->$user->can('update', $this->spot))->toBeTrue()
        ->and($this->$user->can('delete', $this->spot))->toBeTrue();
})->with(['admin', 'responsible', 'member']);

test('everyone, guests included, can view spots, but only members can manage them or see their creator', function () {
    $guest = Gate::forUser(null);

    expect($guest->allows('viewAny', [Spot::class, $this->organization]))->toBeTrue()
        ->and($guest->allows('view', $this->spot))->toBeTrue()
        ->and($guest->allows('viewCreator', $this->spot))->toBeFalse()
        ->and($guest->allows('create', [Spot::class, $this->organization]))->toBeFalse()
        ->and($guest->allows('update', $this->spot))->toBeFalse()
        ->and($guest->allows('delete', $this->spot))->toBeFalse()
        ->and($this->outsider->can('view', $this->spot))->toBeTrue()
        ->and($this->outsider->can('viewCreator', $this->spot))->toBeFalse()
        ->and($this->member->can('viewCreator', $this->spot))->toBeTrue()
        ->and($this->outsider->can('create', [Spot::class, $this->organization]))->toBeFalse()
        ->and($this->outsider->can('update', $this->spot))->toBeFalse()
        ->and($this->outsider->can('delete', $this->spot))->toBeFalse();
});
