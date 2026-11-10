<?php

use App\Models\User;
use Illuminate\Support\Arr;

test('every interface string exists in every language', function () {
    $keys = fn (string $locale) => array_keys(Arr::dot(require lang_path("$locale/ui.php")));

    expect($keys('fr'))->toEqualCanonicalizing($keys('en'));
});

test('pages show no untranslated keys', function (string $locale, string $route, bool $authenticated) {
    if ($authenticated) {
        $this->actingAs(User::factory()->create());
    }

    $this->withUnencryptedCookie('locale', $locale)
        ->get(route($route))
        ->assertOk()
        ->assertDontSee('ui.');
})->with(['en', 'fr'])->with([
    'home' => ['index', false],
    'login' => ['login', false],
    'register' => ['register', false],
    'forgot password' => ['password.request', false],
    'confirm password' => ['password.confirm', true],
    'profile settings' => ['settings.profile', true],
    'security settings' => ['settings.security', true],
    'developer settings' => ['settings.developer', true],
    'new developer token' => ['settings.developer.tokens.create', true],
]);

test('every interface string the code uses exists', function () {
    $strings = require lang_path('en/ui.php');

    // The keys written out in the code and the views, not the ones built from a variable
    $used = collect([...glob(app_path('{,*/,*/*/,*/*/*/,*/*/*/*/}*.php'), GLOB_BRACE), ...glob(resource_path('views/{,*/,*/*/,*/*/*/,*/*/*/*/}*.php'), GLOB_BRACE)])
        ->flatMap(fn (string $file) => preg_match_all('/[\'"]ui\.([a-z0-9_.]+[a-z0-9_])[\'"]/', file_get_contents($file), $matches) ? $matches[1] : [])
        ->unique()
        ->values();

    expect($used)->not->toBeEmpty()
        // A group too, such as a list of strings the view goes through
        ->and($used->reject(fn (string $key) => Arr::has($strings, $key))->values()->all())->toBe([]);
});
