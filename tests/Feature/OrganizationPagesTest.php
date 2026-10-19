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

test('the organization page shows cards linking to the members, without the role', function () {
    Invitation::factory()->for($this->organization)->create();

    $content = $this->actingAs($this->member)->get(($this->url)('organizations.show'))
        ->assertOk()
        ->assertSee('Acme')
        ->assertSee(trans_choice('ui.organizations.show.sections.members.summary', 2))
        ->assertDontSee('Your role')
        ->assertDontSee(($this->url)('organizations.invitations.index'), false)
        ->getContent();

    // The whole card is the link, there is no separate button.
    expect(substr_count($content, 'href="'.($this->url)('organizations.members.index').'"'))->toBe(1)
        ->and($content)->toContain('class="card ')
        ->not->toContain('btn btn-sm');
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
        ->assertSee('<title>Acme - Spot in</title>', false)
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
