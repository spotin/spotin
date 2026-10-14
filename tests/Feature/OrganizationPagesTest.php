<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
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
    OrganizationInvitation::factory()->for($this->organization)->create(['email' => 'pending@example.com']);
    $this->withUnencryptedCookie('locale', $locale);

    $this->actingAs($this->responsible)->get(route('organizations.index', ['mine' => 1]))->assertOk()->assertDontSee('ui.');

    foreach (['organizations.show', 'organizations.members.index', 'organizations.invitations.index', 'organizations.invitations.create'] as $route) {
        $this->get(($this->url)($route))->assertOk()->assertDontSee('ui.');
    }

    $this->get(($this->url)('organizations.invitations.index'))->assertSee(emailHtml('pending@example.com'), false);

    $this->actingAs($this->member)->get(($this->url)('organizations.show'))->assertOk()->assertDontSee('ui.');
    $this->get(($this->url)('organizations.members.index'))->assertOk()->assertDontSee('ui.');
})->with(['en', 'fr']);

test('users see only the organizations they belong to, as cards with their role', function () {
    $this->other->users()->attach($this->responsible);

    $content = $this->actingAs($this->responsible)->get(route('organizations.index', ['mine' => 1]))
        ->assertOk()
        ->assertSee('Acme')
        ->assertSee('Globex')
        ->assertSee(__('ui.organizations.common.forms.fields.role.options.responsible'))
        ->assertSee(__('ui.organizations.common.forms.fields.role.options.member'))
        ->getContent();

    // Each card is a single link to its organization, without a separate button or table.
    expect(substr_count($content, 'href="'.($this->url)('organizations.show').'"'))->toBe(1)
        ->and(substr_count($content, 'href="'.($this->url)('organizations.show', $this->other).'"'))->toBe(1)
        ->and($content)->toContain('class="card ')
        ->not->toContain('<table');

    $this->actingAs($this->member)->get(route('organizations.index', ['mine' => 1]))
        ->assertSee('Acme')
        ->assertDontSee('Globex');
});

test('long organization names cannot make the cards overflow', function () {
    $name = 'Association-Internationale-des-Amis-du-Patrimoine-Culturel-et-Naturel-de-la-Region-Nord-Ouest';
    $this->member->organizations()->attach(Organization::factory()->create(['name' => $name]));

    $content = $this->actingAs($this->member)->get(route('organizations.index', ['mine' => 1]))->assertOk()->getContent();

    // The single column of the grid must be able to shrink below its content on phones,
    // and the title wraps on at most two lines instead of staying on one unbreakable line.
    expect($content)->toContain('grid grid-cols-1 gap-4 sm:grid-cols-2')
        ->and($content)->toContain('line-clamp-2 break-words')
        ->and($content)->toContain('title="'.$name.'"')
        ->and($content)->not->toContain('truncate');
});

test('the organizations can be explored by anyone and filtered to the ones of the user', function () {
    $this->get('/organizations')->assertOk()->assertSee('Acme')->assertSee('Globex')
        ->assertDontSee('name="mine"', false)
        ->assertSee('name="query"', false);

    $this->get('/organizations?mine=1')->assertOk()->assertSee('Globex');

    $this->actingAs($this->member)->get('/organizations')->assertOk()->assertSee('Acme')->assertSee('Globex')
        ->assertSee('>'.__('ui.organizations.index.title').'</h1>', false)
        ->assertSee('name="mine"', false)
        ->assertSee(__('ui.organizations.common.forms.fields.role.options.member'));

    expect(mineIsChecked($this->get('/organizations')->getContent()))->toBeFalse()
        ->and(mineIsChecked($this->get('/organizations?mine=1')->getContent()))->toBeTrue();

    $this->get('/organizations?mine=1')->assertOk()
        ->assertSee('>'.__('ui.organizations.index.title').'</h1>', false)
        ->assertSee('<title>'.__('ui.organizations.index.title').' - Spot in</title>', false)
        ->assertSee(__('ui.organizations.index.filter.mine'))
        ->assertDontSee('Globex');
});

test('a user without organization sees an empty filtered list', function () {
    $this->actingAs(User::factory()->create())->get(route('organizations.index', ['mine' => 1]))
        ->assertOk()
        ->assertSee(__('ui.organizations.index.no-result'));
});

test('the organization page shows cards linking to the members, without the role', function () {
    OrganizationInvitation::factory()->for($this->organization)->create();

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
    OrganizationInvitation::factory()->for($this->organization)->count(2)->create();

    $this->actingAs($this->$user)->get(($this->url)('organizations.show'))
        ->assertOk()
        ->assertSee(trans_choice('ui.organizations.show.sections.invitations.summary', 2))
        ->assertSee(($this->url)('organizations.invitations.index'), false);
})->with(['responsible', 'admin']);

test('responsible users see the members with their emails and the management forms', function () {
    $content = $this->actingAs($this->responsible)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee('Rita Responsible')
        ->assertSee(emailHtml('mike@example.com'), false)
        ->getContent();

    $memberUrl = route('organizations.members.destroy', ['organization' => $this->organization->uuid, 'user' => $this->member->id]);
    $ownUrl = route('organizations.members.destroy', ['organization' => $this->organization->uuid, 'user' => $this->responsible->id]);

    // The role switch and the remove button of another member share a URL. One's own row has
    // neither, since they are the only responsible user and cannot remove themselves.
    expect(substr_count($content, $memberUrl))->toBe(2)
        ->and(substr_count($content, $ownUrl))->toBe(0);
});

test('managers change a role with a select in the Role column and a Save button in the Actions column', function () {
    $second = User::factory()->create(['name' => 'Sam Second']);
    $this->organization->users()->attach($second, ['role' => OrganizationRole::Responsible]);

    $content = $this->actingAs($this->responsible)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee(__('ui.organizations.members.index.table.actions.save'))
        ->getContent();

    // The select and its Save button are tied together by the id of the form, even though they sit in different cells.
    expect($content)->toMatch('/<select\s[^>]*form="role-'.$this->member->id.'"/')
        ->and($content)->toMatch('/<form\s+id="role-'.$this->member->id.'"/');

    // Every row of a member who can change role has both, with the same id.
    expect(substr_count($content, 'form="role-'.$this->member->id.'"'))->toBe(1)
        ->and(substr_count($content, 'id="role-'.$this->member->id.'"'))->toBe(1)
        ->and(substr_count($content, 'id="role-'.$second->id.'"'))->toBe(1);
});

test('the select of the only responsible user is disabled and has no Save button', function () {
    $content = $this->actingAs($this->admin)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->getContent();

    expect($content)->toMatch('/<select\s[^>]*form="role-'.$this->responsible->id.'"[^>]*disabled/s')
        ->and($content)->not->toContain('id="role-'.$this->responsible->id.'"')
        ->and($content)->toContain('id="role-'.$this->member->id.'"');
});

test('changing the role with the select and the Save button works end to end', function () {
    $this->actingAs($this->responsible)
        ->put(route('organizations.members.update', ['organization' => $this->organization->uuid, 'user' => $this->member->id]), ['role' => 'responsible'])
        ->assertRedirect();

    expect($this->member->isResponsibleOf($this->organization))->toBeTrue();
});

test('members see the names and roles only, without emails or management forms', function () {
    $this->actingAs($this->member)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee('Rita Responsible')
        ->assertSee(__('ui.organizations.common.forms.fields.role.options.responsible'))
        ->assertDontSee('rita@example.com')
        ->assertDontSee('name="role"', false);
});

test('admins can see and manage the members of any organization', function () {
    $this->actingAs($this->admin)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee(emailHtml('mike@example.com'), false)
        ->assertSee('name="role"', false);
});

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
        ->assertSee(route('spots.index', ['organization' => $this->organization->uuid]), false)
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

test('the invitations page lists the invitations and links to the invitation form', function () {
    OrganizationInvitation::factory()->for($this->organization)->create(['email' => 'pending@example.com']);
    OrganizationInvitation::factory()->for($this->organization)->expired()->create(['email' => 'old@example.com']);
    OrganizationInvitation::factory()->for($this->other)->create(['email' => 'elsewhere@example.com']);

    $this->actingAs($this->responsible)->get(($this->url)('organizations.invitations.index'))
        ->assertOk()
        ->assertSee(emailHtml('pending@example.com'), false)
        ->assertSee(emailHtml('old@example.com'), false)
        ->assertSee(__('ui.organizations.invitations.index.table.expired'))
        ->assertDontSee('elsewhere@example.com')
        ->assertSee(($this->url)('organizations.invitations.create'), false);
});

test('the invitations page tells when there is no invitation', function () {
    $this->actingAs($this->responsible)->get(($this->url)('organizations.invitations.index'))
        ->assertSee(__('ui.organizations.invitations.index.table.empty'));
});

test('the invitation form posts to the invitation route', function () {
    $this->actingAs($this->responsible)->get(($this->url)('organizations.invitations.create'))
        ->assertOk()
        ->assertSee(route('organizations.invitations.store', ['organization' => $this->organization->uuid]), false)
        ->assertSee('name="email"', false)
        ->assertSee('name="role"', false);
});

test('every member gets a Leave button on their own row and a Remove button on the others', function () {
    $leave = route('organizations.leave', ['organization' => $this->organization->uuid]);
    $remove = fn (User $user) => route('organizations.members.destroy', ['organization' => $this->organization->uuid, 'user' => $user->id]);

    // A plain member can only leave.
    $this->actingAs($this->member)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertSee('id="leave-organization-'.$this->organization->uuid.'-modal"', false)
        ->assertSee($leave, false)
        ->assertDontSee($remove($this->responsible), false);

    // A responsible user can also remove the others, but not themselves.
    $this->organization->users()->updateExistingPivot($this->member->id, ['role' => OrganizationRole::Responsible]);

    $this->actingAs($this->responsible)->get(($this->url)('organizations.members.index'))
        ->assertSee('id="leave-organization-'.$this->organization->uuid.'-modal"', false)
        ->assertSee($leave, false)
        ->assertSee($remove($this->member), false);
});

test('the last responsible user has a disabled Leave button and is told why', function () {
    $this->actingAs($this->responsible)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertDontSee('leave-organization-', false)
        ->assertDontSee(route('organizations.leave', ['organization' => $this->organization->uuid]), false)
        ->assertSee('aria-describedby="last-responsible-hint"', false)
        ->assertSee(__('ui.organizations.members.leave.last-responsible'));
});

test('admins who are not members have no Leave button', function () {
    $this->actingAs($this->admin)->get(($this->url)('organizations.members.index'))
        ->assertOk()
        ->assertDontSee('leave-organization-', false)
        ->assertDontSee(route('organizations.leave', ['organization' => $this->organization->uuid]), false);
});

test('the organization page no longer has a leave section', function () {
    $this->actingAs($this->member)->get(($this->url)('organizations.show'))
        ->assertOk()
        ->assertDontSee(route('organizations.leave', ['organization' => $this->organization->uuid]), false);
});

test('actions show their feedback and errors on the right page', function () {
    $this->actingAs($this->responsible);

    $this->followingRedirects()->from(($this->url)('organizations.members.index'))
        ->put(route('organizations.members.update', ['organization' => $this->organization->uuid, 'user' => $this->member->id]), ['role' => 'responsible'])
        ->assertSee(__('ui.organizations.members.feedback.member-role-updated'));

    $this->followingRedirects()->from(($this->url)('organizations.members.index'))
        ->delete(route('organizations.members.destroy', ['organization' => $this->organization->uuid, 'user' => $this->member->id]))
        ->assertSee(__('ui.organizations.members.feedback.member-removed'));

    $this->followingRedirects()->from(($this->url)('organizations.invitations.create'))
        ->post(route('organizations.invitations.store', ['organization' => $this->organization->uuid]), ['email' => 'new@example.com', 'role' => 'member'])
        ->assertSee(__('ui.organizations.invitations.feedback.invitation-sent'))
        ->assertSee(emailHtml('new@example.com'), false);

    $invitation = OrganizationInvitation::sole();

    $this->followingRedirects()->from(($this->url)('organizations.invitations.index'))
        ->delete(route('organizations.invitations.destroy', ['organization' => $this->organization->uuid, 'invitation' => $invitation->id]))
        ->assertSee(__('ui.organizations.invitations.feedback.invitation-revoked'));
});

test('inviting someone who already belongs shows the error on the invitation form', function () {
    $this->actingAs($this->responsible)
        ->followingRedirects()
        ->from(($this->url)('organizations.invitations.create'))
        ->post(route('organizations.invitations.store', ['organization' => $this->organization->uuid]), ['email' => 'mike@example.com', 'role' => 'member'])
        ->assertSee(__('ui.organizations.invitations.feedback.already-member'));
});

test('sending an invitation brings back to the list of invitations', function () {
    $this->actingAs($this->responsible)
        ->post(route('organizations.invitations.store', ['organization' => $this->organization->uuid]), ['email' => 'new@example.com', 'role' => 'member'])
        ->assertRedirect(($this->url)('organizations.invitations.index'));
});

test('leaving shows a confirmation on the list of organizations', function () {
    $this->actingAs($this->member)
        ->followingRedirects()
        ->delete(route('organizations.leave', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee(__('ui.organizations.members.feedback.organization-left'));
});

test('the navigation links to the public lists, and the list to each organization', function () {
    // Once in the mobile menu and once in the desktop menu.
    $content = $this->actingAs($this->member)->get(route('settings.profile'))->getContent();

    expect(substr_count($content, 'href="'.route('organizations.index').'"'))->toBe(2)
        ->and(substr_count($content, 'href="'.route('spots.index').'"'))->toBe(2)
        ->and($content)->not->toContain('mine=1');

    $this->actingAs($this->admin)->get(route('organizations.index'))->assertSee(($this->url)('organizations.show'), false);
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
    'invitations' => ['organizations.invitations.index', fn ($t) => OrganizationInvitation::factory()->for($t->organization)->count(30)->create()],
]);

test('the lists of organizations are paginated', function () {
    $user = User::factory()->create();
    $user->organizations()->attach(Organization::factory()->count(30)->create());
    Organization::factory()->count(30)->create();

    $this->actingAs($user)->get(route('organizations.index', ['mine' => 1]))->assertOk()->assertSee('page=2', false);
    $this->actingAs($this->admin)->get(route('organizations.index'))->assertOk()->assertSee('page=2', false);
});

test('the organizations can be searched by name, with a count and a way to reset', function () {
    $this->get('/organizations')->assertOk()
        ->assertSee(trans_choice('ui.organizations.index.count', 2, ['count' => 2]))
        ->assertDontSee(__('ui.common.filters.reset'));

    $this->get('/organizations?query=acm')->assertOk()
        ->assertSee('Acme')->assertDontSee('Globex')
        ->assertSee('value="acm"', false)
        ->assertSee(__('ui.common.filters.reset'));

    $this->get('/organizations?query=nothing-like-this')->assertOk()->assertSee(__('ui.organizations.index.no-result'));

    $this->actingAs($this->member)->get('/organizations?query=globex&mine=1')->assertOk()->assertSee(__('ui.organizations.index.no-result'));
});
