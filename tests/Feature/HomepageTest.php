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

test('the home page shows all the spots on a map, without linking guests to the lists of the members, in every language', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)->get(route('index'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('data-map-spots="'.route('spots.map').'"', false)
        ->assertDontSee('href="'.route('spots.index').'"', false)
        ->assertDontSee('href="'.route('organizations.index').'"', false)
        ->assertDontSee(route('register'), false)
        ->assertDontSee(route('dashboard'), false);
})->with(['en', 'fr']);

test('the home page sends logged-in users to their dashboard', function () {
    $this->actingAs(User::factory()->create())->get(route('index'))
        ->assertOk()
        ->assertSee(route('dashboard'), false)
        ->assertDontSee(route('register'), false);
});

test('the home page is its hero, then a wide column for its sections', function () {
    $html = $this->get(route('index'))->assertOk()->getContent();

    expect($html)->toContain('class="hero ')
        ->not->toContain('min-h-[28rem] flex-grow')
        ->toContain('max-w-6xl');

    // The other pages keep their narrow column
    $this->get(route('contact'))->assertOk()->assertSee('max-w-2xl', false)->assertDontSee('max-w-6xl', false);
});

test('the home page presents Spot in® to public institutions, with its advantages and how it works, and ends inviting them to get in touch', function () {
    $response = $this->get('/')->assertOk()->assertSee(__('ui.homepage.title'));

    foreach (['lasting', 'content', 'governance'] as $advantage) {
        $response->assertSee(__("ui.homepage.advantages.$advantage.title"));
    }

    foreach (['organization', 'members', 'spots', 'print', 'evolve'] as $step) {
        $response->assertSee(__("ui.homepage.workflow.$step.title"));
    }

    // The contact form is on its own page, which the end of the page links to
    $response->assertSeeInOrder([__('ui.homepage.call_to_action.title'), 'href="'.route('contact').'"'], false)
        ->assertDontSee('action="'.route('contact.store').'"', false);
});
