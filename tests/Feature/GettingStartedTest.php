<?php

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

beforeEach(function () {
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
    $this->member = User::factory()->create();
    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);
});

/**
 * The steps of the checklist on the dashboard, as `key => done`, or `null` when it is not shown.
 *
 * @return array<string, bool>|null
 */
function gettingStarted(User $user): ?array
{
    $html = test()->actingAs($user)->get(route('dashboard'))->assertOk()->getContent();

    if (! preg_match('/<section aria-labelledby="getting-started".*?<\/section>/s', $html, $section)) {
        return null;
    }

    $steps = [];

    foreach (['organization', 'spot', 'qr_code', 'invite'] as $key) {
        $label = e(__("ui.dashboard.getting_started.steps.$key.label"));

        if (str_contains($section[0], $label)) {
            $steps[$key] = (bool) preg_match('/'.preg_quote(e(__('ui.dashboard.getting_started.done')), '/').'<\/span>\s*'.preg_quote($label, '/').'/', $section[0]);
        }
    }

    return $steps;
}

test('a new member gets the first steps, with a radial progress and a link to the next one, in every language', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)->actingAs($this->member)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee(__('ui.dashboard.getting_started.title'))
        ->assertSee('class="radial-progress', false)
        ->assertSee('style="--value: 33; --size: 3.5rem"', false)
        ->assertSee('aria-valuenow="1"', false)
        ->assertSee('aria-valuemax="3"', false)
        ->assertSee('href="'.route('spots.create').'" class="btn btn-sm"', false);

    expect(gettingStarted($this->member))->toBe(['organization' => true, 'spot' => false, 'qr_code' => false]);
})->with(['en', 'fr']);

test('the steps are done from what the user did: creating a spot, then downloading its QR code', function () {
    $spot = Spot::factory()->for($this->organization)->create(['created_by' => $this->member->id]);

    expect(gettingStarted($this->member))->toBe(['organization' => true, 'spot' => true, 'qr_code' => false]);

    // The QR code step leads to the spot, to download it there
    $this->get(route('dashboard'))->assertSee('href="'.route('spots.show', ['spot' => $spot->uuid]).'" class="btn btn-sm"', false);

    $this->get(route('spots.qr-code', ['spot' => $spot->uuid, 'format' => 'svg']))->assertOk();

    // All done: the checklist stays, full, until the user hides it
    expect(gettingStarted($this->member))->toBe(['organization' => true, 'spot' => true, 'qr_code' => true]);

    $this->get(route('dashboard'))
        ->assertSee(__('ui.dashboard.getting_started.completed'))
        ->assertDontSee(__('ui.dashboard.getting_started.description'))
        ->assertSee('style="--value: 100; --size: 3.5rem"', false);
});

test('downloading a QR code records the first download only, without changing when the spot was updated', function () {
    $spot = Spot::factory()->for($this->organization)->create(['updated_at' => now()->subWeek()]);
    $updatedAt = $spot->updated_at;

    $this->actingAs($this->member)->get(route('spots.qr-code', ['spot' => $spot->uuid, 'format' => 'png']))->assertOk();
    $first = $spot->fresh()->qr_code_downloaded_at;

    $this->travel(1)->day();
    $this->get(route('spots.qr-code', ['spot' => $spot->uuid, 'format' => 'svg']))->assertOk();

    expect($first)->not->toBeNull()
        ->and($spot->fresh()->qr_code_downloaded_at->equalTo($first))->toBeTrue()
        ->and($spot->fresh()->updated_at->equalTo($updatedAt))->toBeTrue();
});

test('the responsible users also invite a colleague, done once someone joined or is invited', function () {
    $responsible = User::factory()->create();
    $this->organization->users()->attach($responsible, ['role' => OrganizationRole::Responsible]);
    $alone = Organization::factory()->create();
    $loner = User::factory()->create();
    $alone->users()->attach($loner, ['role' => OrganizationRole::Responsible]);

    // Acme has a member already
    expect(gettingStarted($responsible))->toHaveKey('invite', true);

    $this->actingAs($loner)->get(route('dashboard'))
        ->assertSee('aria-valuemax="4"', false)
        ->assertSee('href="'.route('organizations.invitations.create', ['organization' => $alone->uuid]).'"', false);
    expect(gettingStarted($loner))->toHaveKey('invite', false);

    Invitation::factory()->for($alone)->create();

    expect(gettingStarted($loner))->toHaveKey('invite', true);

    // The members do not invite
    expect(gettingStarted($this->member))->not->toHaveKey('invite');
});

test('users without organization are told how to join one, and administrators never get the checklist', function () {
    expect(gettingStarted(User::factory()->create()))->toBe(['organization' => false, 'spot' => false, 'qr_code' => false])
        ->and(gettingStarted(User::factory()->admin()->create()))->toBeNull();
});

test('the checklist can be hidden from the dashboard, and shown again from the profile settings', function () {
    $this->actingAs($this->member)
        ->from(route('dashboard'))
        ->put(route('user-profile-information.update'), ['show_getting_started' => '0'])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHasNoErrors();

    $name = $this->member->name;
    $email = $this->member->email;

    // Only the preference changed
    expect($this->member->fresh())
        ->show_getting_started->toBeFalse()
        ->name->toBe($name)
        ->email->toBe($email)
        ->hasVerifiedEmail()->toBeTrue()
        ->and(gettingStarted($this->member))->toBeNull();

    $checkbox = function () {
        preg_match('/<input\s+type="checkbox"\s+name="show_getting_started"[^>]*>/s', $this->get(route('settings.profile'))->assertOk()->getContent(), $input);

        return $input[0];
    };

    expect($checkbox())->not->toMatch('/\schecked/');

    $this->put(route('user-profile-information.update'), ['name' => $name, 'email' => $email, 'show_getting_started' => '1'])
        ->assertSessionHasNoErrors();

    expect($this->member->fresh()->show_getting_started)->toBeTrue()
        ->and($checkbox())->toMatch('/\schecked/')
        ->and(gettingStarted($this->member))->not->toBeNull();
});

test('the profile form still requires a name and an email, and only offers the checklist to the members', function () {
    $this->actingAs($this->member)
        ->put(route('user-profile-information.update'), ['name' => '', 'email' => $this->member->email, 'show_getting_started' => 'maybe'])
        ->assertSessionHasErrorsIn('updateProfileInformation', ['name', 'show_getting_started']);

    $this->actingAs(User::factory()->admin()->create())->get(route('settings.profile'))->assertOk()
        ->assertDontSee('name="show_getting_started"', false);
});
