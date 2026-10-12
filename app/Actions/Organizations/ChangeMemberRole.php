<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationRole;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangeMemberRole
{
    /**
     * Change the role of a user in the organization.
     *
     * An organization always keeps at least one responsible user.
     *
     * @throws ValidationException
     */
    public function change(Organization $organization, User $user, OrganizationRole $role): void
    {
        DB::transaction(function () use ($organization, $user, $role): void {
            // Lock the organization so two concurrent changes cannot both pass the check below.
            Organization::whereKey($organization->id)->lockForUpdate()->first();

            if (! $user->belongsToOrganization($organization)) {
                throw ValidationException::withMessages(['member' => __('ui.organizations.members.feedback.not-a-member')]);
            }

            if ($role !== OrganizationRole::Responsible && $organization->isLastResponsible($user)) {
                throw ValidationException::withMessages(['member' => __('ui.organizations.members.feedback.last-responsible')]);
            }

            $organization->users()->updateExistingPivot($user->id, ['role' => $role]);
        });
    }
}
