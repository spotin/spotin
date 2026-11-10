<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->organization = Organization::factory()->create(['name' => 'Acme']);

    $this->admin = User::factory()->admin()->create();
    $this->member = User::factory()->create();
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);

    // Prepared by a member without a name nor a location: the first person who scans it sets it up
    $this->spot = Spot::factory()->for($this->organization)->needingSetup()->create([
        'name' => null,
        'description' => 'By the lake.',
        'latitude' => null,
        'longitude' => null,
        'website' => null,
    ]);
});

test('the setup form renders in every language without untranslated keys', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale);

    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.');
    $this->actingAs($this->member)->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.');
    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertOk()->assertDontSee('ui.');
})->with(['en', 'fr']);

test('a member creates a spot to set up on site without a name, the name is required otherwise', function () {
    $organization = $this->organization->uuid;

    $this->actingAs($this->member)
        ->post(route('spots.store'), ['organization' => $organization, 'name' => '', 'needs_setup' => '0'])
        ->assertSessionHasErrors('name');

    $this->post(route('spots.store'), ['organization' => $organization, 'name' => '', 'needs_setup' => '1'])->assertSessionHasNoErrors();

    expect(Spot::whereBelongsTo($this->organization)->where('needs_setup', true)->whereNull('name')->count())->toBe(2);
});

test('the creation form offers to set the spot up now or on site, with the name required only now', function () {
    $html = $this->actingAs($this->member)
        ->get(route('spots.create', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSeeInOrder([
            __('ui.spots.common.forms.fields.needs_setup.now.label'),
            __('ui.spots.common.forms.fields.needs_setup.on_site.label'),
            'name="name"',
        ])
        ->getContent();

    // Set up now by default; the name is made required by JS, and checked by the server
    expect($html)->toMatch('/id="needs-setup-now"[^>]*checked/')
        ->toContain('data-required-unless-checked="needs-setup-on_site"');
});

test('a scan of a spot that needs to be set up leads to its form, even when it redirects to its website', function () {
    $this->spot->update(['website' => 'https://example.com', 'redirect_to_website' => true]);

    $this->get(route('spots.redirect', ['spot' => $this->spot->uuid]))
        ->assertRedirect(route('spots.edit', ['spot' => $this->spot->uuid]))
        ->assertSessionHas('scanned', true);

    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertRedirect(route('spots.edit', ['spot' => $this->spot->uuid]));
});

test('the setup form has the details given so far, without what belongs to the members', function () {
    $html = $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee(__('ui.spots.setup.introduction', ['organization' => 'Acme']))
        ->assertSee('By the lake.')
        ->assertSee('name="redirect_to_website"', false)
        ->assertDontSee('name="is_hidden"', false)
        ->assertDontSee('name="needs_setup"', false)
        ->assertDontSee('data-required-unless-checked', false)
        ->getContent();

    // Without the step of the members' choice
    expect(substr_count($html, 'data-step '))->toBe(3);
});

test('anyone who scans the spot sets it up, which keeps it in its organization and closes the form to them', function (?string $user) {
    $user ? $this->actingAs($this->$user) : null;

    $this->patch(route('spots.update', $this->spot), [
        'name' => 'Bench',
        'description' => 'A bench by the lake.',
        'latitude' => 46.5,
        'longitude' => 6.6,
        'website' => 'https://bench.example.com',
        'redirect_to_website' => '1',
    ])
        ->assertRedirect(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertSessionHas('success', __('ui.spots.feedback.spot_set_up'));

    expect($this->spot->fresh())
        ->name->toBe('Bench')
        ->latitude->toBe(46.5)
        ->redirect_to_website->toBeTrue()
        ->needs_setup->toBeFalse()
        ->organization_id->toBe($this->organization->id);

    $edit = $this->get(route('spots.edit', ['spot' => $this->spot->uuid]));
    $user ? $edit->assertForbidden() : $edit->assertRedirect(route('login'));
    $this->patch(route('spots.update', $this->spot), ['name' => 'Again'])->assertForbidden();
})->with([null, 'outsider']);

test('setting a spot up needs a name, and cannot hide it, leave it to be set up nor give it custom data', function () {
    $this->patch(route('spots.update', $this->spot), ['name' => ''])->assertSessionHasErrors('name');
    $this->patch(route('spots.update', $this->spot), ['name' => 'Bench', 'is_hidden' => '1'])->assertSessionHasErrors('is_hidden');
    $this->patch(route('spots.update', $this->spot), ['name' => 'Bench', 'needs_setup' => '1'])->assertSessionHasErrors('needs_setup');
    $this->patch(route('spots.update', $this->spot), ['name' => 'Bench', 'custom_data' => ['floor' => 2]])->assertSessionHasErrors('custom_data');

    expect($this->spot->fresh())->name->toBeNull()->needs_setup->toBeTrue();
});

test('a member fills a spot without setting it up, sets it up, or reopens it without losing its details', function () {
    $this->actingAs($this->member);

    $this->patch(route('spots.update', $this->spot), ['website' => 'https://example.com'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('success', __('ui.spots.feedback.spot_updated'));
    expect($this->spot->fresh())->needs_setup->toBeTrue()->website->toBe('https://example.com');

    // Set up now, which needs a name
    $this->patch(route('spots.update', $this->spot), ['needs_setup' => '0'])->assertSessionHasErrors('name');
    $this->patch(route('spots.update', $this->spot), ['name' => 'Bench', 'needs_setup' => '0'])->assertSessionHasNoErrors();
    expect($this->spot->fresh()->needs_setup)->toBeFalse();

    $this->patch(route('spots.update', $this->spot), ['needs_setup' => '1'])->assertSessionHasNoErrors();
    expect($this->spot->fresh())
        ->needs_setup->toBeTrue()
        ->name->toBe('Bench')
        ->description->toBe('By the lake.')
        ->website->toBe('https://example.com');
});

test('the members see the page of a spot that needs to be set up, with a note, and its edit form with the choice', function () {
    $this->actingAs($this->member)
        ->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee(__('ui.spots.show.manage.notes.needs_setup'));

    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee(__('ui.spots.edit.title'))
        ->assertSee('name="needs_setup"', false)
        ->assertSee('name="is_hidden"', false);
});

test('the spots that need to be set up are only listed for the members of their organization, with a badge', function () {
    $organization = route('organizations.show', ['organization' => $this->organization->uuid]);
    $name = $this->spot->displayName();

    $this->get($organization)->assertOk()->assertDontSee($name);
    $this->actingAs($this->outsider)->get($organization)->assertOk()->assertDontSee($name);

    foreach ([route('spots.index'), $organization] as $list) {
        $this->actingAs($this->member)->get($list)->assertOk()->assertSee($name)->assertSee(__('ui.spots.common.needs_setup'));
    }

    auth()->logout();

    $this->spot->update(['latitude' => 46.5, 'longitude' => 6.6]);
    expect(collect($this->getJson(route('spots.map'))->json('features')))->toBeEmpty()
        ->and(collect($this->actingAs($this->member)->getJson(route('spots.map'))->json('features')))->toHaveCount(1);
});

test('an unnamed spot is shown by the start of its UUID', function () {
    $name = 'Unnamed spot · '.substr($this->spot->uuid, 0, 6);

    expect($this->spot->displayName())->toBe($name);

    $this->actingAs($this->member)
        ->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee('<title>'.e($name), false)
        ->assertSeeInOrder(['<h1', $name], false);

    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->assertSee($name);
});

test('anyone can set up a spot that needs it, only its members can edit it otherwise', function () {
    $guest = Gate::forUser(null);

    expect($guest->allows('update', $this->spot))->toBeTrue()
        ->and($guest->allows('manage', $this->spot))->toBeFalse()
        ->and($this->outsider->can('update', $this->spot))->toBeTrue()
        ->and($this->outsider->can('manage', $this->spot))->toBeFalse()
        ->and($this->member->can('manage', $this->spot))->toBeTrue()
        ->and($this->admin->can('manage', $this->spot))->toBeTrue();

    $this->spot->update(['needs_setup' => false]);

    expect($guest->allows('update', $this->spot))->toBeFalse()
        ->and($this->outsider->can('update', $this->spot))->toBeFalse();
});

test('an unverified member cannot edit a spot once set up, and only sets up the ones that need it, as anyone', function () {
    $this->member->forceFill(['email_verified_at' => null])->save();
    $this->actingAs($this->member);

    expect($this->member->can('manage', $this->spot))->toBeFalse();

    $this->get(route('spots.show', ['spot' => $this->spot->uuid]))->assertRedirect(route('spots.edit', ['spot' => $this->spot->uuid]));
    $this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->assertOk()->assertSee(__('ui.spots.setup.title'))->assertDontSee('name="is_hidden"', false);
    $this->patch(route('spots.update', $this->spot), ['name' => 'Bench', 'is_hidden' => '1'])->assertSessionHasErrors('is_hidden');
    $this->patch(route('spots.update', $this->spot), ['name' => 'Bench'])->assertSessionHasNoErrors();

    expect($this->spot->fresh()->needs_setup)->toBeFalse();

    $setUp = Spot::factory()->for($this->organization)->create(['name' => 'Harbour']);

    $this->get(route('spots.edit', ['spot' => $setUp->uuid]))->assertRedirect(route('verification.notice'));
    $this->patch(route('spots.update', $setUp), ['name' => 'Port'])->assertForbidden();

    expect($setUp->fresh()->name)->toBe('Harbour');
});

test('the API creates a spot to set up on site, and sets it up for the others than its members', function () {
    Sanctum::actingAs($this->member, ['spots:create', 'spots:read']);

    $this->postJson('/api/v1/spots', ['organization' => $this->organization->uuid, 'needs_setup' => true])
        ->assertCreated()
        ->assertJsonPath('data.attributes.name', null)
        ->assertJsonPath('data.attributes.needs_setup', true);

    $this->postJson('/api/v1/spots', ['organization' => $this->organization->uuid])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');

    Sanctum::actingAs($this->outsider, ['spots:update']);

    // Its name is not kept when not sent: the spot needs one to be set up
    $this->patchJson('/api/v1/spots/'.$this->spot->uuid, ['website' => 'https://example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');

    $this->patchJson('/api/v1/spots/'.$this->spot->uuid, ['name' => 'Bench'])
        ->assertOk()
        ->assertJsonPath('data.attributes.needs_setup', false);
});
