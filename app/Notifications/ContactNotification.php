<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * A message of the contact form of the home page, sent to the administrators of the platform. Replying to it answers the
 * person who wrote.
 */
class ContactNotification extends Notification
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $organization,
        public readonly string $message,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('ui.contact.mail.subject', ['organization' => $this->organization]))
            ->replyTo($this->email, $this->name)
            ->line(__('ui.contact.mail.from', ['name' => $this->name, 'email' => $this->email]))
            ->line(__('ui.contact.mail.organization', ['organization' => $this->organization]))
            ->line($this->message)
            ->line(__('ui.contact.mail.reply'));
    }
}
