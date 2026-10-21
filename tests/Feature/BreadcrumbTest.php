<?php

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

beforeEach(function () {
    $this->organization = Organization::factory()->create(['name' => 'Acme']);
    $this->admin = User::factory()->admin()->create();
    $this->responsible = User::factory()->create();
    $this->organization->users()->attach($this->responsible, ['role' => OrganizationRole::Responsible]);

    $this->orgUrl = '/organizations/'.$this->organization->uuid;
});

/**
 * The text of every crumb of the trail, in order.
 *
 * @return list<string>
 */
function crumbs(string $html): array
{
    preg_match('/<nav aria-label="[^"]*" class="breadcrumbs[^"]*">(.*?)<\/nav>/s', $html, $nav);
    preg_match_all('/<li>\s*(.*?)\s*<\/li>/s', $nav[1] ?? '', $items);

    return array_map(fn (string $item) => trim(strip_tags($item)), $items[1]);
}

test('organization pages show a trail starting at Organizations', function (string $path, array $expected) {
    $html = $this->actingAs($this->responsible)->get($this->orgUrl.$path)->assertOk()->getContent();

    expect(crumbs($html))->toBe($expected);
})->with([
    'overview' => ['', ['Organizations', 'Acme']],
    'members' => ['/members', ['Organizations', 'Acme', 'Members']],
    'invitations' => ['/invitations', ['Organizations', 'Acme', 'Invitations']],
    'invite someone' => ['/invitations/create', ['Organizations', 'Acme', 'Invitations', 'Invite someone']],
]);

test('guests see no trail on the public pages', function () {
    $spot = Spot::factory()->for($this->organization)->create();

    foreach ([$this->orgUrl, route('spots.show', ['spot' => $spot->uuid])] as $url) {
        $this->get($url)->assertOk()->assertDontSee('class="breadcrumbs', false);
    }

    // The same pages show it once logged in
    $this->actingAs($this->responsible)->get($this->orgUrl)->assertSee('class="breadcrumbs', false);
});

test('the crumbs link to their page and the last one is the current page', function () {
    $html = $this->actingAs($this->responsible)->get($this->orgUrl.'/invitations/create')->getContent();

    preg_match('/<nav aria-label="[^"]*" class="breadcrumbs[^"]*">(.*?)<\/nav>/s', $html, $nav);

    expect($nav[1])
        ->toContain('<a href="'.url('/organizations').'">Organizations</a>')
        ->toContain('<a href="'.url($this->orgUrl).'">Acme</a>')
        ->toContain('<a href="'.url($this->orgUrl.'/invitations').'">Invitations</a>')
        ->toMatch('/<span\s+aria-current="page"[^>]*>Invite someone<\/span>/')
        ->not->toContain('<a href="'.url($this->orgUrl.'/invitations/create').'">');
});

test('an administrator who is not a member reaches an organization through the public list', function () {
    $html = $this->actingAs($this->admin)->get($this->orgUrl.'/members')->assertOk()->getContent();

    expect(crumbs($html))->toBe(['Organizations', 'Acme', 'Members']);
});

test('the organization management pages show their trail', function (string $path, array $expected) {
    $html = $this->actingAs($this->admin)->get('/organizations'.$path)->assertOk()->getContent();

    expect(crumbs($html))->toBe($expected);
})->with([
    'new' => ['/create', ['Organizations', 'New organization']],
]);

test('the edit page of an organization shows its trail, linking back to the organization', function () {
    $html = $this->actingAs($this->admin)->get('/organizations/'.$this->organization->uuid.'/edit')->assertOk()->getContent();

    expect($html)->toContain('href="'.route('organizations.show', ['organization' => $this->organization->uuid]).'"');

    expect(crumbs($html))->toBe(['Organizations', 'Acme', 'Edit the organization']);
});

test('the trail is translated', function () {
    $html = $this->withUnencryptedCookie('locale', 'fr')->actingAs($this->responsible)->get($this->orgUrl.'/members')->getContent();

    expect(crumbs($html))->toBe(['Organisations', 'Acme', 'Membres']);
    $this->assertStringContainsString('aria-label="'.e(__('ui.common.breadcrumbs', [], 'fr')).'"', $html);
});

test('pages without hierarchy have no trail', function (string $url, bool $authenticated) {
    if ($authenticated) {
        $this->actingAs($this->responsible);
    }

    $this->get($url)->assertOk()->assertDontSee('class="breadcrumbs', false);
})->with([
    'home' => ['/', false],
    'login' => ['/auth/login', false],
    'register' => ['/auth/register', false],
    'dashboard' => ['/dashboard', true],
    'my organizations' => ['/organizations?mine=1', true],
]);

test('settings pages show a trail starting at Settings', function (string $path, array $expected) {
    $html = $this->actingAs($this->responsible)->get($path)->assertOk()->getContent();

    expect(crumbs($html))->toBe($expected);
})->with([
    'profile' => ['/settings/profile', ['Settings', 'Profile settings']],
    'security' => ['/settings/security', ['Settings', 'Security settings']],
]);
