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

test('the contact form sends the message to the administrators, who reply to its author', function () {
    Notification::fake();
    $admins = User::factory()->admin()->count(2)->create();
    $user = User::factory()->create();

    $this->post(route('contact.store'), contactData())
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('contact'))
        ->assertSessionHas('success', __('ui.contact.feedback.sent'));

    Notification::assertSentTo($admins, ContactNotification::class, function (ContactNotification $notification, array $channels, User $notifiable) {
        $mail = $notification->toMail($notifiable);

        return $notification->email === 'ada@example.ch'
            && $mail->replyTo === [['ada@example.ch', 'Ada Lovelace']]
            && in_array('Nous aimerions utiliser Spot in®.', $mail->introLines, true);
    });
    Notification::assertNotSentTo($user, ContactNotification::class);
});

test('the contact page shows the form, and the success message once sent, in every language', function (string $locale) {
    $this->withUnencryptedCookie('locale', $locale)
        ->withSession(['success' => 'Sent!'])
        ->get(route('contact'))
        ->assertOk()
        ->assertDontSee('ui.')
        ->assertSee('<title>'.e(__('ui.contact.title', locale: $locale)).' - '.e(config('app.name')).'</title>', false)
        ->assertSee('action="'.route('contact.store').'"', false)
        ->assertSee('Sent!');
})->with(['en', 'fr']);

test('validation errors are shown on the contact form', function () {
    $this->followingRedirects()
        ->from(route('contact'))
        ->post(route('contact.store'), contactData(['email' => 'not-an-email']))
        ->assertOk()
        ->assertSee('aria-invalid="true"', false)
        ->assertSee('value="Ada Lovelace"', false);
});

test('the contact form is validated, and goes back to the form with its errors', function (array $data, string $field) {
    Notification::fake();

    $this->from(route('contact'))
        ->post(route('contact.store'), contactData($data))
        ->assertRedirect(route('contact'))
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
