<?php

use App\Models\User;
use App\Notifications\ContactNotification;
use Illuminate\Support\Facades\Notification;

function contactData(array $overrides = []): array
{
    return [
        'name' => 'Ada Lovelace',
        'email' => 'Ada@Example.ch',
        'organization' => 'Commune de Lausanne',
        'message' => 'Nous aimerions utiliser Spot in®.',
        ...$overrides,
    ];
}

test('the home page renders with its title and description', function () {
    $this->get(route('index'))
        ->assertOk()
        ->assertSee('<title>'.e(__('ui.homepage.title')).' - '.e(config('app.name')).'</title>', false)
        ->assertSee('<meta name="description" content="'.e(__('ui.homepage.description')).'">', false)
        ->assertSee(e(config('app.name')), false)
        ->assertSee('2021-'.date('Y'), false);
});

test('the home page shows all the spots on a map, and invites guests to explore the platform and to log in or register, in every language', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)->get(route('index'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('data-map-spots="'.route('spots.map').'"', false)
        ->assertSee(route('spots.index'), false)
        ->assertSee(route('organizations.index'), false)
        ->assertSee(route('login'), false)
        ->assertSee(route('register'), false)
        ->assertDontSee(route('dashboard'), false);
})->with(['en', 'fr']);

test('the home page sends logged-in users to their dashboard', function () {
    $this->actingAs(User::factory()->create())->get(route('index'))
        ->assertOk()
        ->assertSee(route('dashboard'), false)
        ->assertDontSee(route('register'), false);
});

test('the home page is its hero, then a wide column for its sections', function () {
    $html = $this->get(route('index'))->assertOk()->getContent();

    expect($html)->toContain('class="hero ')
        ->not->toContain('min-h-[28rem] flex-grow')
        ->toContain('max-w-6xl');

    // The other pages keep their narrow column
    $this->get(route('spots.index'))->assertOk()->assertSee('max-w-2xl', false)->assertDontSee('max-w-6xl', false);
});

test('the home page presents Spot in® to public institutions, with its advantages and how it works', function () {
    $response = $this->get('/')->assertOk()->assertSee(__('ui.homepage.title'));

    foreach (['lasting', 'content', 'governance'] as $advantage) {
        $response->assertSee(__("ui.homepage.advantages.$advantage.title"));
    }

    foreach (['organization', 'members', 'spots', 'print', 'evolve'] as $step) {
        $response->assertSee(__("ui.homepage.workflow.$step.title"));
    }

    $response->assertSee('href="#contact"', false)->assertSee('id="contact"', false);
});

test('the contact form sends the message to the administrators, who reply to its author', function () {
    Notification::fake();
    $admins = User::factory()->admin()->count(2)->create();
    $user = User::factory()->create();

    $this->post(route('contact.store'), contactData())
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('index').'#contact')
        ->assertSessionHas('success', __('ui.contact.feedback.sent'));

    Notification::assertSentTo($admins, ContactNotification::class, function (ContactNotification $notification, array $channels, User $notifiable) {
        $mail = $notification->toMail($notifiable);

        return $notification->email === 'ada@example.ch'
            && $mail->replyTo === [['ada@example.ch', 'Ada Lovelace']]
            && in_array('Nous aimerions utiliser Spot in®.', $mail->introLines, true);
    });
    Notification::assertNotSentTo($user, ContactNotification::class);
});

test('the contact form shows the success message next to the form', function () {
    $this->withSession(['success' => __('ui.contact.feedback.sent')])
        ->get('/')
        ->assertSeeInOrder(['id="contact"', __('ui.contact.feedback.sent')], false);
});

test('the contact form is validated, and goes back to the form with its errors', function (array $data, string $field) {
    Notification::fake();

    $this->post(route('contact.store'), contactData($data))
        ->assertRedirect(route('index').'#contact')
        ->assertSessionHasErrors($field);

    Notification::assertNothingSent();
})->with([
    'missing name' => [['name' => ''], 'name'],
    'invalid email' => [['email' => 'not-an-email'], 'email'],
    'missing organization' => [['organization' => ''], 'organization'],
    'missing message' => [['message' => ''], 'message'],
    'too long message' => [['message' => str_repeat('a', 5001)], 'message'],
    'filled honeypot' => [['website' => 'https://spam.example.com'], 'website'],
]);

test('the contact form only accepts a few messages per hour', function () {
    Notification::fake();
    $admin = User::factory()->admin()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('contact.store'), contactData())->assertRedirect();
    }

    $this->post(route('contact.store'), contactData())->assertTooManyRequests();

    Notification::assertSentToTimes($admin, ContactNotification::class, 5);
});
