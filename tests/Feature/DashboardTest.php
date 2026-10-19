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

test('the dashboard shows the organizations and the spots of the user, with pending invitations', function (string $locale) {
    $user = User::factory()->create(['email' => 'jane@example.com']);
    $mine = Organization::factory()->create(['name' => 'Acme']);
    $other = Organization::factory()->create(['name' => 'Globex']);
    $mine->users()->attach($user, ['role' => OrganizationRole::Responsible]);

    Spot::factory()->for($mine)->create(['name' => 'Harbour', 'created_by' => $user->id]);
    Spot::factory()->for($mine)->create(['name' => 'Colleague Spot']);
    Spot::factory()->for($other)->create(['name' => 'Foreign Spot']);
    Invitation::factory()->for($other)->create(['email' => 'jane@example.com']);

    $this->withUnencryptedCookie('locale', $locale)->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('Acme')
        ->assertSee(__('ui.organizations.common.forms.fields.role.options.responsible'))
        ->assertDontSee('Globex')
        ->assertSee('Harbour')
        ->assertDontSee('Colleague Spot')
        ->assertDontSee('Foreign Spot')
        ->assertSee(__('ui.invitations.common.alert.action'))
        ->assertSee(route('organizations.index', ['mine' => 1]), false)
        ->assertSee(route('spots.index', ['mine' => 1]), false);
})->with(['en', 'fr']);

test('the dashboard of a new user explains how to get started', function () {
    $this->actingAs(User::factory()->create())->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('ui.dashboard.organizations.empty'))
        ->assertSee(__('ui.dashboard.spots.empty'))
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
