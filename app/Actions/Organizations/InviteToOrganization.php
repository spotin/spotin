<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationRole;
use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\InvitationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InviteToOrganization
{
    /**
     * Invite an email address to join the organization.
     *
     * Inviting an address that already has a pending invitation replaces that
     * invitation, which also acts as resending it.
     *
     * @throws ValidationException
     */
    public function invite(Organization $organization, string $email, OrganizationRole $role, User $inviter): Invitation
    {
        $alreadyMember = $organization->users()->where('users.email', $email)->exists();

        if ($alreadyMember) {
            throw ValidationException::withMessages(['email' => __('ui.organizations.invitations.feedback.already_member')]);
        }

        $token = Invitation::generateToken();

        $invitation = $organization->invitations()->updateOrCreate(
            ['email' => $email],
            [
                'role' => $role,
                'token' => Invitation::hashToken($token),
                'invited_by' => $inviter->id,
                'expires_at' => now()->addDays(config('organizations.invitation_lifetime_days')),
            ],
        );

        Notification::route('mail', $email)->notify(new InvitationNotification($invitation, $token));

        return $invitation;
    }

    /**
     * Send a pending invitation again, with a new token and a new expiry.
     *
     * The plain token cannot be recovered from its hash, so a new one is issued.
     */
    public function resend(Invitation $invitation, User $inviter): Invitation
    {
        return $this->invite($invitation->organization, $invitation->email, $invitation->role, $inviter);
    }
}
