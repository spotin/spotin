<?php

use App\Enums\OrganizationRole;
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

test('users see only the organizations they belong to, as cards with their role', function () {
    $this->other->users()->attach($this->responsible);

    $content = $this->actingAs($this->responsible)->get(route('organizations.index'))
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

    $this->actingAs($this->member)->get(route('organizations.index'))
        ->assertSee('Acme')
        ->assertDontSee('Globex');
});

test('long organization names cannot make the cards overflow', function () {
    $name = 'Association-Internationale-des-Amis-du-Patrimoine-Culturel-et-Naturel-de-la-Region-Nord-Ouest';
    $this->member->organizations()->attach(Organization::factory()->create(['name' => $name]));

    $content = $this->actingAs($this->member)->get(route('organizations.index'))->assertOk()->getContent();

    // The single column of the grid must be able to shrink below its content on phones,
    // and the title wraps on at most two lines instead of staying on one unbreakable line.
    expect($content)->toContain('grid grid-cols-1 gap-4 sm:grid-cols-2')
        ->and($content)->toContain('line-clamp-2 break-words')
        ->and($content)->toContain('title="'.$name.'"')
        ->and($content)->not->toContain('truncate');
});

test('the list of the organizations is for the logged-in users, and can be filtered to the ones they can manage', function () {
    $this->get('/organizations')->assertRedirect(route('login'));

    $this->other->users()->attach($this->responsible, ['role' => OrganizationRole::Member]);

    $this->actingAs($this->responsible)->get('/organizations')->assertOk()->assertSee('Acme')->assertSee('Globex')
        ->assertSee('>'.__('ui.organizations.index.title').'</h1>', false)
        ->assertSee('<title>'.__('ui.organizations.index.title').' - Spot in®</title>', false)
        ->assertSee(__('ui.organizations.index.filter.mine'));

    expect(filterIsChecked($this->get('/organizations')->getContent()))->toBeFalse()
        ->and(filterIsChecked($this->get('/organizations?mine=1')->getContent()))->toBeTrue();

    $this->get('/organizations?mine=1')->assertOk()->assertSee('Acme')->assertDontSee('Globex');
    $this->get('/organizations?mine=true')->assertOk()->assertSee('Acme')->assertDontSee('Globex');
    $this->get('/organizations?mine=false')->assertOk()->assertSee('Globex');

    $this->actingAs($this->member)->get('/organizations?mine=1')->assertOk()->assertSee(__('ui.organizations.index.no_result'));

    // Administrators can manage all of them
    $this->actingAs($this->admin)->get('/organizations?mine=1')->assertOk()->assertSee('Acme')->assertSee('Globex');
});

test('the list of the organizations shows the ones of the user, all of them to administrators', function () {
    $this->actingAs($this->member)->get('/organizations')->assertOk()->assertSee('Acme')->assertDontSee('Globex');
    $this->actingAs($this->outsider)->get('/organizations')->assertOk()->assertSee('Globex')->assertDontSee('Acme');
    $this->actingAs($this->admin)->get('/organizations')->assertOk()->assertSee('Acme')->assertSee('Globex');
});

test('a user without organization sees an empty list', function () {
    $this->actingAs(User::factory()->create())->get(route('organizations.index'))
        ->assertOk()
        ->assertSee(__('ui.organizations.index.empty'));
});

test('leaving shows a confirmation on the list of organizations', function () {
    $this->actingAs($this->member)
        ->followingRedirects()
        ->delete(route('organizations.leave', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee(__('ui.organizations.members.feedback.organization_left'));
});

test('the navigation links to the public lists, and the list to each organization', function () {
    // Once in the mobile menu and once in the desktop menu.
    $content = $this->actingAs($this->member)->get(route('settings.profile'))->getContent();

    expect(substr_count($content, 'href="'.route('organizations.index').'"'))->toBe(2)
        ->and(substr_count($content, 'href="'.route('spots.index').'"'))->toBe(2)
        ->and($content)->not->toContain('mine=1');

    $this->actingAs($this->admin)->get(route('organizations.index'))->assertSee(($this->url)('organizations.show'), false);
});

test('the lists of organizations are paginated', function () {
    $user = User::factory()->create();
    $user->organizations()->attach(Organization::factory()->count(30)->create());
    Organization::factory()->count(30)->create();

    $this->actingAs($user)->get(route('organizations.index'))->assertOk()->assertSee('page=2', false);
    $this->actingAs($this->admin)->get(route('organizations.index'))->assertOk()->assertSee('page=2', false);
});

test('the organizations can be searched by name, with a count and a way to reset', function () {
    $this->actingAs($this->admin);

    $this->get('/organizations')->assertOk()
        ->assertSee(trans_choice('ui.organizations.index.count', 2, ['count' => 2]))
        ->assertDontSee(__('ui.common.filters.reset'));

    $this->get('/organizations?query=acm')->assertOk()
        ->assertSee('Acme')->assertDontSee('Globex')
        ->assertSee('value="acm"', false)
        ->assertSee(__('ui.common.filters.reset'));

    $this->get('/organizations?query=nothing-like-this')->assertOk()->assertSee(__('ui.organizations.index.no_result'));

    $this->actingAs($this->member)->get('/organizations?query=globex')->assertOk()->assertSee(__('ui.organizations.index.no_result'));
});

test('the number of spots of the organizations in the list and the API leaves out the hidden spots, except for their members', function () {
    Spot::factory()->for($this->organization)->count(2)->create();
    Spot::factory()->for($this->organization)->create(['is_hidden' => true]);

    $this->actingAs($this->member)->get(route('organizations.index'))->assertSee(trans_choice('ui.organizations.common.spots', 3));

    expect(Organization::withSpotsCount(null)->find($this->organization->id)->spots_count)->toBe(2)
        ->and(Organization::withSpotsCount($this->outsider)->find($this->organization->id)->spots_count)->toBe(2)
        ->and(Organization::listed($this->member)->firstWhere('id', $this->organization->id)->spots_count)->toBe(3);
});
