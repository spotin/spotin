<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

test('unverified users are redirected to the verification notice from the dashboard', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('verification.notice'));
});

test('verified users can see the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertSee(__('ui.dashboard.title'));
});

test('the dashboard renders in every language, with the pending invitations, and leaves the spots and organizations to their lists', function (string $locale) {
    $user = User::factory()->create(['email' => 'jane@example.com']);
    $mine = Organization::factory()->create(['name' => 'Acme']);
    $other = Organization::factory()->create(['name' => 'Globex']);
    $mine->users()->attach($user, ['role' => OrganizationRole::Responsible]);

    Spot::factory()->for($mine)->create(['name' => 'Harbour', 'created_by' => $user->id]);
    Invitation::factory()->for($other)->create(['email' => 'jane@example.com']);

    $this->withUnencryptedCookie('locale', $locale)->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee(__('ui.invitations.common.alert.action'))
        ->assertDontSee('Harbour')
        ->assertDontSee('id="dashboard-spots"', false)
        ->assertDontSee('id="dashboard-organizations"', false);
})->with(['en', 'fr']);

test('the dashboard counts the spots and the organizations of the user, each leading to its list', function () {
    $user = User::factory()->create();
    $acme = Organization::factory()->create();
    $globex = Organization::factory()->create();
    $acme->users()->attach($user, ['role' => OrganizationRole::Member]);
    $globex->users()->attach($user, ['role' => OrganizationRole::Responsible]);

    // All the spots of their organizations, hidden ones included, whoever created them; not the ones of the others
    Spot::factory()->for($acme)->count(2)->create();
    Spot::factory()->for($globex)->hidden()->create();
    Spot::factory()->for(Organization::factory())->count(4)->create();

    $stat = fn (string $route, int $count) => '/<a href="'.preg_quote(route($route), '/').'" class="stat[^"]*">.*?<div class="stat-value[^"]*">'.$count.'<\/div>/s';

    $html = $this->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

    expect($html)->toMatch($stat('spots.index', 3))
        ->toMatch($stat('organizations.index', 2));
});

test('the dashboard of a new user explains how to get started', function () {
    $this->actingAs(User::factory()->create())->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('ui.dashboard.getting_started.title'))
        ->assertSeeInOrder([__('ui.dashboard.statistics.spots.title'), '>0<', __('ui.dashboard.statistics.organizations.title'), '>0<'], false)
        ->assertDontSee(__('ui.invitations.common.alert.action'));
});

test('the dashboard points to the pending invitations only when there are some', function () {
    $jane = User::factory()->create(['email' => 'jane@example.com']);

    $this->actingAs($jane)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('alert alert-info', false)
        ->assertDontSee(__('ui.invitations.common.alert.action'));

    Invitation::factory()->for(Organization::factory()->create())->count(2)->create(['email' => 'jane@example.com']);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('alert alert-info', false)
        ->assertSee(__('ui.invitations.common.alert.action'))
        ->assertSee(trans_choice('ui.invitations.common.alert.message', 2, ['count' => 2]));
});

test('the dashboard offers quick actions: a new spot to the members, and also a new organization to the administrators', function () {
    $member = User::factory()->create();
    Organization::factory()->create()->users()->attach($member, ['role' => OrganizationRole::Member]);

    $spot = 'href="'.route('spots.create').'"';
    $organization = 'href="'.route('organizations.create').'"';

    $this->actingAs($member)->get(route('dashboard'))->assertOk()
        ->assertSee(__('ui.dashboard.quick_actions.title'))
        ->assertSeeInOrder([$spot, __('ui.dashboard.quick_actions.spot.title'), __('ui.dashboard.quick_actions.spot.description')], false)
        ->assertDontSee($organization, false);

    $this->actingAs(User::factory()->admin()->create())->get(route('dashboard'))->assertOk()
        ->assertSeeInOrder([
            $organization, __('ui.dashboard.quick_actions.organization.title'), e(__('ui.dashboard.quick_actions.organization.description')),
            $spot, __('ui.dashboard.quick_actions.spot.title'),
        ], false);

    // Without organization, there is nothing to create yet
    $this->actingAs(User::factory()->create())->get(route('dashboard'))->assertOk()
        ->assertDontSee(__('ui.dashboard.quick_actions.title'))
        ->assertDontSee($spot, false);
});

test('the dashboard shows the overview, then the quick actions, each under its heading', function () {
    $member = User::factory()->create();
    Organization::factory()->create()->users()->attach($member, ['role' => OrganizationRole::Member]);

    $this->actingAs($member)->get(route('dashboard'))->assertOk()->assertSeeInOrder([
        __('ui.dashboard.getting_started.title'),
        '<h2 id="overview">'.e(__('ui.dashboard.overview')).'</h2>',
        'class="not-prose stats',
        '<h2 id="quick-actions">'.e(__('ui.dashboard.quick_actions.title')).'</h2>',
        route('spots.create'),
    ], false);
});

test('the quick actions and the cards of the lists are the same link cards, the actions in the primary color', function () {
    $member = User::factory()->create();
    $acme = Organization::factory()->create(['name' => 'Acme']);
    $acme->users()->attach($member, ['role' => OrganizationRole::Member]);
    Spot::factory()->for($acme)->create(['name' => 'Harbour']);

    $card = fn (string $url, string $circle, string $title) => '/<a\s+href="'.preg_quote($url, '/').'"\s+class="card card-border card-side[^"]*"\s*>\s*'
        .'<div\s+class="ml-4 shrink-0 rounded-full p-3 '.preg_quote($circle, '/').'"\s*>.*?'.preg_quote($title, '/').'.*?<\/a>/s';

    $this->actingAs($member);

    expect($this->get(route('dashboard'))->getContent())
        ->toMatch($card(route('spots.create'), 'bg-primary text-primary-content', __('ui.dashboard.quick_actions.spot.title')))
        ->and($this->get(route('spots.index'))->getContent())
        ->toMatch($card(route('spots.show', ['spot' => Spot::firstWhere('name', 'Harbour')->uuid]), 'bg-base-200', 'Harbour'))
        ->and($this->get(route('organizations.index'))->getContent())
        ->toMatch($card(route('organizations.show', ['organization' => $acme->uuid]), 'bg-base-200', 'Acme'));
});
