<?php

use App\Models\User;

test('the home page renders with its title and description', function () {
    $this->get(route('index'))
        ->assertOk()
        ->assertSee('<title>'.e(__('ui.homepage.title')).' - '.e(config('app.name')).'</title>', false)
        ->assertSee('<meta name="description" content="'.e(__('ui.homepage.description')).'">', false)
        ->assertSee(e(config('app.name')), false)
        ->assertSee('2021-'.date('Y'), false);
});

test('the home page shows all the spots on a map, and invites guests to explore the platform and to log in or register, in every language', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)->get(route('index'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('data-map-spots="'.route('spots.map').'"', false)
        ->assertSee(route('spots.index'), false)
        ->assertSee(route('organizations.index'), false)
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false)
        ->assertDontSee(route('dashboard'), false);
})->with(['en', 'fr']);

test('the home page sends logged-in users to their dashboard', function () {
    $this->actingAs(User::factory()->create())->get(route('index'))
        ->assertOk()
        ->assertSee(route('dashboard'), false)
        ->assertDontSee(route('register'), false);
});

test('the home page is only its hero, without an empty column under it', function () {
    $html = $this->get(route('index'))->assertOk()->getContent();

    expect($html)->toContain('class="hero ')
        ->not->toContain('<main');

    // The other pages keep their column
    $this->get(route('spots.index'))->assertOk()->assertSee('<main', false);
});
