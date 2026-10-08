<?php

test('the preferred locale is stored in a plain cookie', function () {
    $this->patch(route('locale.update'), ['locale' => 'fr'])
        ->assertRedirect()
        ->assertCookie('locale', 'fr', false);
});

test('a stored locale cookie is applied', function () {
    $this->withUnencryptedCookie('locale', 'fr')
        ->get('/')
        ->assertSee('<html lang="fr"', false)
        ->assertSee(__('ui.welcome.title', locale: 'fr'));
});

test('the browser language is used when no locale is stored', function () {
    $this->withHeader('Accept-Language', 'fr-CH,fr;q=0.9,en;q=0.8')
        ->get('/')
        ->assertSee('<html lang="fr"', false);
});

test('the first supported locale is used when the browser prefers none of them', function (array $headers) {
    $this->withHeaders($headers)
        ->withUnencryptedCookie('locale', 'de')
        ->get('/')
        ->assertSee('<html lang="'.config('app.supported_locales')[0].'"', false);
})->with([
    'unsupported language' => [['Accept-Language' => 'de-CH,de;q=0.9']],
    'no language header' => [[]],
]);

test('the language switcher offers every supported locale', function () {
    $response = $this->get('/');

    foreach (config('app.supported_locales') as $locale) {
        $response->assertSee('name="locale" value="'.$locale.'"', false);
    }
});

test('an unsupported locale is rejected', function () {
    $this->patch(route('locale.update'), ['locale' => 'de'])
        ->assertSessionHasErrors('locale')
        ->assertCookieMissing('locale');
});

test('the language switcher has an accessible name and shows the current language', function () {
    $this->withUnencryptedCookie('locale', 'fr')
        ->get('/')
        ->assertSeeInOrder(['<span class="sr-only">'.__('ui.common.locale.label', locale: 'fr').'</span>', 'FR'], false);
});
