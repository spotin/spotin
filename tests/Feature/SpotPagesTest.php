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

    // A fixed description and website: random text could contain `ui.`, which the pages are checked not to show
    $this->spot = Spot::factory()->for($this->organization)->create([
        'name' => 'Harbour',
        'description' => 'A **harbour** by the lake.',
        'website' => 'https://harbour.example.com',
        'created_by' => $this->member->id,
    ]);
});

test('the pages render in every language without untranslated keys', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)->actingAs($this->member);

    $this->get(route('organizations.show', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.');
    $this->get(route('organizations.show', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.')->assertSee('Harbour')->assertSee(route('spots.create', ['organization' => $this->organization->uuid]), false);
    $this->get(route('spots.index'))->assertOk()->assertDontSee('ui.')->assertSee('Harbour');
    $this->get(route('spots.create', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.');
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.')->assertSee('Mike Member');
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.');
})->with(['en', 'fr']);

test('everyone can see the spots, but only members can edit them or see their creator', function () {
    foreach ([null, $this->outsider] as $user) {
        $user ? $this->actingAs($user) : auth()->logout();

        $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Harbour')->assertDontSee('Mike Member')
            ->assertDontSee(route('spots.edit', ['spot' => $this->spot->uuid]), false);
        $this->get(route('organizations.show', ['organization' => $this->organization->uuid]))->assertOk()->assertSee('Harbour');
    }

    $this->actingAs($this->member)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Mike Member');
});

test('only members and admins can edit a spot, and create one in its organization', function () {
    // The organization of the address is the one of the new spot, sent as is, without choosing
    $hidden = fn (Organization $organization) => '<input type="hidden" name="organization" value="'.$organization->uuid.'"';

    foreach ([$this->member, $this->admin] as $user) {
        $this->actingAs($user)->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk();
        $this->get(route('spots.create', ['organization' => $this->organization->uuid]))
            ->assertOk()
            ->assertSee($hidden($this->organization), false)
            ->assertSee(__('ui.spots.create.introduction_in', ['organization' => $this->organization->name]));
    }

    // The members of another organization create spots in theirs only: their only one, without choosing
    $this->actingAs($this->outsider);
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertForbidden();
    $this->get(route('spots.create', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertDontSee('value="'.$this->organization->uuid.'"', false)
        ->assertSee($hidden($this->other), false)
        // Nothing to change
        ->assertDontSee(__('ui.spots.create.change_organization'));

    // The users without an organization create no spot
    $this->actingAs(User::factory()->create())->get(route('spots.create'))->assertForbidden();

    auth()->logout();
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertRedirect(route('login'));
    $this->get(route('spots.create'))->assertRedirect(route('login'));
});

test('the creation form is in steps: the organization to choose, the setup, the name and description, the website and visibility, and the location, with the submit button', function () {
    // A member of two organizations chooses the one of the spot, among cards
    $this->other->users()->attach($this->member);

    $html = $this->actingAs($this->member)->get(route('spots.create'))
        ->assertOk()
        ->assertSee('<form method="POST" action="'.route('spots.store').'"', false)
        ->assertSee(e(__('ui.spots.create.introduction')), false)
        ->assertSeeInOrder([
            __('ui.spots.common.forms.steps.organization'), 'type="radio"', 'name="organization"',
            __('ui.spots.common.forms.steps.setup'), 'name="needs_setup"',
            __('ui.spots.common.forms.steps.name_and_description'), 'name="name"', 'name="description"',
            __('ui.spots.common.forms.steps.website'), 'name="website"', 'name="redirect_to_website"', 'name="is_hidden"',
            __('ui.spots.common.forms.steps.location'), 'name="latitude"', 'name="longitude"', 'type="submit"',
        ], false)
        ->getContent();

    // A card for each of its organizations, sorted by name
    expect(substr_count($html, 'data-step '))->toBe(5)
        ->and($html)->toContain('value="'.$this->organization->uuid.'"')
        ->and($html)->toContain('value="'.$this->other->uuid.'"')
        // A new spot opens on the first step, an existing one on any
        ->and($html)->not->toContain('data-steps-complete');

    // From the page of an organization: no step to choose it, and a way to choose another one
    $html = $this->get(route('spots.create', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee('<input type="hidden" name="organization" value="'.$this->organization->uuid.'"', false)
        ->assertSee(__('ui.spots.create.introduction_in', ['organization' => $this->organization->name]))
        ->assertSee('href="'.route('spots.create').'"', false)
        ->assertDontSee(__('ui.spots.common.forms.fields.organization.question'))
        ->getContent();

    expect(substr_count($html, 'data-step '))->toBe(4);

    // An existing spot keeps its organization: no step to choose it
    $edit = $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()
        ->assertDontSee('name="organization"', false)
        ->assertSee('data-steps-complete', false)
        ->assertSee('value="https://harbour.example.com"', false)
        ->assertSee('A **harbour** by the lake.')
        ->getContent();

    expect(substr_count($edit, 'data-step '))->toBe(4);
});

test('the administrators choose among all the organizations, as cards', function () {
    Organization::factory()->count(5)->create();

    $html = $this->actingAs($this->admin)->get(route('spots.create'))->assertOk()->getContent();

    // A radio for each organization, and no list
    expect(preg_match_all('/type="radio"\s+name="organization"/', $html))->toBe(Organization::count())
        ->and($html)->not->toMatch('/<select[^>]*name="organization"/s');
});

test('the organization is offered to choose again when it is refused', function () {
    // Back on the form from the page of its organization, with the error of the one sent instead
    $this->actingAs($this->member)
        ->from(route('spots.create', ['organization' => $this->organization->uuid]))
        ->followingRedirects()
        ->post(route('spots.store'), ['organization' => $this->other->uuid] + spotData())
        ->assertOk()
        ->assertSee(__('ui.spots.common.forms.fields.organization.question'))
        ->assertSee(__('ui.spots.common.forms.fields.organization.not_allowed'))
        ->assertDontSee('<input type="hidden" name="organization"', false);
});

test('the creation and edit pages say which organization, and which spot, the form is for', function () {
    $this->actingAs($this->member);

    $this->get(route('spots.create', ['organization' => $this->organization->uuid]))->assertOk()
        ->assertSee(__('ui.spots.create.introduction_in', ['organization' => $this->organization->name]));

    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()
        ->assertSee(__('ui.spots.edit.introduction', ['spot' => 'Harbour', 'organization' => $this->organization->name]));
});

test('a member creates a spot with all its details', function () {
    $this->actingAs($this->member)
        ->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData([
            'description' => "## The light\n\nA *white* tower.",
            'website' => 'https://lighthouse.example.com',
            'redirect_to_website' => '1',
            'is_hidden' => '1',
        ]))
        ->assertSessionHasNoErrors();

    expect(Spot::firstWhere('name', 'Lighthouse'))
        ->description->toBe("## The light\n\nA *white* tower.")
        ->latitude->toBe(46.5197)
        ->longitude->toBe(6.6323)
        ->website->toBe('https://lighthouse.example.com')
        ->redirect_to_website->toBeTrue()
        ->is_hidden->toBeTrue();
});

test('the unchecked toggles are sent by the form and turn the options off', function () {
    $this->spot->update(['redirect_to_website' => true, 'is_hidden' => true]);

    $this->actingAs($this->member)
        ->patch(route('spots.update', $this->spot), ['redirect_to_website' => '0', 'is_hidden' => '0'])
        ->assertSessionHasNoErrors();

    expect($this->spot->fresh())
        ->redirect_to_website->toBeFalse()
        ->is_hidden->toBeFalse()
        ->name->toBe('Harbour');
});

test('the details of a spot are validated', function (array $overrides, string $field) {
    $this->actingAs($this->member)
        ->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData($overrides))
        ->assertSessionHasErrors($field);

    expect(Spot::where('name', 'Lighthouse')->exists())->toBeFalse();
})->with([
    'longitude without latitude' => [['latitude' => ''], 'latitude'],
    'latitude out of range' => [['latitude' => 91], 'latitude'],
    'latitude without longitude' => [['longitude' => ''], 'longitude'],
    'longitude out of range' => [['longitude' => -181], 'longitude'],
    'not a website' => [['website' => 'not a website'], 'website'],
    'website with an unsafe scheme' => [['website' => 'javascript:alert(1)'], 'website'],
    'too long description' => [['description' => str_repeat('a', 10001)], 'description'],
]);

test('a spot can be created without a location, and its location removed', function () {
    $this->actingAs($this->member)
        ->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData(['latitude' => '', 'longitude' => '']))
        ->assertSessionHasNoErrors();

    expect(Spot::firstWhere('name', 'Lighthouse'))->latitude->toBeNull()->longitude->toBeNull();

    $this->actingAs($this->member)
        ->patch(route('spots.update', $this->spot), ['latitude' => '', 'longitude' => ''])
        ->assertSessionHasNoErrors();

    expect($this->spot->fresh())->hasLocation()->toBeFalse();
});

test('a spot cannot redirect its visitors without a website', function () {
    $this->actingAs($this->member)
        ->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData(['website' => '', 'redirect_to_website' => '1']))
        ->assertSessionHasErrors(['redirect_to_website' => __('ui.spots.common.forms.fields.redirect_to_website.needs_website')]);

    $this->patch(route('spots.update', $this->spot), ['website' => '', 'redirect_to_website' => '1'])
        ->assertSessionHasErrors('redirect_to_website');

    // A website that is not valid only gets its own error
    $this->patch(route('spots.update', $this->spot), ['website' => 'not a website', 'redirect_to_website' => '1'])
        ->assertSessionHasErrors('website')
        ->assertSessionDoesntHaveErrors('redirect_to_website');

    expect(Spot::where('name', 'Lighthouse')->exists())->toBeFalse()
        ->and($this->spot->fresh()->redirect_to_website)->toBeFalse();
});

test('everyone who scans the spot is redirected to the website, and its editors see a note on its page', function () {
    $this->spot->update(['redirect_to_website' => true]);

    $this->get(route('spots.redirect', ['spot' => $this->spot->uuid]))->assertRedirect('https://harbour.example.com')->assertStatus(302);

    foreach ([$this->outsider, $this->member, $this->admin] as $user) {
        $this->actingAs($user)->get(route('spots.redirect', ['spot' => $this->spot->uuid]))->assertRedirect('https://harbour.example.com');
    }

    // A badge, and the whole note, with the domain of the website, in its tooltip
    $badge = e(__('ui.spots.show.manage.badges.redirect'));
    $note = e(__('ui.spots.show.manage.notes.redirect', ['website' => 'harbour.example.com']));

    foreach ([$this->member, $this->admin] as $user) {
        $this->actingAs($user)->get(route('spots.show', ['spot' => $this->spot->uuid]))
            ->assertOk()
            ->assertSee($badge, false)
            ->assertSee('data-tip="'.$note.'"', false);
    }

    $this->actingAs($this->outsider)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee($note, false);
});
test('the page of a spot that redirects its visitors shows its details to everyone', function () {
    $this->spot->update(['redirect_to_website' => true]);

    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Harbour');
    $this->actingAs($this->outsider)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee('Harbour');
});

test('the visitors see the page of the spot when it does not redirect them, or has no website', function () {
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee(__('ui.spots.show.visit', ['website' => 'harbour.example.com']))
        ->assertSee('href="https://harbour.example.com"', false)
        ->assertDontSee(e(__('ui.spots.show.manage.notes.redirect', ['website' => 'https://harbour.example.com'])), false);

    $this->spot->update(['website' => null, 'redirect_to_website' => true]);

    // Without a website, neither the button nor its dialog
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()
        ->assertDontSee(__('ui.spots.show.visit', ['website' => 'harbour.example.com']))
        ->assertDontSee(e(__('ui.common.leave.title', ['app' => config('app.name')])), false);
});

test('the page of a spot shows its map and coordinates, its name and organization, its actions, its description, and the way to its website with a warning', function () {
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSeeInOrder([
            'data-map=', __('ui.spots.show.fields.coordinates'),
            '<h1', 'Harbour',
            route('organizations.show', ['organization' => $this->organization->uuid]), e($this->organization->name),
            __('ui.common.actions.like'), __('ui.common.actions.report'), __('ui.common.actions.share'),
            '<strong>harbour</strong>',
            __('ui.spots.show.visit', ['website' => 'harbour.example.com']),
            e(__('ui.common.leave.title', ['app' => config('app.name')])), '<strong class="break-all">https://harbour.example.com</strong>',
            __('ui.common.leave.continue'),
        ], false)
        // The guests do not see who created the spot, nor when, nor the tools of its managers
        ->assertDontSee('Mike Member')
        ->assertDontSee($this->spot->created_at->isoFormat('LL'))
        ->assertDontSee(__('ui.spots.show.manage.title'));

    // The members of the organization and the administrators do, in the box of the managers
    $created = __('ui.spots.show.manage.created_by', ['creator' => 'Mike Member', 'date' => $this->spot->created_at->isoFormat('LL')]);

    foreach ([$this->member, $this->admin] as $user) {
        $this->actingAs($user)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertSee($created);
    }

    $this->actingAs($this->outsider)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertDontSee('Mike Member');
});

test('the managers of a spot manage it from the top of its page, with its notes', function () {
    $this->spot->update(['is_hidden' => true]);

    $html = $this->actingAs($this->member)->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSeeInOrder([
            __('ui.spots.show.manage.title'),
            route('spots.edit', ['spot' => $this->spot->uuid]), 'popovertarget="qr-code-popover"',
            e(__('ui.spots.show.manage.notes.hidden', ['organization' => $this->organization->name])),
            '<h1', 'Harbour',
            __('ui.common.actions.like'), __('ui.common.actions.report'), __('ui.common.actions.share'),
        ], false)
        ->getContent();

    // Nothing to delete the spot by mistake
    expect($html)->not->toContain('delete-spot-'.$this->spot->uuid.'-modal');
});

test('the spot is deleted from the danger zone of its edit page, for its managers only', function () {
    // The deletion has the address of the spot, as its edit form: told apart by its dialog
    $modal = 'delete-spot-'.$this->spot->uuid.'-modal';

    $this->actingAs($this->member)->get(route('spots.edit', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSeeInOrder(['name="name"', __('ui.spots.edit.danger.title'), $modal, '<input type="hidden" name="_method" value="DELETE"'], false);

    // Who sets up a spot cannot delete it
    $this->spot->update(['needs_setup' => true]);
    auth()->logout();

    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertDontSee(__('ui.spots.edit.danger.title'))
        ->assertDontSee($modal, false);
});

test('the description is rendered from Markdown, without HTML or unsafe links', function () {
    $this->spot->update(['description' => "## Welcome\n\nA **bold** <script>alert(1)</script> [trap](javascript:alert(1)) [site](https://example.com)"]);

    $html = $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->getContent();

    expect($html)->toContain('<h2>Welcome</h2>')
        ->toContain('<strong>bold</strong>')
        ->not->toContain('<script>alert(1)</script>')
        ->not->toContain('javascript:alert(1)')
        ->toContain('<a rel="nofollow noopener noreferrer" target="_blank" href="https://example.com">site</a>');
});

test('the description can have headings, code blocks and images, but no unsafe image', function () {
    $this->spot->update(['description' => "# One\n\n###### Six\n\n```php\necho 'hi';\n```\n\n![A boat](https://example.com/boat.jpg)\n\n![Trap](javascript:alert(1))"]);

    $html = $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->getContent();

    // The page has the first heading: the headings of the description start below it, each one under the previous
    expect($html)->toContain('<h2>One</h2>')
        ->toContain('<h3>Six</h3>')
        ->not->toContain('<h1>One</h1>')
        ->toContain("<pre><code class=\"language-php\">echo 'hi';")
        ->toContain('<img src="https://example.com/boat.jpg" alt="A boat" />')
        // The unsafe image keeps its description, without its address
        ->toContain('<img src="" alt="Trap" />')
        ->not->toContain('javascript:alert(1)');
});

test('the headings of the description are shifted under the title of the page, keeping their order', function () {
    $this->spot->update(['description' => "# Access\n\n## By train\n\n#### From the station\n\n# Opening hours"]);

    $html = $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->getContent();

    expect($html)->toContain('<h2>Access</h2>')
        ->toContain('<h3>By train</h3>')
        // A skipped level is closed up
        ->toContain('<h4>From the station</h4>')
        ->toContain('<h2>Opening hours</h2>')
        // The only first heading is the name of the spot
        ->and(substr_count($html, '<h1'))->toBe(1);
});

test('the page of a spot shows its location on a map, given in the page, and its coordinates', function () {
    $this->spot->update(['latitude' => 46.5197, 'longitude' => 6.6323]);

    $html = $this->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee('data-map="display"', false)
        ->assertSee('46.519700, 6.632300')
        ->getContent();

    preg_match('/data-map-spots="([^"]*)"/', $html, $spots);

    // One point, longitude first, without a link to the page itself
    expect(json_decode(html_entity_decode($spots[1]), true))->toBe([
        'type' => 'FeatureCollection',
        'features' => [[
            'type' => 'Feature',
            'geometry' => ['type' => 'Point', 'coordinates' => [6.6323, 46.5197]],
            'properties' => ['name' => 'Harbour'],
        ]],
    ]);
});

test('the page of a spot without a location has no map nor coordinates', function () {
    $this->spot->update(['latitude' => null, 'longitude' => null]);

    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee('Harbour')
        ->assertDontSee('data-map=', false)
        ->assertDontSee(__('ui.spots.show.fields.coordinates'));
});

test('the members are told when a spot is hidden from the public lists, the visitors are not', function () {
    $this->spot->update(['is_hidden' => true]);

    $note = e(__('ui.spots.show.manage.notes.hidden', ['organization' => $this->organization->name]));

    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee($note, false);
    $this->actingAs($this->member)->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertSee($note, false);
});

test('a spot whose creator is gone is still shown', function () {
    $this->member->delete();

    $this->actingAs($this->admin)->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee('Harbour')
        ->assertSee(__('ui.spots.show.manage.created', ['date' => $this->spot->created_at->isoFormat('LL')]));
});

test('a member creates a spot and becomes its creator', function () {
    $response = $this->actingAs($this->member)
        ->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData(['name' => 'Lighthouse']));

    $spot = Spot::firstWhere('name', 'Lighthouse');

    $response->assertRedirect(route('spots.show', ['spot' => $spot->uuid]))->assertSessionHas('success', __('ui.spots.feedback.spot_created'));
    expect($spot->organization->is($this->organization))->toBeTrue()
        ->and($spot->creator->is($this->member))->toBeTrue();
});

test('outsiders cannot create, update or delete spots', function () {
    $this->actingAs($this->outsider);

    $this->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData(['name' => 'Lighthouse']))
        ->assertSessionHasErrors(['organization' => __('ui.spots.common.forms.fields.organization.not_allowed')]);
    $this->patch(route('spots.update', $this->spot), ['name' => 'Renamed'])->assertForbidden();
    $this->delete(route('spots.destroy', $this->spot))->assertForbidden();

    expect(Spot::count())->toBe(1)->and($this->spot->fresh()->name)->toBe('Harbour');
});

test('spot names are validated and unique within the organization chosen in the form', function () {
    $this->actingAs($this->member);

    $this->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData(['name' => '']))->assertSessionHasErrors('name');
    $this->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData(['name' => 'A']))->assertSessionHasErrors('name');
    $this->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData(['name' => 'Harbour']))->assertSessionHasErrors('name');

    $this->post(route('spots.store'), ['organization' => $this->organization->uuid] + spotData(['name' => 'Lighthouse']))->assertSessionHasNoErrors();
    expect(Spot::firstWhere('name', 'Lighthouse')->organization->is($this->organization))->toBeTrue();

    // The same name in another organization, for the administrators, who create spots in all of them
    $this->actingAs($this->admin)
        ->post(route('spots.store'), ['organization' => $this->other->uuid] + spotData(['name' => 'Harbour']))
        ->assertSessionHasNoErrors();
});

test('the organization of a new spot is required, and one the user creates spots in', function () {
    $this->actingAs($this->member);

    $this->post(route('spots.store'), spotData())->assertSessionHasErrors('organization');
    $this->post(route('spots.store'), ['organization' => 'a320f6e6-e69d-439a-b787-4d4f6974ad60'] + spotData())->assertSessionHasErrors('organization');
    $this->post(route('spots.store'), ['organization' => $this->other->uuid] + spotData())
        ->assertSessionHasErrors(['organization' => __('ui.spots.common.forms.fields.organization.not_allowed')]);

    expect(Spot::where('name', 'Lighthouse')->exists())->toBeFalse();
});

test('a member renames a spot, keeping its creator', function () {
    $this->actingAs($this->admin)->patch(route('spots.update', $this->spot), ['name' => 'Harbour'])->assertSessionHasNoErrors();
    $this->patch(route('spots.update', $this->spot), ['name' => 'Marina'])
        ->assertRedirect(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertSessionHas('success', __('ui.spots.feedback.spot_updated'));

    expect($this->spot->fresh()->name)->toBe('Marina')
        ->and($this->spot->fresh()->created_by)->toBe($this->member->id);
});

test('the redirect address of a spot leads to its page, for everyone', function () {
    $this->get(route('spots.redirect', ['spot' => $this->spot->uuid]))
        ->assertRedirect(route('spots.show', ['spot' => $this->spot->uuid]));

    $this->get('/spots/unknown/redirect')->assertNotFound();
});

test('the page of a spot says it was just scanned, only when opened from its redirect address', function () {
    $this->followingRedirects()->get(route('spots.redirect', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee(__('ui.spots.show.scanned'));

    // Once: the next visit, as any other, does not
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee(__('ui.spots.show.scanned'));
});

test('a member deletes a spot', function () {
    $this->actingAs($this->member)
        ->delete(route('spots.destroy', $this->spot))
        ->assertRedirect(route('organizations.show', ['organization' => $this->organization->uuid]))
        ->assertSessionHas('success', __('ui.spots.feedback.spot_deleted'));

    expect(Spot::count())->toBe(0);
});

test('the edit page of a spot shows a trail starting at the spots, without the organization, the page of the spot has none', function () {
    $crumbs = function (string $html) {
        preg_match('/<nav aria-label="[^"]*" class="breadcrumbs[^"]*">(.*?)<\/nav>/s', $html, $nav);
        preg_match_all('/<(?:a|span)\b[^>]*>\s*([^<]+?)\s*<\/(?:a|span)>/', $nav[1], $labels);

        return $labels[1];
    };

    $this->actingAs($this->member);

    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('class="breadcrumbs', false);

    expect($crumbs($this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->getContent()))->toBe(['Spots', 'Harbour', 'Edit the spot']);
});
