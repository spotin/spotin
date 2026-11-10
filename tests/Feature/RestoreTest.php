<?php

use App\Actions\Organizations\DeleteOrganization;
use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->member = User::factory()->create();
    $this->outsider = User::factory()->create();
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);
    $this->spot = Spot::factory()->for($this->organization)->create(['name' => 'Harbour', 'latitude' => 46.5, 'longitude' => 6.6]);
});

test('the deleted spots are listed with their deletion date and a badge, leading to their page, as their map, in every language', function (string $locale) {
    Spot::factory()->for($this->organization)->create(['name' => 'Lighthouse']);
    $this->spot->delete();

    // The spots of a deleted organization come back with it only
    $other = Organization::factory()->create();
    $other->users()->attach($this->member);
    Spot::factory()->for($other)->create(['name' => 'Foreign']);
    app(DeleteOrganization::class)->delete($other);

    $this->withUnencryptedCookie('locale', $locale)->actingAs($this->member);

    $this->get(route('spots.index', ['deleted' => 1]))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee(__('ui.spots.index.filter.deleted'))
        ->assertSee('Harbour')
        ->assertDontSee('Lighthouse')
        ->assertDontSee('Foreign')
        ->assertSee(__('ui.spots.common.deleted'))
        ->assertSee('href="'.route('spots.show', ['spot' => $this->spot->uuid]).'"', false)
        ->assertDontSee('action="'.route('spots.restore', ['spot' => $this->spot->uuid]).'"', false)
        ->assertSee('data-map-spots="'.e(route('spots.manageable.map', ['deleted' => 1])).'"', false);

    expect($this->getJson(route('spots.manageable.map', ['deleted' => 1]))->json('features'))
        ->toHaveCount(1)
        ->{0}->properties->toBe(['name' => 'Harbour', 'url' => route('spots.show', ['spot' => $this->spot->uuid])]);

})->with(['en', 'fr']);

test('the page of an organization lists its deleted spots too, for its members and the administrators only', function () {
    Spot::factory()->for($this->organization)->create(['name' => 'Lighthouse']);
    $this->spot->delete();

    $url = route('organizations.show', ['organization' => $this->organization->uuid, 'deleted' => 1]);
    $map = route('organizations.spots.map', ['organization' => $this->organization->uuid, 'deleted' => 1]);

    foreach ([$this->member, $this->admin] as $user) {
        $this->actingAs($user)->get($url)->assertOk()
            ->assertSee(__('ui.spots.index.filter.deleted'))
            ->assertSee('Harbour')
            ->assertDontSee('Lighthouse')
            ->assertSee('href="'.route('spots.show', ['spot' => $this->spot->uuid]).'"', false)
            ->assertSee('data-map-spots="'.e($map).'"', false);

        expect($this->getJson($map)->json('features.0.properties.name'))->toBe('Harbour');
    }

    // The others get the public list: the filter is not theirs
    $this->actingAs($this->outsider)->get($url)->assertOk()
        ->assertDontSee(__('ui.spots.index.filter.deleted'))
        ->assertSee('Lighthouse')
        ->assertDontSee('Harbour');

    auth()->logout();
    $this->get($url)->assertOk()->assertSee('Lighthouse')->assertDontSee('Harbour');
});

test('a deleted spot keeps its page and its edit page for who can restore it, with a badge and the button to restore it', function () {
    $this->spot->delete();

    $show = route('spots.show', ['spot' => $this->spot->uuid]);
    $edit = route('spots.edit', ['spot' => $this->spot->uuid]);
    $restore = 'action="'.route('spots.restore', ['spot' => $this->spot->uuid]).'"';

    $this->actingAs($this->member)->get($show)->assertOk()
        ->assertSee('Harbour')
        ->assertSee(__('ui.spots.common.deleted'))
        // Its edit page restores it
        ->assertSeeInOrder(['href="'.$edit.'"', __('ui.common.actions.restore')], false)
        // Its QR code menu stays, its code being the same once it is restored
        ->assertSee(route('spots.qr-code', ['spot' => $this->spot->uuid, 'format' => 'svg']), false);

    $this->get(route('spots.qr-code', ['spot' => $this->spot->uuid, 'format' => 'svg']))->assertOk();
    $this->actingAs($this->outsider)->get(route('spots.qr-code', ['spot' => $this->spot->uuid, 'format' => 'svg']))->assertForbidden();
    $this->actingAs($this->member);

    $this->get($edit)->assertOk()
        ->assertSee(e(__('ui.spots.edit.deleted', ['date' => $this->spot->fresh()->deleted_at->isoFormat('LL')])), false)
        ->assertSee($restore, false)
        ->assertDontSee('action="'.route('spots.update', $this->spot).'"', false)
        ->assertDontSee('id="danger-title"', false);

    $this->actingAs($this->admin)->get($show)->assertOk()->assertSee(__('ui.spots.common.deleted'));

    // Not found for the others, and the guests are asked to log in to edit it
    $this->actingAs($this->outsider)->get($show)->assertGone()->assertSee(__('ui.spots.removed.title'));
    $this->get($edit)->assertNotFound();
    auth()->logout();
    $this->get($show)->assertGone();
    $this->get($edit)->assertRedirect(route('login'));

    // A spot of a deleted organization comes back with it only: not even the administrators reach it
    app(DeleteOrganization::class)->delete($this->organization);
    $this->actingAs($this->admin)->get($show)->assertGone();
});

test('a name taken meanwhile is shown on the edit page of the deleted spot', function () {
    $this->spot->delete();
    Spot::factory()->for($this->organization)->create(['name' => 'Harbour']);

    $this->actingAs($this->member)
        ->followingRedirects()
        ->from(route('spots.edit', ['spot' => $this->spot->uuid]))
        ->post(route('spots.restore', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee(e(__('ui.spots.feedback.name_taken', ['name' => 'Harbour', 'organization' => 'Acme'])), false);
});

test('the members restore a deleted spot of their organization, which leads its QR code to it again', function () {
    $this->spot->delete();

    $this->actingAs($this->outsider)->post(route('spots.restore', ['spot' => $this->spot->uuid]))->assertForbidden();

    $this->actingAs($this->member)->post(route('spots.restore', ['spot' => $this->spot->uuid]))
        ->assertRedirect(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertSessionHas('success', __('ui.spots.feedback.spot_restored'));

    $this->assertNotSoftDeleted($this->spot);
    $this->get(route('spots.redirect', ['spot' => $this->spot->uuid]))->assertRedirect();
});

test('a spot of a deleted organization is not restored alone', function () {
    app(DeleteOrganization::class)->delete($this->organization);

    // Its members do not belong to it anymore, and the administrators are told to restore the organization
    $this->actingAs($this->member)->post(route('spots.restore', ['spot' => $this->spot->uuid]))->assertForbidden();

    $this->actingAs($this->admin)->from(route('spots.index', ['deleted' => 1]))
        ->post(route('spots.restore', ['spot' => $this->spot->uuid]))
        ->assertSessionHasErrors(['restore' => __('ui.spots.feedback.organization_deleted')]);

    $this->assertSoftDeleted($this->spot);
});

test('a spot whose name was taken meanwhile is not restored, with the error on the list', function () {
    $this->spot->delete();
    Spot::factory()->for($this->organization)->create(['name' => 'Harbour']);

    $this->actingAs($this->member)
        ->followingRedirects()
        ->from(route('spots.index', ['deleted' => 1]))
        ->post(route('spots.restore', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSee(e(__('ui.spots.feedback.name_taken', ['name' => 'Harbour', 'organization' => 'Acme'])), false);

    $this->assertSoftDeleted($this->spot);
});

test('only the administrators list and restore the deleted organizations, which come back with their members and the spots deleted with them', function () {
    $alone = Spot::factory()->for($this->organization)->create(['name' => 'Deleted before']);
    $alone->delete();
    $this->travel(1)->minute();
    app(DeleteOrganization::class)->delete($this->organization);

    // The filter is not theirs: by its address, they get none, rather than the organizations that are not deleted
    Organization::factory()->create(['name' => 'Globex'])->users()->attach($this->member);

    $this->actingAs($this->member)->get(route('organizations.index', ['deleted' => 1]))
        ->assertOk()
        ->assertDontSee(__('ui.organizations.index.filter.deleted'))
        ->assertDontSee('Acme')
        ->assertDontSee('Globex')
        ->assertSee(__('ui.organizations.index.no_result'));
    $this->post(route('organizations.restore', ['organization' => $this->organization->uuid]))->assertForbidden();

    $this->actingAs($this->admin)->get(route('organizations.index', ['deleted' => 1]))
        ->assertOk()
        ->assertSee(__('ui.organizations.index.filter.deleted'))
        ->assertSee('Acme')
        ->assertSee(__('ui.organizations.common.deleted'))
        ->assertSee('href="'.route('organizations.show', ['organization' => $this->organization->uuid]).'"', false);

    $this->post(route('organizations.restore', ['organization' => $this->organization->uuid]))
        ->assertRedirect(route('organizations.show', ['organization' => $this->organization->uuid]))
        ->assertSessionHas('success', __('ui.organizations.feedback.organization_restored'));

    $this->assertNotSoftDeleted($this->organization);
    $this->assertNotSoftDeleted($this->spot);
    $this->assertSoftDeleted($alone);
    expect($this->member->fresh()->belongsToOrganization($this->organization))->toBeTrue();
});

test('an organization whose name was taken meanwhile is not restored', function () {
    app(DeleteOrganization::class)->delete($this->organization);
    Organization::factory()->create(['name' => 'Acme']);

    $this->actingAs($this->admin)->from(route('organizations.index', ['deleted' => 1]))
        ->post(route('organizations.restore', ['organization' => $this->organization->uuid]))
        ->assertSessionHasErrors(['restore' => __('ui.organizations.feedback.name_taken', ['name' => 'Acme'])]);

    $this->assertSoftDeleted($this->organization);
});

test('the API lists the deleted ones and restores them, with the deletion ability and the same rules', function () {
    $this->spot->delete();

    Sanctum::actingAs($this->member, ['spots:read', 'spots:delete']);

    $this->getJson('/api/v1/spots?filter[deleted]=1')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $this->spot->uuid)
        ->assertJsonPath('data.0.attributes.deleted_at', fn ($date) => $date !== null);

    $this->postJson('/api/v1/spots/'.$this->spot->uuid.'/restore')
        ->assertOk()
        ->assertJsonPath('data.attributes.deleted_at', null);

    // Without the ability to delete, nor the right to
    $this->spot->delete();
    Sanctum::actingAs($this->member, ['spots:read', 'spots:update']);
    $this->postJson('/api/v1/spots/'.$this->spot->uuid.'/restore')->assertForbidden();
    Sanctum::actingAs($this->outsider, ['spots:delete']);
    $this->postJson('/api/v1/spots/'.$this->spot->uuid.'/restore')->assertForbidden();

    // A name taken meanwhile
    Spot::factory()->for($this->organization)->create(['name' => 'Harbour']);
    Sanctum::actingAs($this->member, ['spots:delete']);
    $this->postJson('/api/v1/spots/'.$this->spot->uuid.'/restore')->assertUnprocessable()->assertJsonValidationErrors('restore');

    // The organizations, for the administrators only
    app(DeleteOrganization::class)->delete($this->organization);

    Sanctum::actingAs($this->member, ['organizations:read', 'organizations:delete']);
    expect($this->getJson('/api/v1/organizations?filter[deleted]=1')->assertOk()->json('data'))->toBeEmpty();
    $this->postJson('/api/v1/organizations/'.$this->organization->uuid.'/restore')->assertForbidden();

    Sanctum::actingAs($this->admin, ['organizations:read', 'organizations:delete']);
    $this->getJson('/api/v1/organizations?filter[deleted]=true')->assertOk()->assertJsonPath('data.0.id', $this->organization->uuid);
    $this->postJson('/api/v1/organizations/'.$this->organization->uuid.'/restore')
        ->assertOk()
        ->assertJsonPath('data.attributes.deleted_at', null);
});

test('a deleted organization keeps its page and its edit page for the administrators, with a badge, its QR code and the button to restore it', function () {
    app(DeleteOrganization::class)->delete($this->organization);

    $show = route('organizations.show', ['organization' => $this->organization->uuid]);
    $edit = route('organizations.edit', ['organization' => $this->organization->uuid]);
    $qrCode = route('organizations.qr-code', ['organization' => $this->organization->uuid, 'format' => 'svg']);

    $this->actingAs($this->admin)->get($show)->assertOk()
        ->assertSee('Acme')
        ->assertSee(__('ui.organizations.common.deleted'))
        ->assertSeeInOrder(['href="'.$edit.'"', __('ui.common.actions.restore')], false)
        ->assertSee($qrCode, false)
        // Its spots come back with it: their number, without their list
        ->assertSee(trans_choice('ui.organizations.show.spots_coming_back', 1))
        ->assertDontSee('id="spots"', false)
        // Its members and invitations are managed again once it is restored
        ->assertDontSee(route('organizations.members.index', ['organization' => $this->organization->uuid]), false)
        // Its spots come back with it only: not offered alone
        ->assertDontSee(__('ui.spots.index.filter.deleted'));

    $this->get($qrCode)->assertOk();

    $this->get($edit)->assertOk()
        ->assertSee(e(__('ui.organizations.edit.deleted', ['date' => $this->organization->fresh()->deleted_at->isoFormat('LL')])), false)
        ->assertSee('action="'.route('organizations.restore', ['organization' => $this->organization->uuid]).'"', false)
        ->assertDontSee('action="'.route('organizations.update', $this->organization).'"', false)
        ->assertDontSee('id="danger-title"', false);

    // Not found for its members, nor the others
    foreach ([$this->member, $this->outsider] as $user) {
        $this->actingAs($user)->get($show)->assertGone()->assertSee(e(__('ui.organizations.removed.title')), false);
        $this->get($edit)->assertNotFound();
    }

    auth()->logout();
    $this->get($show)->assertGone();
});

test('a name taken meanwhile is shown on the edit page of the deleted organization', function () {
    app(DeleteOrganization::class)->delete($this->organization);
    Organization::factory()->create(['name' => 'Acme']);

    $this->actingAs($this->admin)
        ->followingRedirects()
        ->from(route('organizations.edit', ['organization' => $this->organization->uuid]))
        ->post(route('organizations.restore', ['organization' => $this->organization->uuid]))
        ->assertOk()
        ->assertSee(e(__('ui.organizations.feedback.name_taken', ['name' => 'Acme'])), false);
});

test('the message of a deletion offers to undo it at once', function () {
    $this->actingAs($this->member)
        ->followingRedirects()
        ->delete(route('spots.destroy', $this->spot))
        ->assertOk()
        ->assertSeeInOrder([__('ui.spots.feedback.spot_deleted'), 'form="undo-form"', __('ui.common.actions.undo'), 'id="undo-form"', 'action="'.route('spots.restore', ['spot' => $this->spot->uuid]).'"'], false);

    $this->post(route('spots.restore', ['spot' => $this->spot->uuid]))->assertRedirect(route('spots.show', ['spot' => $this->spot->uuid]));
    $this->assertNotSoftDeleted($this->spot);

    $this->actingAs($this->admin)
        ->followingRedirects()
        ->delete(route('organizations.destroy', $this->organization))
        ->assertOk()
        ->assertSeeInOrder([__('ui.organizations.feedback.organization_deleted'), 'form="undo-form"', __('ui.common.actions.undo'), 'id="undo-form"', 'action="'.route('organizations.restore', ['organization' => $this->organization->uuid]).'"'], false);

    // Once: the next pages do not offer it anymore
    $this->get(route('organizations.index'))->assertDontSee(__('ui.common.actions.undo'));
});

test('the deletion warnings tell how to restore', function () {
    expect(__('ui.spots.delete.warning'))->toContain('restored if needed from its edit page')
        ->and(__('ui.organizations.delete.warning'))->toContain('restored if needed from its edit page');
});

test('the buttons of the alerts are in them, their forms out of them, as a paragraph cannot hold a form', function () {
    // The alert, a paragraph, holds its button; a form in it would be pushed out of it by the browser
    $inAlert = function (string $html, string $form) {
        preg_match_all('/<p role="[^"]*" data-alert[^>]*>.*?<\/p>/s', $html, $alerts);

        $alert = collect($alerts[0])->first(fn (string $alert) => str_contains($alert, 'form="'.$form.'"'));

        return $alert !== null && ! str_contains($alert, '<form') && str_contains($html, '<form id="'.$form.'"');
    };

    $html = $this->actingAs($this->member)->followingRedirects()->delete(route('spots.destroy', $this->spot))->getContent();
    expect($inAlert($html, 'undo-form'))->toBeTrue();

    expect($inAlert($this->get(route('spots.edit', ['spot' => $this->spot->uuid]))->getContent(), 'restore-form'))->toBeTrue();

    app(DeleteOrganization::class)->delete($this->organization);
    expect($inAlert($this->actingAs($this->admin)->get(route('organizations.edit', ['organization' => $this->organization->uuid]))->getContent(), 'restore-form'))->toBeTrue();
});

test('a deleted organization counts the spots that come back with it, in its list, on its page and in the API', function () {
    Spot::factory()->for($this->organization)->create(['name' => 'Lighthouse']);
    $before = Spot::factory()->for($this->organization)->create(['name' => 'Deleted before']);
    $before->delete();
    $this->travel(1)->minute();
    app(DeleteOrganization::class)->delete($this->organization);

    // Harbour and Lighthouse, not the one deleted before
    $this->actingAs($this->admin)->get(route('organizations.index', ['deleted' => 1]))
        ->assertOk()
        ->assertSee(trans_choice('ui.organizations.common.spots', 2));

    $this->get(route('organizations.show', ['organization' => $this->organization->uuid]))
        ->assertSee(trans_choice('ui.organizations.show.spots_coming_back', 2));

    Sanctum::actingAs($this->admin, ['organizations:read']);
    $this->getJson('/api/v1/organizations?filter[deleted]=1')->assertJsonPath('data.0.attributes.spots_count', 2);
    $this->getJson('/api/v1/organizations/'.$this->organization->uuid)->assertJsonPath('data.attributes.spots_count', 2);
});
