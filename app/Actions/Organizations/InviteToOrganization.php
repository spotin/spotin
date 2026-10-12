<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use App\Notifications\OrganizationInvitationNotification;
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
    public function invite(Organization $organization, string $email, OrganizationRole $role, User $inviter): OrganizationInvitation
    {
        $alreadyMember = $organization->users()->where('users.email', $email)->exists();

        if ($alreadyMember) {
            throw ValidationException::withMessages(['email' => __('ui.organizations.invitations.feedback.already-member')]);
        }

        $token = OrganizationInvitation::generateToken();

        $invitation = $organization->invitations()->updateOrCreate(
            ['email' => $email],
            [
                'role' => $role,
                'token' => OrganizationInvitation::hashToken($token),
                'invited_by' => $inviter->id,
                'expires_at' => now()->addDays(config('organizations.invitation_lifetime_days')),
            ],
        );

        Notification::route('mail', $email)->notify(new OrganizationInvitationNotification($invitation, $token));

        return $invitation;
    }

    /**
     * Send a pending invitation again, with a new token and a new expiry.
     *
     * The plain token cannot be recovered from its hash, so a new one is issued.
     */
    public function resend(OrganizationInvitation $invitation, User $inviter): OrganizationInvitation
    {
        return $this->invite($invitation->organization, $invitation->email, $invitation->role, $inviter);
    }
}
