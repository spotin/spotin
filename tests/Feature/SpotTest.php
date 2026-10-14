<?php

use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->creator = User::factory()->create();
    $this->organization->users()->attach($this->creator);
    $this->spot = Spot::factory()->for($this->organization)->create(['created_by' => $this->creator->id]);
});

test('a spot belongs to an organization and has a creator', function () {
    expect($this->spot->organization->is($this->organization))->toBeTrue()
        ->and($this->spot->creator->is($this->creator))->toBeTrue()
        ->and($this->organization->spots)->toHaveCount(1);
});

test('a spot is identified publicly by its uuid', function () {
    expect($this->spot->uuid)->toBeString()
        ->and($this->spot->getRouteKeyName())->toBe('uuid');
});

test('deleting the creator keeps the spot and clears its creator', function () {
    $this->creator->delete();

    expect($this->spot->fresh())->not->toBeNull()
        ->and($this->spot->fresh()->created_by)->toBeNull()
        ->and($this->spot->fresh()->creator)->toBeNull();
});

test('the creator leaving the organization keeps the spot and its creator', function () {
    $this->organization->users()->detach($this->creator);

    expect($this->spot->fresh()->creator->is($this->creator))->toBeTrue();
});

test('deleting the organization deletes its spots', function () {
    $this->organization->delete();

    expect(Spot::count())->toBe(0);
});

test('a spot name is unique within an organization only', function () {
    expect(fn () => Spot::factory()->for($this->organization)->create(['name' => $this->spot->name]))
        ->toThrow(QueryException::class);

    Spot::factory()->create(['name' => $this->spot->name]);

    expect(Spot::where('name', $this->spot->name)->count())->toBe(2);
});

test('a spot can be created without a creator', function () {
    expect(Spot::factory()->withoutCreator()->create()->creator)->toBeNull();
});
