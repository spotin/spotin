<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AcceptInvitation
{
    /**
     * Add the user to the organization the plain token invites to.
     *
     * The invitation must be active and sent to the user's verified email address.
     * It is deleted once accepted, in the same transaction as the membership.
     *
     * @throws ValidationException
     */
    public function accept(string $token, User $user): Organization
    {
        return DB::transaction(fn (): Organization => $this->join(
            OrganizationInvitation::active()
                ->where('token', OrganizationInvitation::hashToken($token))
                ->lockForUpdate()
                ->with('organization')
                ->first(),
            $user,
        ));
    }

    /**
     * Accept an invitation the user picked from their pending invitations.
     *
     * @throws ValidationException
     */
    public function acceptInvitation(OrganizationInvitation $invitation, User $user): Organization
    {
        return DB::transaction(fn (): Organization => $this->join(
            OrganizationInvitation::active()
                ->whereKey($invitation->getKey())
                ->lockForUpdate()
                ->with('organization')
                ->first(),
            $user,
        ));
    }

    /**
     * Add the user to the organization of the invitation, then delete the invitation.
     *
     * @throws ValidationException
     */
    private function join(?OrganizationInvitation $invitation, User $user): Organization
    {
        // The same message for an unknown, expired or foreign invitation, so the
        // page never reveals who an invitation was sent to.
        if (! $invitation || ! $invitation->isFor($user)) {
            throw ValidationException::withMessages(['invitation' => __('ui.organizations.invitations.feedback.invalid-invitation')]);
        }

        $organization = $invitation->organization;
        $membership = $organization->users()->whereKey($user->id)->first()?->pivot;

        if (! $membership) {
            $organization->users()->attach($user, ['role' => $invitation->role]);
        } elseif ($invitation->role === OrganizationRole::Responsible && $membership->role !== OrganizationRole::Responsible) {
            // An invitation can promote an existing member, never demote one.
            $organization->users()->updateExistingPivot($user->id, ['role' => $invitation->role]);
        }

        $invitation->delete();

        return $organization;
    }
}
