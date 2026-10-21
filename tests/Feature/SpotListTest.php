<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

beforeEach(function () {
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
    $this->other = Organization::factory()->create();

    $this->admin = User::factory()->admin()->create();
    $this->member = User::factory()->create(['name' => 'Mike Member']);
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);
    $this->other->users()->attach($this->outsider);

    $this->spot = Spot::factory()->for($this->organization)->create(['name' => 'Harbour', 'created_by' => $this->member->id]);
});

test('the spots of an organization are listed at /organizations/{uuid}/spots, with the filters', function () {
    Spot::factory()->for($this->organization)->create(['name' => 'Lighthouse', 'created_by' => $this->member->id]);
    Spot::factory()->for($this->other)->create(['name' => 'Foreign']);

    $this->actingAs($this->member);

    $this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertOk()
        ->assertSee('Harbour')->assertSee('Lighthouse')->assertDontSee('Foreign');

    $this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]).'?query=light')->assertOk()
        ->assertSee('Lighthouse')->assertDontSee('Harbour');

    $this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]).'?mine=1')->assertOk()
        ->assertSee('Harbour')->assertSee('Lighthouse');

    $this->get('/organizations/unknown/spots')->assertNotFound();

    // Guests can browse them as well.
    auth()->logout();
    $this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertOk()->assertSee('Harbour');
});

test('the filters stay on the page they are used on', function () {
    $nested = route('organizations.spots.index', ['organization' => $this->organization->uuid]);

    $html = $this->actingAs($this->member)->get($nested.'?query=harb')->assertOk()->getContent();

    expect($html)->toMatch('/<form\s+method="GET"\s+action="'.preg_quote($nested, '/').'"/')
        ->and($html)->toContain('href="'.$nested.'"')
        ->and($html)->not->toContain('name="organization"');

    $all = $this->get(route('spots.index').'?query=harb')->assertOk()->getContent();

    expect($all)->toMatch('/<form\s+method="GET"\s+action="'.preg_quote(route('spots.index'), '/').'"/')
        ->and($all)->toContain('href="'.route('spots.index').'"');

    // Without a filter, there is nothing to reset.
    $this->get($nested)->assertOk()->assertDontSee(__('ui.common.filters.reset'));
    $this->get(route('spots.index'))->assertOk()->assertDontSee(__('ui.common.filters.reset'));
});

test('only the members of an organization are offered to create a spot in it', function () {
    $create = route('organizations.spots.create', ['organization' => $this->organization->uuid]);

    $this->actingAs($this->member)->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertSee($create, false);
    $this->actingAs($this->admin)->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertSee($create, false);
    $this->actingAs($this->outsider)->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertDontSee($create, false);

    auth()->logout();
    $this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertDontSee($create, false);

});

test('the trail of the spots of an organization starts at the organizations', function () {
    $crumbs = function (string $html) {
        if (! preg_match('/<nav aria-label="[^"]*" class="breadcrumbs[^"]*">(.*?)<\/nav>/s', $html, $nav)) {
            return [];
        }

        preg_match_all('/<(?:a|span)\b[^>]*>\s*([^<]+?)\s*<\/(?:a|span)>/', $nav[1], $labels);

        return $labels[1];
    };

    // The trail is shown to the logged in users only
    $this->actingAs($this->member);

    expect($crumbs($this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->getContent()))->toBe(['Organizations', $this->organization->name, 'Spots'])
        ->and($crumbs($this->get(route('organizations.spots.create', ['organization' => $this->organization->uuid]))->getContent()))->toBe(['Organizations', $this->organization->name, 'Spots', 'New spot'])
        ->and($crumbs($this->get(route('spots.index'))->getContent()))->toBe([]);
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
        ->assertSee(__('ui.spots.index.no_result'))
        ->assertSee(__('ui.common.filters.reset'));

    // The filters are validated like in the API: a malformed one sends the visitor back with the error.
    $this->get('/spots?query[]=x')->assertRedirect()->assertSessionHasErrors('query');
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

test('hidden spots are only listed for the members of their organization and administrators, and stay reachable', function () {
    Spot::factory()->for($this->organization)->hidden()->create(['name' => 'Secret Cove']);

    $this->get(route('spots.index'))->assertOk()->assertSee('Harbour')->assertDontSee('Secret Cove');
    $this->get(route('organizations.spots.index', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('Secret Cove');
    $this->get(route('spots.index', ['query' => 'Secret']))->assertOk()->assertDontSee('Secret Cove');
    $this->actingAs($this->outsider)->get(route('spots.index'))->assertOk()->assertDontSee('Secret Cove');

    foreach ([$this->member, $this->admin] as $user) {
        $this->actingAs($user)->get(route('spots.index'))
            ->assertOk()
            ->assertSee('Secret Cove')
            ->assertSee(__('ui.spots.common.hidden'));
    }

    auth()->logout();

    $this->get(route('spots.show', ['spot' => Spot::firstWhere('name', 'Secret Cove')->uuid]))->assertOk()->assertSee('Secret Cove');
});
