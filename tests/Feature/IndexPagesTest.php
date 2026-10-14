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
