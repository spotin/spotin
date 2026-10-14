<?php

use App\Enums\TokenAbility;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);
});

test('the user endpoint shows the owner of the token and what the token allows', function () {
    $token = $this->user->createToken('CI', [TokenAbility::SpotsRead->value, TokenAbility::SpotsCreate->value], now()->addDays(30));

    $this->withToken($token->plainTextToken)->getJson('/api/v1/user')
        ->assertOk()
        ->assertJsonPath('data.name', 'Jane Doe')
        ->assertJsonPath('data.email', 'jane@example.com')
        ->assertJsonPath('data.is_admin', false)
        ->assertJsonPath('data.token.name', 'CI')
        ->assertJsonPath('data.token.abilities', ['spots:read', 'spots:create'])
        ->assertJsonPath('data.token.expires_at', $token->accessToken->expires_at->toJSON())
        ->assertJsonMissingPath('data.id')
        ->assertJsonMissingPath('data.password');
});

test('the user endpoint needs no specific ability', function () {
    $token = $this->user->createToken('Nothing', [TokenAbility::OrganizationsRead->value]);

    $this->withToken($token->plainTextToken)->getJson('/api/v1/user')->assertOk();
});

test('the last use of a token is recorded', function () {
    $token = $this->user->createToken('CI', ['spots:read']);

    expect($token->accessToken->last_used_at)->toBeNull();

    $this->withToken($token->plainTextToken)->getJson('/api/v1/user')->assertOk();

    expect($token->accessToken->fresh()->last_used_at)->not->toBeNull();
});

test('requests without a valid token are refused', function () {
    $this->getJson('/api/v1/user')->assertUnauthorized();
    $this->withToken('not-a-token')->getJson('/api/v1/user')->assertUnauthorized();
});

test('a revoked or expired token is refused', function () {
    $revoked = $this->user->createToken('Revoked', ['spots:read']);
    $expired = $this->user->createToken('Expired', ['spots:read'], now()->subMinute());

    $revoked->accessToken->delete();

    $this->withToken($revoked->plainTextToken)->getJson('/api/v1/user')->assertUnauthorized();
    $this->withToken($expired->plainTextToken)->getJson('/api/v1/user')->assertUnauthorized();
});

// actingAs() sets the user on the guard without going through a session, so this guards the config:
// sessions must never authenticate on the API, even if they were enabled on it one day.
test('a browser session alone does not authenticate on the API', function () {
    $this->actingAs($this->user)->getJson('/api/v1/user')->assertUnauthorized();
    $this->getJson('/api/v1/spots')->assertUnauthorized();
});

test('tokens of users with an unverified email address are refused', function () {
    Sanctum::actingAs(User::factory()->unverified()->create(), ['spots:read']);

    $this->getJson('/api/v1/user')->assertForbidden()->assertJsonPath('message', 'Your email address is not verified.');
});

test('the API is rate limited', function () {
    Sanctum::actingAs($this->user, ['spots:read']);

    $this->getJson('/api/v1/user')->assertOk()->assertHeader('X-RateLimit-Limit', 60);
});
