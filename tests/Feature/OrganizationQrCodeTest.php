<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use App\Support\QrCode;

beforeEach(function () {
    $this->organization = Organization::factory()->create(['name' => 'Acme Corp']);

    $this->admin = User::factory()->admin()->create();
    $this->responsible = User::factory()->create();
    $this->member = User::factory()->create();
    $this->outsider = User::factory()->create();

    $this->organization->users()->attach($this->responsible, ['role' => OrganizationRole::Responsible]);
    $this->organization->users()->attach($this->member, ['role' => OrganizationRole::Member]);
});

test('the managers of an organization download its QR code as SVG and PNG, named after it', function (string $user) {
    $this->actingAs($this->$user);

    $svg = $this->get(route('organizations.qr-code', ['organization' => $this->organization->uuid, 'format' => 'svg']))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/svg+xml')
        ->assertDownload('acme-corp-qr-code.svg')
        ->getContent();

    // The QR code of the scan address of the organization
    expect($svg)->toBe(QrCode::svg(route('organizations.redirect', ['organization' => $this->organization->uuid])));

    $png = $this->get(route('organizations.qr-code', ['organization' => $this->organization->uuid, 'format' => 'png']))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertDownload('acme-corp-qr-code.png')
        ->getContent();

    expect(getimagesizefromstring($png))->toMatchArray([0 => 1024, 1 => 1024, 'mime' => 'image/png']);
})->with(['responsible', 'admin']);

test('only the managers of an organization download its QR code, in the formats offered', function () {
    $svg = route('organizations.qr-code', ['organization' => $this->organization->uuid, 'format' => 'svg']);

    $this->get($svg)->assertRedirect(route('login'));
    $this->actingAs($this->outsider)->get($svg)->assertForbidden();
    $this->actingAs($this->member)->get($svg)->assertForbidden();
    $this->actingAs($this->responsible)->get('/organizations/'.$this->organization->uuid.'/qr-code.gif')->assertNotFound();
});

test('the address of the QR codes of the organizations never changes, as printed codes lead to it', function () {
    // Written as is, not with route(): renaming the route must fail here, not break the codes already printed
    $address = '/organizations/'.$this->organization->uuid.'/redirect';

    expect(route('organizations.redirect', ['organization' => $this->organization->uuid], absolute: false))->toBe($address);

    $this->get($address)->assertRedirect(route('organizations.show', ['organization' => $this->organization->uuid]));
});

test('the page of an organization offers its QR code and its scan link to its managers only', function () {
    $url = route('organizations.show', ['organization' => $this->organization->uuid]);
    $redirect = route('organizations.redirect', ['organization' => $this->organization->uuid]);

    $this->actingAs($this->responsible)->get($url)
        ->assertOk()
        ->assertSeeInOrder([
            'popovertarget="qr-code-popover"',
            route('organizations.qr-code', ['organization' => $this->organization->uuid, 'format' => 'svg']),
            route('organizations.qr-code', ['organization' => $this->organization->uuid, 'format' => 'png']),
            'href="'.$redirect.'" target="_blank"',
            'value="'.$redirect.'"',
            __('ui.common.qr_code.hint'),
        ], false);

    foreach ([$this->member, $this->outsider] as $user) {
        $this->actingAs($user)->get($url)->assertOk()->assertDontSee('qr-code-popover', false);
    }
});
