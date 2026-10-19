<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->other = Organization::factory()->create();

    $this->admin = User::factory()->admin()->create();
    $this->member = User::factory()->create(['name' => 'Mike Member']);
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);
    $this->other->users()->attach($this->outsider);

    $this->spot = Spot::factory()->for($this->organization)->create(['name' => 'Harbour', 'created_by' => $this->member->id]);
});

test('the pages render in every language without untranslated keys', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)->actingAs($this->member);

    $this->get(route('organizations.show', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.');
    $this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.')->assertSee('Harbour')->assertSee(route('organizations.spots.create', ['organization' => $this->organization->uuid]), false);
    $this->get(route('spots.index'))->assertOk()->assertDontSee('ui.')->assertSee('Harbour');
    $this->get(route('organizations.spots.create', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.');
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.')->assertSee('Mike Member');
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.');
})->with(['en', 'fr']);

test('everyone can see the spots, but only members can edit them or see their creator', function () {
    foreach ([null, $this->outsider] as $user) {
        $user ? $this->actingAs($user) : auth()->logout();

        $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Harbour')->assertDontSee('Mike Member')
            ->assertDontSee(route('spots.edit', ['spot' => $this->spot->uuid]), false);
        $this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertOk()->assertSee('Harbour');
        $this->get('/spots')->assertOk()->assertSee('Harbour');
    }

    $this->actingAs($this->member)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Mike Member');
});

test('only members and admins can edit a spot or open the form to create one in its organization', function () {
    foreach ([$this->member, $this->admin] as $user) {
        $this->actingAs($user)->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk();
        $this->get(route('organizations.spots.create', ['organization' => $this->organization->uuid]))->assertOk();
    }

    $this->actingAs($this->outsider);
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertForbidden();
    $this->get(route('organizations.spots.create', ['organization' => $this->organization->uuid]))->assertForbidden();

    auth()->logout();
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertRedirect(route('login'));
    $this->get(route('organizations.spots.create', ['organization' => $this->organization->uuid]))->assertRedirect(route('login'));

    $this->actingAs($this->admin)->get('/organizations/unknown/spots/create')->assertNotFound();
});

test('the creation form is in the organization and only asks for the name', function () {
    $this->actingAs($this->member)->get(route('organizations.spots.create', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee('<form method="POST" action="'.route('organizations.spots.store', ['organization' => $this->organization->uuid]).'"', false)
        ->assertSee('name="name"', false);

});

test('a spot whose creator is gone is still shown', function () {
    $this->member->delete();

    $this->actingAs($this->admin)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Harbour');
});

test('a member creates a spot and becomes its creator', function () {
    $response = $this->actingAs($this->member)
        ->post(route('organizations.spots.store', ['organization' => $this->organization->uuid]), ['name' => 'Lighthouse']);

    $spot = Spot::firstWhere('name', 'Lighthouse');

    $response->assertRedirect(route('spots.show', ['spot' => $spot->uuid]))->assertSessionHas('success', __('ui.organizations.spots.feedback.spot_created'));
    expect($spot->organization->is($this->organization))->toBeTrue()
        ->and($spot->creator->is($this->member))->toBeTrue();
});

test('outsiders cannot create, update or delete spots', function () {
    $this->actingAs($this->outsider);

    $this->post(route('organizations.spots.store', ['organization' => $this->organization->uuid]), ['name' => 'Lighthouse'])->assertForbidden();
    $this->patch(route('spots.update', $this->spot), ['name' => 'Renamed'])->assertForbidden();
    $this->delete(route('spots.destroy', $this->spot))->assertForbidden();

    expect(Spot::count())->toBe(1)->and($this->spot->fresh()->name)->toBe('Harbour');
});

test('spot names are validated and unique within the organization', function () {
    $this->actingAs($this->member);

    $this->post(route('organizations.spots.store', ['organization' => $this->organization->uuid]), ['name' => ''])->assertSessionHasErrors('name');
    $this->post(route('organizations.spots.store', ['organization' => $this->organization->uuid]), ['name' => 'A'])->assertSessionHasErrors('name');
    $this->post(route('organizations.spots.store', ['organization' => $this->organization->uuid]), ['name' => 'Harbour'])->assertSessionHasErrors('name');

    // The organization comes from the URL: nothing about it is read from the form.
    $this->post(route('organizations.spots.store', ['organization' => $this->organization->uuid]), ['name' => 'Lighthouse', 'organization' => $this->other->uuid])->assertSessionHasNoErrors();
    expect(Spot::firstWhere('name', 'Lighthouse')->organization->is($this->organization))->toBeTrue();

    $this->actingAs($this->admin)
        ->post(route('organizations.spots.store', ['organization' => $this->other->uuid]), ['name' => 'Harbour'])
        ->assertSessionHasNoErrors();

    $this->post('/organizations/unknown/spots', ['name' => 'Pier'])->assertNotFound();
});

test('a member renames a spot, keeping its creator', function () {
    $this->actingAs($this->admin)->patch(route('spots.update', $this->spot), ['name' => 'Harbour'])->assertSessionHasNoErrors();
    $this->patch(route('spots.update', $this->spot), ['name' => 'Marina'])->assertSessionHas('success', __('ui.spots.feedback.spot_updated'));

    expect($this->spot->fresh()->name)->toBe('Marina')
        ->and($this->spot->fresh()->created_by)->toBe($this->member->id);
});

test('a member deletes a spot', function () {
    $this->actingAs($this->member)
        ->delete(route('spots.destroy', $this->spot))
        ->assertRedirect(route('organizations.spots.index', ['organization' => $this->organization->uuid]))
        ->assertSessionHas('success', __('ui.spots.feedback.spot_deleted'));

    expect(Spot::count())->toBe(0);
});

test('the spot pages show a trail starting at the spots, without the organization', function () {
    $crumbs = function (string $html) {
        preg_match('/<nav aria-label="[^"]*" class="breadcrumbs[^"]*">(.*?)<\/nav>/s', $html, $nav);
        preg_match_all('/<(?:a|span)\b[^>]*>\s*([^<]+?)\s*<\/(?:a|span)>/', $nav[1], $labels);

        return $labels[1];
    };

    $this->actingAs($this->member);

    expect($crumbs($this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->getContent()))->toBe(['Spots', 'Harbour'])
        ->and($crumbs($this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->getContent()))->toBe(['Spots', 'Harbour', 'Edit the spot']);
});
