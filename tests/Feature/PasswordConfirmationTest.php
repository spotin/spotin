<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/sensitive', fn () => 'sensitive content')->middleware(['web', 'auth', 'password.confirm']);
});

test('the password confirmation page renders', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('password.confirm'))
        ->assertOk()
        ->assertSee('<title>'.e(__('ui.auth.password_confirmation.title')), false)
        ->assertSee('name="password"', false)
        ->assertSee('autocomplete="current-password"', false);
});

test('sensitive routes require the password to be confirmed', function () {
    $this->actingAs(User::factory()->create())
        ->get('/sensitive')
        ->assertRedirect(route('password.confirm'));
});

test('users can confirm their password to reach a sensitive route', function () {
    $this->actingAs(User::factory()->create())
        ->get('/sensitive');

    $this->post(route('password.confirm.store'), ['password' => 'password'])
        ->assertRedirect('/sensitive');

    $this->get('/sensitive')->assertOk()->assertSee('sensitive content');
});

test('a wrong password is not confirmed', function () {
    $this->actingAs(User::factory()->create())
        ->from(route('password.confirm'))
        ->post(route('password.confirm.store'), ['password' => 'wrong-password'])
        ->assertRedirect(route('password.confirm'))
        ->assertSessionHasErrors('password');
});
