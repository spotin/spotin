<?php

use App\Models\User;

test('the initials are the first letters of the first two words of the name', function (string $name, string $initials) {
    expect(User::factory()->make(['name' => $name])->initials())->toBe($initials);
})->with([
    'two words' => ['Jane Doe', 'JD'],
    'lowercase' => ['jane doe', 'JD'],
    'one word' => ['Jane', 'J'],
    'more than two words' => ['Jean Pierre Dupont', 'JP'],
    'extra spaces' => ['  Jane   Doe ', 'JD'],
    'accents' => ['élodie Émile', 'ÉÉ'],
]);

test('logged-in users get an avatar menu with their identity and the account links', function (string $locale) {
    $user = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

    $this->withUnencryptedCookie('locale', $locale)->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('popovertarget="account-popover"', false)
        ->assertSee('aria-label="'.__('ui.common.account').'"', false)
        ->assertSee('JD')
        ->assertSee('Jane Doe')
        ->assertSee('jane@example.com')
        ->assertSee(route('settings.profile'), false)
        ->assertSee(route('settings.security'), false)
        ->assertSee(route('logout'), false)
        ->assertDontSee('settings-popover', false);
})->with(['en', 'fr']);

test('logged-in users get a dashboard link in both navigation menus, outside of the avatar menu', function () {
    $html = $this->actingAs(User::factory()->create())->get(route('settings.profile'))->assertOk()->getContent();

    $link = '/<a\s+href="'.preg_quote(route('dashboard'), '/').'"/';

    // Once in the mobile menu and once in the desktop menu.
    expect(preg_match_all($link, $html))->toBe(2);

    // Not in the account menu, which opens from the avatar.
    preg_match('/<ul\s+popover\s+id="account-popover".*?<\/ul>/s', $html, $menu);
    expect($menu[0])->not->toContain(route('dashboard'));
});

test('guests get the login and register links and no avatar menu', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee('account-popover', false)
        ->assertDontSee(route('dashboard'), false)
        ->assertSee(route('register'), false);
});
