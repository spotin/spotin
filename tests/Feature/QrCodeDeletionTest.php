<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->member = User::factory()->create();
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);
    $this->spot = Spot::factory()->for($this->organization)->create(['name' => 'Harbour', 'latitude' => 46.5, 'longitude' => 6.6]);
});

test('the QR code of a deleted spot tells it is no longer in use, as gone, without naming its organization, in every language', function (string $locale) {
    $this->actingAs($this->member)->delete(route('spots.destroy', $this->spot))->assertRedirect();
    auth()->logout();

    $this->assertSoftDeleted($this->spot);

    $this->withUnencryptedCookie('locale', $locale)->get(route('spots.redirect', ['spot' => $this->spot->uuid]))
        ->assertGone()
        ->assertDontSee('ui.')
        ->assertSee('<title>'.e(__('ui.qr_codes.inactive.title', locale: $locale)), false)
        ->assertSee(e(__('ui.qr_codes.inactive.spot', locale: $locale)), false)
        ->assertDontSee('Acme')
        ->assertSee('href="'.route('index').'"', false);

    // Its page, to who cannot restore it, says it was removed as well
    $this->withUnencryptedCookie('locale', $locale)->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertGone()
        ->assertDontSee('ui.')
        ->assertSee('<title>'.e(__('ui.spots.removed.title', locale: $locale)), false)
        ->assertSee(e(__('ui.spots.removed.text', locale: $locale)), false)
        ->assertDontSee('Acme');
})->with(['en', 'fr']);

test('deleting an organization deletes its spots and its invitations, and their QR codes tell they are no longer in use', function () {
    Invitation::factory()->for($this->organization)->create();

    $this->actingAs($this->admin)->delete(route('organizations.destroy', $this->organization))->assertRedirect();
    auth()->logout();

    $this->assertSoftDeleted($this->organization);
    $this->assertSoftDeleted($this->spot);
    expect(Invitation::count())->toBe(0);

    $this->get(route('organizations.redirect', ['organization' => $this->organization->uuid]))
        ->assertGone()
        ->assertSee(e(__('ui.qr_codes.inactive.organization')), false);

    $this->get(route('spots.redirect', ['spot' => $this->spot->uuid]))
        ->assertGone()
        ->assertSee(e(__('ui.qr_codes.inactive.spot')), false)
        ->assertDontSee('Acme');

    $this->get(route('organizations.show', ['organization' => $this->organization->uuid]))
        ->assertGone()
        ->assertSee(e(__('ui.organizations.removed.title')), false)
        ->assertSee(e(__('ui.organizations.removed.text')), false);
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertGone()
        ->assertSee(e(__('ui.spots.removed.text')), false);
});

test('the deleted spots and organizations are left out everywhere', function () {
    $this->spot->delete();
    $other = Organization::factory()->create(['name' => 'Globex']);
    $other->users()->attach($this->member);
    $other->delete();

    $this->actingAs($this->member);

    $this->get(route('spots.index'))->assertOk()->assertDontSee('Harbour');
    $this->get(route('organizations.index'))->assertOk()->assertSee('Acme')->assertDontSee('Globex');
    expect(collect($this->getJson(route('spots.map'))->json('features')))->toBeEmpty();
    $this->get(route('dashboard'))->assertOk()->assertSeeInOrder([__('ui.dashboard.statistics.spots.title'), '>0<', __('ui.dashboard.statistics.organizations.title'), '>1<'], false);

    Sanctum::actingAs($this->admin, ['spots:read', 'organizations:read']);
    expect($this->getJson('/api/v1/spots')->assertOk()->json('data'))->toBeEmpty();
});

test('the API gives a deleted spot or organization to who can restore it, and tells the others it is gone', function () {
    $this->spot->delete();
    $other = Organization::factory()->create(['name' => 'Globex']);
    $other->delete();
    $outsider = User::factory()->create();

    Sanctum::actingAs($this->member, ['spots:read', 'organizations:read']);
    $this->getJson('/api/v1/spots/'.$this->spot->uuid)->assertOk()->assertJsonPath('data.attributes.deleted_at', fn ($date) => $date !== null);
    $this->getJson('/api/v1/organizations/'.$other->uuid)->assertGone();

    Sanctum::actingAs($outsider, ['spots:read']);
    $this->getJson('/api/v1/spots/'.$this->spot->uuid)->assertGone();

    Sanctum::actingAs($this->admin, ['spots:read', 'organizations:read']);
    $this->getJson('/api/v1/spots/'.$this->spot->uuid)->assertOk();
    $this->getJson('/api/v1/organizations/'.$other->uuid)->assertOk()->assertJsonPath('data.attributes.deleted_at', fn ($date) => $date !== null);
});

test('a new spot or organization can take the name of a deleted one', function () {
    $this->spot->delete();

    $this->actingAs($this->member)
        ->post(route('spots.store'), spotData(['organization' => $this->organization->uuid, 'name' => 'Harbour']))
        ->assertSessionHasNoErrors();

    $this->organization->delete();

    $this->actingAs($this->admin)->post(route('organizations.store'), ['name' => 'Acme'])->assertSessionHasNoErrors();

    expect(Spot::withTrashed()->where('name', 'Harbour')->count())->toBe(2)
        ->and(Organization::withTrashed()->where('name', 'Acme')->count())->toBe(2);
});

test('the warnings of the deletions tell what happens to the QR codes', function () {
    $this->actingAs($this->member)->get(route('spots.edit', ['spot' => $this->spot->uuid]))
        ->assertSee(e(__('ui.spots.delete.warning')), false);

    expect(__('ui.spots.delete.warning'))->toContain('no longer in use')
        ->and(__('ui.organizations.delete.warning'))->toContain('its spots')->toContain('no longer in use');
});
