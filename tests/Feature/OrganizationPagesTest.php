<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

beforeEach(function () {
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
    $this->other = Organization::factory()->create(['name' => 'Globex']);

    $this->admin = User::factory()->admin()->create();
    $this->responsible = User::factory()->create(['name' => 'Rita Responsible', 'email' => 'rita@example.com']);
    $this->member = User::factory()->create(['name' => 'Mike Member', 'email' => 'mike@example.com']);
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->responsible, ['role' => OrganizationRole::Responsible]);
    $this->organization->users()->attach($this->member);
    $this->other->users()->attach($this->outsider);

    $this->url = fn (string $route, ?Organization $organization = null) => route($route, ['organization' => ($organization ?? $this->organization)->uuid]);
});

test('the pages render in every language without untranslated keys', function (string $locale) {
    Invitation::factory()->for($this->organization)->create(['email' => 'pending@example.com']);
    $this->withUnencryptedCookie('locale', $locale);

    $this->actingAs($this->responsible)->get(route('organizations.index', ['mine' => 1]))->assertOk()->assertDontSee('ui.');

    foreach (['organizations.show', 'organizations.members.index', 'organizations.invitations.index', 'organizations.invitations.create'] as $route) {
        $this->get(($this->url)($route))->assertOk()->assertDontSee('ui.');
    }

    $this->get(($this->url)('organizations.invitations.index'))->assertSee(emailHtml('pending@example.com'), false);

    $this->actingAs($this->member)->get(($this->url)('organizations.show'))->assertOk()->assertDontSee('ui.');
    $this->get(($this->url)('organizations.members.index'))->assertOk()->assertDontSee('ui.');
})->with(['en', 'fr']);

test('the organization page links to its members in its details, without the role', function () {
    Invitation::factory()->for($this->organization)->create();

    $content = $this->actingAs($this->member)->get(($this->url)('organizations.show'))
        ->assertOk()
        ->assertSee('Acme')
        ->assertSee(trans_choice('ui.organizations.show.sections.members.summary', 2))
        ->assertDontSee('Your role')
        ->assertDontSee(($this->url)('organizations.invitations.index'), false)
        ->getContent();

    // A single link, in the details: there is no separate button.
    expect(substr_count($content, 'href="'.($this->url)('organizations.members.index').'"'))->toBe(1)
        ->and($content)->not->toContain('btn btn-sm');
});

test('responsible users and admins also get a link to the invitations', function (string $user) {
    Invitation::factory()->for($this->organization)->count(2)->create();

    $this->actingAs($this->$user)->get(($this->url)('organizations.show'))
        ->assertOk()
        ->assertSee(trans_choice('ui.organizations.show.sections.invitations.summary', 2))
        ->assertSee(($this->url)('organizations.invitations.index'), false);
})->with(['responsible', 'admin']);

test('only responsible users and admins can reach the invitation pages', function () {
    foreach (['organizations.invitations.index', 'organizations.invitations.create'] as $route) {
        $this->actingAs($this->responsible)->get(($this->url)($route))->assertOk();
        $this->actingAs($this->admin)->get(($this->url)($route))->assertOk();
        $this->actingAs($this->member)->get(($this->url)($route))->assertForbidden();
        $this->actingAs($this->outsider)->get(($this->url)($route))->assertForbidden();
    }
});

test('outsiders and guests cannot see the members or the invitations of an organization', function (string $route) {
    $this->actingAs($this->outsider)->get(($this->url)($route))->assertForbidden();

    auth()->logout();
    $this->get(($this->url)($route))->assertRedirect(route('login'));
})->with(['organizations.members.index', 'organizations.invitations.index', 'organizations.invitations.create']);

test('everyone, guests included, can discover the organizations and see an organization with its spots', function (string $locale) {
    Spot::factory()->for($this->organization)->create(['name' => 'Harbour']);
    $this->withUnencryptedCookie('locale', $locale);

    $this->get('/organizations')->assertOk()->assertDontSee('ui.')->assertSee('Acme')->assertSee('Globex');

    $this->get(($this->url)('organizations.show'))
        ->assertOk()
        ->assertSee('<title>Acme - Spot in®</title>', false)
        ->assertSee('</footer>', false)
        ->assertDontSee('ui.')
        ->assertSee(route('organizations.spots.index', ['organization' => $this->organization->uuid]), false)
        ->assertDontSee(($this->url)('organizations.members.index'), false)
        ->assertDontSee(($this->url)('organizations.invitations.index'), false);

    $this->get(route('spots.index', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee('ui.')->assertSee('Harbour');

    $this->actingAs($this->outsider)->get(($this->url)('organizations.show'))->assertOk()
        ->assertDontSee(($this->url)('organizations.members.index'), false);
})->with(['en', 'fr']);

test('the members see the members card of their organization', function () {
    $this->actingAs($this->member)->get(($this->url)('organizations.show'))
        ->assertOk()
        ->assertSee(($this->url)('organizations.members.index'), false);
});

test('an unknown organization is not found', function () {
    $this->actingAs($this->admin);

    foreach (['/organizations/nope', '/organizations/nope/members', '/organizations/nope/invitations', '/organizations/nope/invitations/create'] as $url) {
        $this->get($url)->assertNotFound();
    }
});

test('actions show their feedback and errors on the right page', function () {
    $this->actingAs($this->responsible);

    $this->followingRedirects()->from(($this->url)('organizations.members.index'))
        ->put(route('organizations.members.update', ['organization' => $this->organization->uuid, 'user' => $this->member->id]), ['role' => 'responsible'])
        ->assertSee(__('ui.organizations.members.feedback.member_role_updated'));

    $this->followingRedirects()->from(($this->url)('organizations.members.index'))
        ->delete(route('organizations.members.destroy', ['organization' => $this->organization->uuid, 'user' => $this->member->id]))
        ->assertSee(__('ui.organizations.members.feedback.member_removed'));

    $this->followingRedirects()->from(($this->url)('organizations.invitations.create'))
        ->post(route('organizations.invitations.store', ['organization' => $this->organization->uuid]), ['email' => 'new@example.com', 'role' => 'member'])
        ->assertSee(__('ui.organizations.invitations.feedback.invitation_sent'))
        ->assertSee(emailHtml('new@example.com'), false);

    $invitation = Invitation::sole();

    $this->followingRedirects()->from(($this->url)('organizations.invitations.index'))
        ->delete(route('organizations.invitations.destroy', ['organization' => $this->organization->uuid, 'invitation' => $invitation->id]))
        ->assertSee(__('ui.organizations.invitations.feedback.invitation_revoked'));
});

test('the lists are paginated with page numbers and equal-width Previous and Next buttons', function (string $route, Closure $seed) {
    $seed($this);

    $this->actingAs($this->admin);

    // First page: Previous is disabled, Next links to the second page, the current page is marked.
    $first = $this->get(($this->url)($route))->assertOk()->getContent();

    expect($first)->toContain('rel="next"')
        ->and($first)->not->toContain('rel="prev"')
        ->and($first)->toContain('page=2')
        ->and($first)->toMatch('/aria-current="page">\s*1\s*</')
        ->and($first)->toContain(__('ui.common.pagination.page', ['current' => 1, 'last' => 2]))
        ->and(substr_count($first, 'w-32 shrink-0'))->toBe(2);

    // Last page: Previous links back, Next is disabled, the current page is marked.
    $last = $this->get(($this->url)($route).'?page=2')->assertOk()->getContent();

    expect($last)->toContain('rel="prev"')
        ->and($last)->not->toContain('rel="next"')
        ->and($last)->toMatch('/aria-current="page">\s*2\s*</')
        ->and($last)->toContain(__('ui.common.pagination.page', ['current' => 2, 'last' => 2]))
        ->and(substr_count($last, 'w-32 shrink-0'))->toBe(2);
})->with([
    'members' => ['organizations.members.index', fn ($t) => $t->organization->users()->attach(User::factory()->count(30)->create())],
    'invitations' => ['organizations.invitations.index', fn ($t) => Invitation::factory()->for($t->organization)->count(30)->create()],
]);

test('the redirect address of an organization leads to its page, which says it was just scanned, once', function () {
    $this->get(route('organizations.redirect', ['organization' => $this->organization->uuid]))
        ->assertRedirect(route('organizations.show', ['organization' => $this->organization->uuid]));

    $this->followingRedirects()->get(route('organizations.redirect', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee(__('ui.organizations.show.scanned'));

    $this->get(route('organizations.show', ['organization' => $this->organization->uuid]))->assertOk()->assertDontSee(__('ui.organizations.show.scanned'));
    $this->get('/organizations/unknown/redirect')->assertNotFound();
});

test('the number of spots of an organization leaves out the hidden spots, except for its members', function () {
    Spot::factory()->for($this->organization)->count(2)->create();
    Spot::factory()->for($this->organization)->create(['is_hidden' => true]);

    $this->get(route('organizations.show', ['organization' => $this->organization->uuid]))
        ->assertSee(trans_choice('ui.organizations.show.sections.spots.summary', 2));

    $this->actingAs($this->member)->get(route('organizations.show', ['organization' => $this->organization->uuid]))
        ->assertSee(trans_choice('ui.organizations.show.sections.spots.summary', 3));
});

test('the spots of an organization are given as GeoJSON for its map, without the hidden ones for the others than its members', function () {
    $lighthouse = Spot::factory()->for($this->organization)->create(['name' => 'Lighthouse', 'latitude' => 46.5, 'longitude' => 6.6]);
    Spot::factory()->for($this->organization)->create(['name' => 'Harbour']);
    Spot::factory()->for($this->organization)->create(['name' => 'Secret cove', 'is_hidden' => true]);
    Spot::factory()->for($this->other)->create(['name' => 'Elsewhere']);

    $url = route('organizations.spots.map', ['organization' => $this->organization->uuid]);
    $names = fn ($response) => collect($response->json('features'))->pluck('properties.name')->sort()->values()->all();

    $response = $this->getJson($url)->assertOk()->assertJsonPath('type', 'FeatureCollection');

    expect($names($response))->toBe(['Harbour', 'Lighthouse'])
        ->and(collect($response->json('features'))->firstWhere('properties.name', 'Lighthouse'))->toBe([
            'type' => 'Feature',
            'geometry' => ['type' => 'Point', 'coordinates' => [6.6, 46.5]],
            'properties' => ['name' => 'Lighthouse', 'url' => route('spots.show', ['spot' => $lighthouse->uuid])],
        ]);

    // As in the lists: its members find the hidden spots, and the search applies
    expect($names($this->actingAs($this->member)->getJson($url)))->toBe(['Harbour', 'Lighthouse', 'Secret cove'])
        ->and($names($this->getJson($url.'?query=light')))->toBe(['Lighthouse']);

    $this->getJson('/organizations/unknown/spots.geojson')->assertNotFound();
});

test('the page of an organization shows the map of its spots across the page, then its details and the actions of its editors', function () {
    $url = route('organizations.show', ['organization' => $this->organization->uuid]);

    // Without spots, there is no map: the details are in the page
    $this->get($url)->assertOk()->assertDontSee('data-map-spots', false)->assertSeeInOrder(['<main', '<article', 'Acme'], false);

    Spot::factory()->for($this->organization)->count(2)->create();
    $map = 'data-map-spots="'.route('organizations.spots.map', ['organization' => $this->organization->uuid]).'"';

    $this->get($url)->assertOk()
        ->assertSeeInOrder(['class="hero ', $map, '<main', '<article', '<h1', 'Acme', trans_choice('ui.organizations.show.sections.spots.summary', 2), $this->organization->created_at->isoFormat('LL')], false)
        ->assertDontSee('hero-content', false)
        // Everyone sees the actions to like, report and share it; the guests see neither the number of members nor the
        // actions of the editors
        ->assertSeeInOrder(['<footer', __('ui.common.actions.like'), __('ui.common.actions.report'), __('ui.common.actions.share')], false)
        ->assertDontSee(trans_choice('ui.organizations.show.sections.members.summary', 2))
        ->assertDontSee(route('organizations.edit', ['organization' => $this->organization->uuid]), false);

    $this->actingAs($this->member)->get($url)
        ->assertSee(trans_choice('ui.organizations.show.sections.members.summary', 2))
        ->assertDontSee(route('organizations.edit', ['organization' => $this->organization->uuid]), false);

    $this->actingAs($this->admin)->get($url)
        ->assertSeeInOrder(['class="hero ', '<footer', route('organizations.edit', ['organization' => $this->organization->uuid]), __('ui.organizations.show.actions.delete')], false);
});

test('the details of an organization lead to its spots, and to its members and invitations for those who can see them', function () {
    $url = route('organizations.show', ['organization' => $this->organization->uuid]);
    $spots = route('organizations.spots.index', ['organization' => $this->organization->uuid]);
    $members = route('organizations.members.index', ['organization' => $this->organization->uuid]);
    $invitations = route('organizations.invitations.index', ['organization' => $this->organization->uuid]);

    $this->get($url)->assertOk()->assertSee('href="'.$spots.'"', false)
        ->assertDontSee('href="'.$members.'"', false)
        ->assertDontSee('href="'.$invitations.'"', false);

    $this->actingAs($this->member)->get($url)->assertSee('href="'.$members.'"', false)->assertDontSee('href="'.$invitations.'"', false);

    $this->actingAs($this->responsible)->get($url)
        ->assertSeeInOrder(['<h1', 'href="'.$spots.'"', 'href="'.$members.'"', 'href="'.$invitations.'"', '</header>'], false);
});
