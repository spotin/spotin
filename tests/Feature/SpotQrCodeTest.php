<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;
use App\Support\SpotQrCode;

beforeEach(function () {
    $this->organization = Organization::factory()->create();

    $this->admin = User::factory()->admin()->create();
    $this->member = User::factory()->create();
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);

    $this->spot = Spot::factory()->for($this->organization)->create(['name' => 'Harbour']);
});

test('the managers of a spot download its QR code as SVG and PNG, named after the spot', function (string $user) {
    $this->actingAs($this->$user);

    $svg = $this->get(route('spots.qr-code', ['spot' => $this->spot->uuid, 'format' => 'svg']))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/svg+xml')
        ->assertDownload('harbour-qr-code.svg')
        ->getContent();

    // The QR code of the scan address of the spot
    expect($svg)->toContain('<svg')->toBe(SpotQrCode::svg($this->spot));

    $png = $this->get(route('spots.qr-code', ['spot' => $this->spot->uuid, 'format' => 'png']))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertDownload('harbour-qr-code.png')
        ->getContent();

    expect(getimagesizefromstring($png))->toMatchArray([0 => 1024, 1 => 1024, 'mime' => 'image/png']);
})->with(['member', 'admin']);

test('the QR code of a spot that needs to be set up is named after the start of its UUID', function () {
    $spot = Spot::factory()->for($this->organization)->needingSetup()->create(['name' => null]);

    $this->actingAs($this->member)
        ->get(route('spots.qr-code', ['spot' => $spot->uuid, 'format' => 'png']))
        ->assertOk()
        ->assertDownload('unnamed-spot-'.substr($spot->uuid, 0, 6).'-qr-code.png');
});

test('only the managers of a spot download its QR code, in the formats offered', function () {
    $svg = route('spots.qr-code', ['spot' => $this->spot->uuid, 'format' => 'svg']);

    $this->get($svg)->assertRedirect(route('login'));
    $this->actingAs($this->outsider)->get($svg)->assertForbidden();
    $this->actingAs($this->member)->get('/spots/'.$this->spot->uuid.'/qr-code.gif')->assertNotFound();
});

test('the address of the QR codes never changes, as printed codes lead to it', function () {
    // Written as is, not with route(): renaming the route must fail here, not break the codes already printed
    $address = '/spots/'.$this->spot->uuid.'/redirect';

    expect(route('spots.redirect', ['spot' => $this->spot->uuid], absolute: false))->toBe($address);

    $this->get($address)->assertRedirect(route('spots.show', ['spot' => $this->spot->uuid]));
});

test('the page of a spot offers its QR code and its scan link to its managers only', function () {
    $redirect = route('spots.redirect', ['spot' => $this->spot->uuid]);
    $png = route('spots.qr-code', ['spot' => $this->spot->uuid, 'format' => 'png']);

    $this->actingAs($this->member)
        ->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertSeeInOrder([
            'popovertarget="qr-code-popover"',
            route('spots.qr-code', ['spot' => $this->spot->uuid, 'format' => 'svg']),
            $png,
            'href="'.$redirect.'" target="_blank"',
            'value="'.$redirect.'"',
            'data-copy="scan-link"',
        ], false);

    $this->actingAs($this->outsider)
        ->get(route('spots.show', ['spot' => $this->spot->uuid]))
        ->assertOk()
        ->assertDontSee('qr-code-popover', false)
        ->assertDontSee($png, false);
});
