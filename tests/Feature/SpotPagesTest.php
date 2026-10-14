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
    $this->get(route('spots.index', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.')->assertSee('Harbour');
    $this->get(route('spots.index'))->assertOk()->assertDontSee('ui.')->assertSee(route('spots.create'), false);
    $this->get(route('spots.create', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.');
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.')->assertSee('Mike Member');
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.');
})->with(['en', 'fr']);

test('everyone can see the spots, but only members can edit them or see their creator', function () {
    foreach ([null, $this->outsider] as $user) {
        $user ? $this->actingAs($user) : auth()->logout();

        $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Harbour')->assertDontSee('Mike Member')
            ->assertDontSee(route('spots.edit', ['spot' => $this->spot->uuid]), false);
        $this->get(route('spots.index', ['organization' => $this->organization->uuid]))->assertOk()->assertSee('Harbour');
        $this->get('/spots')->assertOk()->assertSee('Harbour');
    }

    $this->actingAs($this->member)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Mike Member');
});

test('only logged-in users access the spot forms, and only members and admins can edit', function () {
    foreach ([$this->member, $this->admin] as $user) {
        $this->actingAs($user)->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk();
        $this->get(route('spots.create', ['organization' => $this->organization->uuid]))->assertOk();
    }

    $this->actingAs($this->outsider);
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertForbidden();
    $this->get(route('spots.create'))->assertOk();

    auth()->logout();
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertRedirect(route('login'));
    $this->get(route('spots.create'))->assertRedirect(route('login'));
});

test('the creation form offers the organizations of the user and preselects the requested one', function () {
    $this->member->organizations()->attach($this->other);

    $this->actingAs($this->member)->get(route('spots.create', ['organization' => $this->other->uuid]))
        ->assertOk()
        ->assertSee('value="'.$this->organization->uuid.'"', false)
        ->assertSeeInOrder(['value="'.$this->other->uuid.'"', 'selected'], false);

    $this->actingAs($this->outsider)->get(route('spots.create'))
        ->assertDontSee('value="'.$this->organization->uuid.'"', false);

    $this->actingAs($this->admin)->get(route('spots.create'))
        ->assertSee('value="'.$this->organization->uuid.'"', false)
        ->assertSee('value="'.$this->other->uuid.'"', false);

    $this->actingAs(User::factory()->create())->get(route('spots.create'))
        ->assertOk()
        ->assertSee(__('ui.spots.create.no-organization'))
        ->assertDontSee('<form method="POST" action="'.route('spots.store').'"', false);
});

test('the spots can be filtered by organization', function () {
    Spot::factory()->for($this->other)->create(['name' => 'Foreign']);

    $this->actingAs($this->member)->get(route('spots.index', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee('Harbour')
        ->assertDontSee('Foreign')
        ->assertSee(route('spots.create', ['organization' => $this->organization->uuid]), false)
        ->assertSee(__('ui.common.filters.reset'))
        ->assertDontSee('<select', false);

    $this->get(route('spots.index', ['organization' => 'unknown']))->assertNotFound();
});

test('a spot whose creator is gone is still shown', function () {
    $this->member->delete();

    $this->actingAs($this->admin)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Harbour');
});

test('a member creates a spot and becomes its creator', function () {
    $response = $this->actingAs($this->member)
        ->post(route('spots.store'), ['organization' => $this->organization->uuid, 'name' => 'Lighthouse']);

    $spot = Spot::firstWhere('name', 'Lighthouse');

    $response->assertRedirect(route('spots.show', ['spot' => $spot->uuid]))->assertSessionHas('status', 'spot-created');
    expect($spot->organization->is($this->organization))->toBeTrue()
        ->and($spot->creator->is($this->member))->toBeTrue();
});

test('outsiders cannot create, update or delete spots', function () {
    $this->actingAs($this->outsider);

    $this->post(route('spots.store'), ['organization' => $this->organization->uuid, 'name' => 'Lighthouse'])->assertForbidden();
    $this->put(route('spots.update', $this->spot), ['name' => 'Renamed'])->assertForbidden();
    $this->delete(route('spots.destroy', $this->spot))->assertForbidden();

    expect(Spot::count())->toBe(1)->and($this->spot->fresh()->name)->toBe('Harbour');
});

test('spot names are validated and unique within the organization', function () {
    $this->actingAs($this->member);

    $organization = $this->organization->uuid;

    $this->post(route('spots.store'), ['organization' => $organization, 'name' => ''])->assertSessionHasErrors('name');
    $this->post(route('spots.store'), ['organization' => $organization, 'name' => 'Harbour'])->assertSessionHasErrors('name');
    $this->post(route('spots.store'), ['name' => 'Lighthouse'])->assertSessionHasErrors('organization');
    $this->post(route('spots.store'), ['organization' => 'unknown', 'name' => 'Lighthouse'])->assertSessionHasErrors('organization');

    $this->actingAs($this->admin)->post(route('spots.store'), ['organization' => $this->other->uuid, 'name' => 'Harbour'])->assertSessionHasNoErrors();
});

test('a member renames a spot, keeping its creator', function () {
    $this->actingAs($this->admin)->put(route('spots.update', $this->spot), ['name' => 'Harbour'])->assertSessionHasNoErrors();
    $this->put(route('spots.update', $this->spot), ['name' => 'Marina'])->assertSessionHas('status', 'spot-updated');

    expect($this->spot->fresh()->name)->toBe('Marina')
        ->and($this->spot->fresh()->created_by)->toBe($this->member->id);
});

test('a member deletes a spot', function () {
    $this->actingAs($this->member)
        ->delete(route('spots.destroy', $this->spot))
        ->assertRedirect(route('spots.index', ['organization' => $this->organization->uuid]))
        ->assertSessionHas('status', 'spot-deleted');

    expect(Spot::count())->toBe(0);
});

test('the spots can be filtered to those of the organizations the user belongs to, in every language', function (string $locale) {
    Spot::factory()->for($this->other)->create(['name' => 'Foreign']);

    $this->withUnencryptedCookie('locale', $locale)->actingAs($this->member);

    $on = $this->get('/spots?mine=1')
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('Harbour')
        ->assertDontSee('Foreign')
        ->getContent();

    $off = $this->get('/spots')->assertOk()->assertSee('Harbour')->assertSee('Foreign')
        ->assertSee('>'.__('ui.spots.index.title').'</h1>', false)
        ->getContent();

    expect(mineIsChecked($on))->toBeTrue()->and(mineIsChecked($off))->toBeFalse();

    $this->get('/spots?mine=1')->assertSee('>'.__('ui.spots.index.title').'</h1>', false)->assertSee(__('ui.spots.index.filter.mine'));
})->with(['en', 'fr']);

test('the spots of the user include the ones they created in organizations they do not belong to', function () {
    Spot::factory()->for($this->other)->create(['name' => 'Created by admin', 'created_by' => $this->admin->id]);
    Spot::factory()->for($this->other)->create(['name' => 'Someone elses']);

    $this->actingAs($this->admin)->get('/spots?mine=1')
        ->assertOk()
        ->assertSee('Created by admin')
        ->assertDontSee('Someone elses')
        ->assertDontSee('Harbour');
});

test('the filter is empty for users without spots, and only offered to logged-in users', function () {
    $this->actingAs(User::factory()->create())->get('/spots?mine=1')->assertOk()->assertDontSee('ui.')->assertDontSee('Harbour');

    auth()->logout();
    $this->get('/spots')->assertOk()->assertDontSee('name="mine"', false)->assertSee('name="query"', false);
    $this->get('/spots?mine=1')->assertOk()->assertSee('Harbour');
});

test('the filter survives pagination', function () {
    Spot::factory()->for($this->organization)->count(30)->create(['created_by' => $this->member->id]);

    $this->actingAs($this->member)->get('/spots?mine=1')->assertOk()->assertSee('mine=1&amp;page=2', false);
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

test('the spots can be searched by name and by organization, with a count and a way to reset', function () {
    Spot::factory()->for($this->organization)->create(['name' => 'Lighthouse']);
    Spot::factory()->for($this->other)->create(['name' => 'Harbour Two']);

    $this->actingAs($this->member);

    $this->get('/spots')->assertOk()->assertSee(trans_choice('ui.spots.index.count', 3, ['count' => 3]))->assertDontSee(__('ui.common.filters.reset'));

    $this->get('/spots?query=harbour')->assertOk()
        ->assertSee('Harbour')->assertSee('Harbour Two')->assertDontSee('Lighthouse')
        ->assertSee(trans_choice('ui.spots.index.count', 2, ['count' => 2]))
        ->assertSee('value="harbour"', false)
        ->assertSee(__('ui.common.filters.reset'));

    $this->get('/spots?query=harbour&organization='.$this->other->uuid)->assertOk()
        ->assertSee('Harbour Two')->assertDontSee('Lighthouse');

    $this->get('/spots?query=nothing-like-this')->assertOk()
        ->assertSee(__('ui.spots.index.no-result'))
        ->assertSee(__('ui.common.filters.reset'));

    $this->get('/spots?query[]=x&organization[]=y')->assertOk()->assertSee('Harbour');
});

test('the filters survive pagination and combine with the mine checkbox', function () {
    Spot::factory()->for($this->organization)->count(30)->sequence(fn ($sequence) => ['name' => 'Pier '.$sequence->index, 'created_by' => $this->member->id])->create();

    $this->actingAs($this->member)->get('/spots?query=pier&mine=1')->assertOk()->assertSee('mine=1&amp;page=2', false)->assertSee('query=pier', false);
});

test('the filters are submitted with a visible search button and a checkbox, never automatically', function () {
    $html = $this->actingAs($this->member)->get('/spots')->assertOk()->getContent();

    expect($html)->toMatch('/<button type="submit" class="btn join-item">\s*<svg[^>]*>.*?<\/svg>\s*'.preg_quote(__('ui.common.filters.search'), '/').'\s*<\/button>/s')
        ->and($html)->toMatch('/<input type="checkbox" name="mine"[^>]*class="checkbox"/')
        ->and($html)->not->toContain('this.form.submit()')
        ->and($html)->not->toContain('class="toggle toggle-sm"')
        ->and($html)->not->toContain('<select');
});

test('my spots only lists the spots the user created, not the other ones of their organizations', function () {
    $this->organization->users()->attach($this->admin);
    Spot::factory()->for($this->organization)->create(['name' => 'Colleague Spot', 'created_by' => $this->admin->id]);
    Spot::factory()->for($this->organization)->withoutCreator()->create(['name' => 'Orphan Spot']);

    $this->actingAs($this->member)->get('/spots?mine=1')
        ->assertOk()
        ->assertSee('Harbour')
        ->assertDontSee('Colleague Spot')
        ->assertDontSee('Orphan Spot');

    $this->get('/spots')->assertSee('Colleague Spot')->assertSee('Orphan Spot');
});
