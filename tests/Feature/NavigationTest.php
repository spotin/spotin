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
        ->assertSee(route('invitations.index'), false)
        ->assertSee(__('ui.invitations.index.title'))
        ->assertSee(route('settings.index'), false)
        ->assertSee(route('logout'), false);
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

test('guests get the login link, no register link, as accounts are created by invitation, no lists of the members and no avatar menu', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee('account-popover', false)
        ->assertDontSee(route('dashboard'), false)
        ->assertDontSee('href="'.route('spots.index').'"', false)
        ->assertDontSee('href="'.route('organizations.index').'"', false)
        ->assertSee(route('login'), false)
        ->assertDontSee(route('register'), false);
});

test('guests get a contact link in both navigation menus, as accounts are created for the institutions that get in touch', function () {
    $link = 'href="'.route('contact').'"';

    // In the mobile menu, in the desktop menu and in the footer.
    expect(substr_count($this->get(route('contact'))->assertOk()->getContent(), $link))->toBe(3);

    // Only in the footer for the logged-in users, already members of an institution.
    expect(substr_count($this->actingAs(User::factory()->create())->get(route('settings.profile'))->assertOk()->getContent(), $link))->toBe(1);
});
