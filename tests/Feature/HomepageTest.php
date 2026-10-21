<?php

use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

test('the home page renders with its title and description', function () {
    $this->get(route('index'))
        ->assertOk()
        ->assertSee('<title>'.e(__('ui.homepage.title')).' - '.e(config('app.name')).'</title>', false)
        ->assertSee('<meta name="description" content="'.e(__('ui.homepage.description')).'">', false)
        ->assertSee(e(config('app.name')), false)
        ->assertSee('2021-'.date('Y'), false);
});

test('the home page invites guests to explore the platform and to log in or register, in every language', function (string $locale) {
    Spot::factory()->for(Organization::factory()->create(['name' => 'Acme']))->create(['name' => 'Harbour']);

    $this->withUnencryptedCookie('locale', $locale)->get(route('index'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee(route('spots.index'), false)
        ->assertSee(route('organizations.index'), false)
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false)
        ->assertSee(__('ui.homepage.latest_spots'))
        ->assertSee('Harbour')
        ->assertSee('Acme')
        ->assertDontSee(route('dashboard'), false);
})->with(['en', 'fr']);

test('the home page says so when there is no spot yet, in every language', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)->get(route('index'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee(__('ui.homepage.latest_spots_empty'));
})->with(['en', 'fr']);

test('the home page sends logged-in users to their dashboard', function () {
    $this->actingAs(User::factory()->create())->get(route('index'))
        ->assertOk()
        ->assertSee(route('dashboard'), false)
        ->assertDontSee(route('register'), false);
});

test('the latest spots of the home page leave out the hidden spots, except for the members of their organization', function () {
    $organization = Organization::factory()->create();
    Spot::factory()->for($organization)->create(['name' => 'Harbour']);
    Spot::factory()->for($organization)->create(['name' => 'Secret cove', 'is_hidden' => true]);

    $this->get(route('index'))->assertOk()->assertSee('Harbour')->assertDontSee('Secret cove');
    $this->actingAs(User::factory()->create())->get(route('index'))->assertDontSee('Secret cove');

    $member = User::factory()->create();
    $organization->users()->attach($member);

    $this->actingAs($member)->get(route('index'))->assertSee('Secret cove');
});
