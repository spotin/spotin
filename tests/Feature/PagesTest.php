<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\Spot;
use App\Models\User;

test('the home page renders with its title and description', function () {
    $this->get(route('index'))
        ->assertOk()
        ->assertSee('<title>'.e(__('ui.homepage.title')).' - '.e(config('app.name')).'</title>', false)
        ->assertSee('<meta name="description" content="'.e(__('ui.homepage.description')).'">', false)
        ->assertSee(e(config('app.name')).'<sup>®</sup>', false);
});

test('an unknown page returns a not found response', function () {
    $this->get('/this-page-does-not-exist')->assertNotFound();
});

test('guests are redirected to the login page from protected pages', function (string $url) {
    $this->get($url)->assertRedirect(route('login'));
})->with([
    'dashboard' => '/dashboard',
    'profile settings' => '/settings/profile',
    'security settings' => '/settings/security',
    'email verification notice' => '/auth/verify-email',
    'password confirmation' => '/auth/confirm-password',
]);

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

test('authentication pages are served at their Fortify paths only', function (string $url) {
    $this->get($url)->assertNotFound();
})->with([
    'reset password without a token' => '/auth/reset-password',
    'default verification notice path' => '/auth/email/verify',
    'default password confirmation path' => '/auth/user/confirm-password',
]);

test('the home page invites guests to explore the platform and to log in or register, in every language', function (string $locale) {
    Spot::factory()->for(Organization::factory()->create(['name' => 'Acme']))->create(['name' => 'Harbour']);

    $this->withUnencryptedCookie('locale', $locale)->get(route('index'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee(route('spots.index'), false)
        ->assertSee(route('organizations.index'), false)
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false)
        ->assertSee(__('ui.homepage.latest-spots'))
        ->assertSee('Harbour')
        ->assertSee('Acme')
        ->assertDontSee(route('dashboard'), false);
})->with(['en', 'fr']);

test('the home page says so when there is no spot yet, in every language', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)->get(route('index'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee(__('ui.homepage.latest-spots-empty'));
})->with(['en', 'fr']);

test('the home page sends logged-in users to their dashboard', function () {
    $this->actingAs(User::factory()->create())->get(route('index'))
        ->assertOk()
        ->assertSee(route('dashboard'), false)
        ->assertDontSee(route('register'), false);
});

test('the dashboard shows the organizations and the spots of the user, with pending invitations', function (string $locale) {
    $user = User::factory()->create(['email' => 'jane@example.com']);
    $mine = Organization::factory()->create(['name' => 'Acme']);
    $other = Organization::factory()->create(['name' => 'Globex']);
    $mine->users()->attach($user, ['role' => OrganizationRole::Responsible]);

    Spot::factory()->for($mine)->create(['name' => 'Harbour', 'created_by' => $user->id]);
    Spot::factory()->for($mine)->create(['name' => 'Colleague Spot']);
    Spot::factory()->for($other)->create(['name' => 'Foreign Spot']);
    OrganizationInvitation::factory()->for($other)->create(['email' => 'jane@example.com']);

    $this->withUnencryptedCookie('locale', $locale)->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('Acme')
        ->assertSee(__('ui.organizations.common.forms.fields.role.options.responsible'))
        ->assertDontSee('Globex')
        ->assertSee('Harbour')
        ->assertDontSee('Colleague Spot')
        ->assertDontSee('Foreign Spot')
        ->assertSee(route('organizations.invitations.pending'), false)
        ->assertSee(route('organizations.index', ['mine' => 1]), false)
        ->assertSee(route('spots.index', ['mine' => 1]), false);
})->with(['en', 'fr']);

test('the dashboard of a new user explains how to get started', function () {
    $this->actingAs(User::factory()->create())->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('ui.dashboard.organizations.empty'))
        ->assertSee(__('ui.dashboard.spots.empty'))
        ->assertDontSee(route('organizations.invitations.pending'), false)
        ->assertDontSee(route('spots.create'), false);
});
