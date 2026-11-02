<?php

use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Support\Facades\Blade;

test('an alert has the class and the icon of its type, and no button to close it by default', function (string $type, string $class) {
    $html = Blade::render('<x-alert :type="$type">Saved</x-alert>', ['type' => $type]);

    expect($html)->toContain('role="alert"')
        ->toContain('class="alert '.$class.'"')
        ->toContain('Saved')
        ->not->toContain('data-alert-close');
})->with([
    'info' => ['info', 'alert-info'],
    'success' => ['success', 'alert-success'],
    'error' => ['error', 'alert-error'],
]);

test('a closable alert has a button to close it, shown by the script', function () {
    expect(Blade::render('<x-alert type="success" closable>Saved</x-alert>'))
        ->toMatch('/<button[^>]*aria-label="'.preg_quote(__('ui.common.actions.close'), '/').'"[^>]*data-alert-close[^>]*hidden/s');
});

test('an alert can have its own icon, role and actions, before its button to close it', function () {
    $html = Blade::render('<x-alert type="info" icon="mail" role="status" closable>Invited<x-slot:actions><a href="/invitations">See</a></x-slot:actions></x-alert>');

    expect($html)->toContain('role="status"')
        ->toContain('<a href="/invitations">See</a>')
        ->and(strpos($html, 'See'))->toBeLessThan(strpos($html, 'data-alert-close'));
});

test('the messages shown once after an action can be closed, but not the one of a new token, nor the notes of a page', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    $organization->users()->attach($user);
    $spot = Spot::factory()->for($organization)->create(['is_hidden' => true, 'created_by' => $user->id]);

    $this->actingAs($user)->followingRedirects()
        ->patch(route('spots.update', $spot), ['name' => 'Renamed'])
        ->assertSee(__('ui.spots.feedback.spot_updated'))
        ->assertSee('data-alert-close', false);

    // The page of the hidden spot has its note, without the message of the action
    $this->get(route('spots.show', ['spot' => $spot->uuid]))
        ->assertSee(e(__('ui.spots.show.notes.hidden', ['organization' => $organization->name])), false)
        ->assertDontSee('data-alert-close', false);

    $this->followingRedirects()
        ->post(route('settings.developer.tokens.store'), ['name' => 'CI', 'abilities' => ['spots:read'], 'expiry' => '30'])
        ->assertSee(__('ui.settings.developer.feedback.token_created'))
        ->assertDontSee('data-alert-close', false);
});
