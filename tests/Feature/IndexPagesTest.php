<?php

use App\Models\User;

test('the settings page links to the settings sections', function () {
    $this->actingAs(User::factory()->create())
        ->get('/settings')
        ->assertOk()
        ->assertSee(route('settings.profile'), false)
        ->assertSee(route('settings.security'), false);
});

test('guests are sent to log in from the settings page', function () {
    $this->get('/settings')->assertRedirect(route('login'));
});

test('the administration page links to the administration sections', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee(route('admin.organizations.index'), false);
});

test('only admins can see the administration page', function () {
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    auth()->logout();
    $this->get('/admin')->assertRedirect(route('login'));
});
