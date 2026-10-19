<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent on demand to the invited email address, which may not have an account yet.
 *
 * It is deliberately not queued, so the plain token never ends up in the jobs table.
 */
class InvitationNotification extends Notification
{
    public function __construct(
        protected Invitation $invitation,
        protected string $token,
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
        $organization = $this->invitation->organization->name;

        return (new MailMessage)
            ->subject(__('ui.organizations.invitations.common.mail.subject', ['organization' => $organization]))
            ->line(__('ui.organizations.invitations.common.mail.line', [
                'organization' => $organization,
                'role' => __('ui.organizations.common.forms.fields.role.options.'.$this->invitation->role->value),
            ]))
            ->action(__('ui.organizations.invitations.common.mail.action'), route('invitations.show', ['token' => $this->token]))
            ->line(__('ui.organizations.invitations.common.mail.expires', [
                'date' => $this->invitation->expires_at->isoFormat('LLL'),
            ]))
            ->line(__('ui.organizations.invitations.common.mail.ignore'));
    }
}
