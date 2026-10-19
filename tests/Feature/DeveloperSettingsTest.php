<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Laravel\Sanctum\PersonalAccessToken;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->admin = User::factory()->admin()->create();
});

test('the developer settings render in every language without untranslated keys', function (string $locale) {
    $this->user->createToken('CI', ['spots:read', 'organizations:read'], now()->addDays(30));
    $this->user->createToken('Old', ['spots:read'], now()->subDay());

    $this->withUnencryptedCookie('locale', $locale)->actingAs($this->user)->get(route('settings.developer'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('CI')
        ->assertSee(__('ui.settings.developer.index.warning'))
        ->assertSee('>'.__('ui.settings.developer.index.your_tokens').'</h2>', false)
        ->assertSee(__('ui.settings.developer.index.table.expired'))
        ->assertSee(route('settings.developer.tokens.create'), false)
        ->assertDontSee('<form method="POST" action="'.route('settings.developer.tokens.store').'"', false);

    $this->get(route('settings.developer.tokens.create'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('<form method="POST" action="'.route('settings.developer.tokens.store').'"', false)
        ->assertSee(route('settings.developer'), false);
})->with(['en', 'fr']);

test('the settings link to the developer settings', function () {
    $this->actingAs($this->user)->get(route('settings.index'))->assertOk()->assertSee(route('settings.developer'), false);
});

test('guests and users with an unverified email address cannot use the developer settings', function () {
    $this->get(route('settings.developer'))->assertRedirect(route('login'));
    $this->get(route('settings.developer.tokens.create'))->assertRedirect(route('login'));
    $this->post(route('settings.developer.tokens.store'), ['name' => 'CI'])->assertRedirect(route('login'));

    $this->actingAs(User::factory()->unverified()->create());

    $this->get(route('settings.developer'))->assertRedirect(route('verification.notice'));
    $this->get(route('settings.developer.tokens.create'))->assertRedirect(route('verification.notice'));
});

test('a user without tokens is told so', function () {
    $this->actingAs($this->user)->get(route('settings.developer'))
        ->assertOk()
        ->assertSee(__('ui.settings.developer.index.table.empty'))
        ->assertSee(route('settings.developer.tokens.create'), false)
        ->assertDontSee('<table', false);
});

test('creating a token shows its plain text value once and stores the chosen abilities and expiry', function () {
    $this->freezeTime();

    $response = $this->actingAs($this->user)->post(route('settings.developer.tokens.store'), [
        'name' => 'CI',
        'abilities' => ['spots:read', 'spots:create', 'spots:read'],
        'expiry' => '30',
    ]);

    $response->assertRedirect(route('settings.developer'))->assertSessionHas('success', __('ui.settings.developer.feedback.token_created'));

    $token = $this->user->tokens()->sole();

    expect($token->name)->toBe('CI')
        ->and($token->abilities)->toBe(['spots:read', 'spots:create'])
        ->and($token->expires_at->toDateTimeString())->toBe(now()->addDays(30)->toDateTimeString());

    $plainText = session('new-token');

    expect($plainText)->toStartWith($token->id.'|');

    $this->get(route('settings.developer'))->assertOk()
        ->assertSee($plainText)
        ->assertSee(__('ui.settings.developer.feedback.token_created'));
    $this->get(route('settings.developer'))->assertOk()->assertDontSee($plainText);

    // The token works against the API, within its abilities.
    $this->withToken($plainText)->getJson('/api/v1/spots')->assertOk();
    $this->withToken($plainText)->getJson('/api/v1/organizations')->assertForbidden();
});

test('a token can be created without expiry', function () {
    $this->actingAs($this->user)->post(route('settings.developer.tokens.store'), [
        'name' => 'Forever',
        'abilities' => ['spots:read'],
        'expiry' => 'never',
    ])->assertSessionHasNoErrors();

    expect($this->user->tokens()->sole()->expires_at)->toBeNull();
});

test('the expiry choices set the expiration date', function (string $expiry, int $days) {
    $this->freezeTime();

    $this->actingAs($this->user)->post(route('settings.developer.tokens.store'), [
        'name' => 'CI',
        'abilities' => ['spots:read'],
        'expiry' => $expiry,
    ]);

    expect($this->user->tokens()->sole()->expires_at->toDateTimeString())->toBe(now()->addDays($days)->toDateTimeString());
})->with([['30', 30], ['90', 90], ['365', 365]]);

test('the token input is validated', function (array $input, string $error) {
    $this->actingAs($this->user)
        ->from(route('settings.developer.tokens.create'))
        ->post(route('settings.developer.tokens.store'), $input + ['name' => 'CI', 'abilities' => ['spots:read'], 'expiry' => '90'])
        ->assertRedirect(route('settings.developer.tokens.create'))
        ->assertSessionHasErrors($error);

    expect($this->user->tokens()->count())->toBe(0);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'too long name' => [['name' => str_repeat('a', 101)], 'name'],
    'no abilities' => [['abilities' => []], 'abilities'],
    'unknown ability' => [['abilities' => ['spots:fly']], 'abilities.0'],
    'invalid expiry' => [['expiry' => '7'], 'expiry'],
]);

test('token names are unique for a user only', function () {
    $this->user->createToken('CI', ['spots:read']);
    $this->admin->createToken('CI', ['spots:read']);

    $this->actingAs($this->user)->post(route('settings.developer.tokens.store'), [
        'name' => 'CI', 'abilities' => ['spots:read'], 'expiry' => '90',
    ])->assertSessionHasErrors('name');

    $this->actingAs($this->admin)->post(route('settings.developer.tokens.store'), [
        'name' => 'Other', 'abilities' => ['spots:read'], 'expiry' => '90',
    ])->assertSessionHasNoErrors();
});

test('only administrators can give a token the abilities to change organizations', function () {
    $write = ['organizations:create', 'organizations:update', 'organizations:delete'];

    foreach ($write as $ability) {
        $this->actingAs($this->user)->post(route('settings.developer.tokens.store'), [
            'name' => $ability, 'abilities' => [$ability], 'expiry' => '90',
        ])->assertSessionHasErrors('abilities.0');
    }

    $this->actingAs($this->user)->post(route('settings.developer.tokens.store'), [
        'name' => 'Read', 'abilities' => ['organizations:read', 'spots:delete'], 'expiry' => '90',
    ])->assertSessionHasNoErrors();

    $this->actingAs($this->admin)->post(route('settings.developer.tokens.store'), [
        'name' => 'Everything', 'abilities' => ['organizations:read', ...$write], 'expiry' => '90',
    ])->assertSessionHasNoErrors();

    expect($this->admin->tokens()->sole()->abilities)->toContain('organizations:delete');
});

test('the abilities are in one fieldset under a single heading', function () {
    $html = $this->actingAs($this->user)->get(route('settings.developer.tokens.create'))->assertOk()->getContent();

    expect($html)->toMatch('/<legend class="fieldset-legend">\s*'.preg_quote(__('ui.settings.developer.common.forms.fields.abilities.label'), '/').'\s*<\/legend>/')
        ->and(substr_count($html, 'name="abilities[]"'))->toBe(5);
});

test('the abilities error is shown after the abilities fieldset, in the way the validator of the form shows errors', function (array $abilities) {
    $this->actingAs($this->user)
        ->from(route('settings.developer.tokens.create'))
        ->post(route('settings.developer.tokens.store'), ['name' => 'CI', 'abilities' => $abilities, 'expiry' => '90'])
        ->assertRedirect(route('settings.developer.tokens.create'));

    $html = $this->get(route('settings.developer.tokens.create'))->assertOk()->getContent();

    // The hint is only visible when it follows an element with the "validator" class and an invalid field.
    expect($html)->toMatch('/<fieldset class="fieldset validator[^"]*">/')
        ->and($html)->toMatch('/<\/fieldset>\s*<span class="validator-hint[^"]*">\s*\S[^<]*<\/span>/')
        ->and($html)->toMatch('/<input\s+type="checkbox"[^>]*aria-invalid="true"/');
})->with([
    'no ability' => [[]],
    'unknown ability' => [['spots:fly']],
]);

test('the abilities fieldset has no error when the form has not been submitted', function () {
    $html = $this->actingAs($this->user)->get(route('settings.developer.tokens.create'))->assertOk()->getContent();

    expect($html)->not->toContain('validator-hint')
        ->and($html)->not->toContain('aria-invalid="true"');
});

test('the expiration is chosen from a select with a placeholder and no default', function () {
    $html = $this->actingAs($this->user)->get(route('settings.developer.tokens.create'))->assertOk()->getContent();

    expect($html)->toMatch('/<option value="" disabled\s+selected>\s*'.preg_quote(__('ui.settings.developer.common.forms.fields.expiry.placeholder'), '/').'\s*<\/option>/')
        ->and($html)->toContain('<option value="90"')
        ->and($html)->not->toMatch('/<option value="90"[^>]*selected/');

    $this->from(route('settings.developer.tokens.create'))
        ->post(route('settings.developer.tokens.store'), ['name' => 'CI', 'abilities' => ['spots:read']])
        ->assertSessionHasErrors('expiry');
});

test('the form only offers the abilities the user can use', function () {
    $this->actingAs($this->user)->get(route('settings.developer.tokens.create'))
        ->assertSee('value="organizations:read"', false)
        ->assertSee('value="spots:delete"', false)
        ->assertDontSee('value="organizations:create"', false)
        ->assertDontSee('value="organizations:delete"', false);

    $this->actingAs($this->admin)->get(route('settings.developer.tokens.create'))
        ->assertSee('value="organizations:create"', false)
        ->assertSee('value="organizations:delete"', false);
});

test('the abilities are shown with their identifier on the form and in the list of tokens', function () {
    $this->user->createToken('CI', ['organizations:read', 'spots:create']);

    $form = $this->actingAs($this->user)->get(route('settings.developer.tokens.create'))->assertOk()->getContent();

    expect($form)->toMatch('/'.preg_quote(__('ui.settings.developer.common.forms.fields.abilities.options.organizations_read'), '/').'\s*<\/span>\s*<code\s+class="badge[^>]*>\s*organizations:read\s*<\/code\s*>/')
        ->and($form)->toMatch('/'.preg_quote(__('ui.settings.developer.common.forms.fields.abilities.options.spots_delete'), '/').'\s*<\/span>\s*<code\s+class="badge[^>]*>\s*spots:delete\s*<\/code\s*>/');

    $list = $this->get(route('settings.developer'))->assertOk()->getContent();

    expect($list)->toMatch('/<code\s+class="badge[^"]*"[^>]*>\s*organizations:read\s*<\/code>/')
        ->and($list)->toMatch('/<code\s+class="badge[^"]*"\s+title="'.preg_quote(__('ui.settings.developer.common.forms.fields.abilities.options.spots_create'), '/').'"\s*>\s*spots:create\s*<\/code>/');
});

test('the tokens of other users are not listed', function () {
    $this->user->createToken('Mine', ['spots:read']);
    $this->admin->createToken('Theirs', ['spots:read']);

    $this->actingAs($this->user)->get(route('settings.developer'))->assertSee('Mine')->assertDontSee('Theirs');
});

test('a user revokes their own token', function () {
    $token = $this->user->createToken('CI', ['spots:read']);

    $this->actingAs($this->user)->get(route('settings.developer'))
        ->assertSee('id="revoke-token-'.$token->accessToken->id.'-modal"', false);

    $this->delete(route('settings.developer.tokens.destroy', ['token' => $token->accessToken->id]))
        ->assertRedirect(route('settings.developer'))
        ->assertSessionHas('success', __('ui.settings.developer.feedback.token_revoked'));

    expect(PersonalAccessToken::count())->toBe(0);

    $this->withToken($token->plainTextToken)->getJson('/api/v1/me')->assertUnauthorized();
});

test('a user cannot revoke the token of someone else', function () {
    $token = $this->admin->createToken('Theirs', ['spots:read']);

    $this->actingAs($this->user)->delete(route('settings.developer.tokens.destroy', ['token' => $token->accessToken->id]))->assertNotFound();
    $this->delete(route('settings.developer.tokens.destroy', ['token' => 999999]))->assertNotFound();

    expect(PersonalAccessToken::count())->toBe(1);
});

test('the last use and the expiration of the tokens are shown', function () {
    $token = $this->user->createToken('CI', ['spots:read'], now()->addDays(10));

    $this->actingAs($this->user)->get(route('settings.developer'))->assertSee(__('ui.settings.developer.index.table.never_used'));

    $token->accessToken->forceFill(['last_used_at' => Date::now()->subHours(3)])->save();

    $this->get(route('settings.developer'))
        ->assertDontSee(__('ui.settings.developer.index.table.never_used'))
        ->assertSee(__('ui.settings.developer.index.table.last_used', ['when' => now()->subHours(3)->diffForHumans()]))
        ->assertSee(__('ui.settings.developer.index.table.expires', ['date' => now()->addDays(10)->isoFormat('LL')]))
        ->assertSee('title="'.now()->addDays(10)->isoFormat('LLL').'"', false);
});

test('the errors of the selects are shown, as the hint only appears after an element with the validator class', function () {
    $organization = Organization::factory()->create();
    $organization->users()->attach($this->user, ['role' => OrganizationRole::Responsible]);

    $pages = [
        route('settings.developer.tokens.create'),
        route('organizations.invitations.create', ['organization' => $organization->uuid]),
    ];

    foreach ($pages as $page) {
        $this->actingAs($this->user)->get($page)->assertOk()->assertSee('<label class="select validator w-full">', false);
    }
});
