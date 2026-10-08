<?php

const CHECKED_THEME_SWITCH = '/class="theme-controller"[^>]*\schecked[\s>]/';

test('a stored dark theme is applied and the theme switch is checked', function () {
    $response = $this->withUnencryptedCookie('theme', 'dark')
        ->get('/')
        ->assertOk()
        ->assertSee('data-theme="dark"', false);

    expect($response->getContent())->toMatch(CHECKED_THEME_SWITCH);
});

test('a stored light theme is applied and the theme switch is unchecked', function () {
    $response = $this->withUnencryptedCookie('theme', 'light')
        ->get('/')
        ->assertSee('data-theme="light"', false);

    expect($response->getContent())->not->toMatch(CHECKED_THEME_SWITCH);
});

test('an unknown theme cookie is ignored', function () {
    $this->withUnencryptedCookie('theme', 'neon')
        ->get('/')
        ->assertDontSee('data-theme=', false);
});

test('no theme is forced without a cookie', function () {
    $this->get('/')->assertDontSee('data-theme=', false);
});
