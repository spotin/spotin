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
]);
