<?php

use App\Models\Organization;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create(['name' => 'Ada Admin', 'email' => 'ada@example.com']);
    $this->user = User::factory()->create(['name' => 'Uma User', 'email' => 'uma@example.com']);

    Organization::factory()->count(2)->create()->each(fn (Organization $organization) => $organization->users()->attach($this->user));
});

test('guests are redirected to the login page, and the other users are refused the page and the changes', function () {
    $this->get(route('users.index'))->assertRedirect(route('login'));

    $this->actingAs($this->user)->get(route('users.index'))->assertForbidden();
    $this->actingAs($this->user)->put(route('users.update', ['user' => $this->user->id]), ['is_admin' => true])->assertForbidden();

    expect($this->user->fresh()->is_admin)->toBeFalse();
});

test('administrators see every user with their email, their number of organizations and their role', function () {
    $content = $this->actingAs($this->admin)->get(route('users.index'))
        ->assertOk()
        ->assertSee('Ada Admin')
        ->assertSee('Uma User')
        ->assertSee(emailHtml('uma@example.com'), false)
        ->assertSee('href="mailto:uma@example.com"', false)
        ->assertSee(trans_choice('ui.organizations.index.count', 2))
        ->getContent();

    expect($content)->toMatch('/<select\s[^>]*form="role-'.$this->user->id.'"/')
        ->and($content)->toMatch('/<form\s+id="role-'.$this->user->id.'"/');
});

test('administrators search the users by name and by email', function () {
    $this->actingAs($this->admin)->get(route('users.index', ['query' => 'Uma']))
        ->assertOk()
        ->assertSee('Uma User')
        // The admin's name is in the account menu: their row is told by their email
        ->assertDontSee(emailHtml('ada@example.com'), false);

    $this->actingAs($this->admin)->get(route('users.index', ['query' => 'ada@']))
        ->assertOk()
        ->assertSee(emailHtml('ada@example.com'), false)
        ->assertDontSee('Uma User');

    $this->actingAs($this->admin)->get(route('users.index', ['query' => 'nobody']))
        ->assertOk()
        ->assertSee(__('ui.users.index.no_result'));
});

test('administrators make a user an administrator, and no longer', function () {
    $this->actingAs($this->admin)
        ->from(route('users.index'))
        ->put(route('users.update', ['user' => $this->user->id]), ['is_admin' => '1'])
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success', __('ui.users.feedback.role_updated'));

    expect($this->user->fresh()->is_admin)->toBeTrue();

    $this->actingAs($this->admin)
        ->from(route('users.index'))
        ->put(route('users.update', ['user' => $this->user->id]), ['is_admin' => '0'])
        ->assertRedirect(route('users.index'));

    expect($this->user->fresh()->is_admin)->toBeFalse();
});

test('the role is required and must be a boolean', function () {
    $this->actingAs($this->admin)
        ->put(route('users.update', ['user' => $this->user->id]), ['is_admin' => 'maybe'])
        ->assertSessionHasErrors('is_admin');
});

test('the last administrator cannot give up the role, and cannot even try from the page', function () {
    $this->actingAs($this->admin)
        ->from(route('users.index'))
        ->put(route('users.update', ['user' => $this->admin->id]), ['is_admin' => '0'])
        ->assertRedirect(route('users.index'))
        ->assertSessionHasErrors(['user' => __('ui.users.feedback.last_admin')]);

    expect($this->admin->fresh()->is_admin)->toBeTrue();

    $content = $this->actingAs($this->admin)->get(route('users.index'))
        ->assertSee(__('ui.users.index.last_admin'))
        ->getContent();

    expect($content)->toMatch('/<select\s[^>]*form="role-'.$this->admin->id.'"[^>]*disabled/')
        ->and($content)->not->toMatch('/<form\s+id="role-'.$this->admin->id.'"/');
});

test('an administrator who gives up the role is sent to the dashboard, as the list is no longer theirs', function () {
    User::factory()->admin()->create();

    $this->actingAs($this->admin)
        ->from(route('users.index'))
        ->put(route('users.update', ['user' => $this->admin->id]), ['is_admin' => '0'])
        ->assertRedirect(route('dashboard'));

    expect($this->admin->fresh()->is_admin)->toBeFalse();

    $this->actingAs($this->admin->fresh())->get(route('users.index'))->assertForbidden();
});

test('only administrators get the users in the navigation and on the dashboard', function () {
    User::factory()->count(3)->create();

    $content = $this->actingAs($this->admin)->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('ui.dashboard.statistics.users.title'))
        ->getContent();

    // Both navigation menus and the stat lead to the list
    expect(substr_count($content, 'href="'.route('users.index').'"'))->toBe(3)
        ->and($content)->toMatch('/<div class="stat-value text-primary">5<\/div>/');

    $content = $this->actingAs($this->user)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee(__('ui.dashboard.statistics.users.title'))
        ->getContent();

    expect($content)->not->toContain(route('users.index'));
});
