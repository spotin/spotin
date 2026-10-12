<?php

namespace App\Actions\Organizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RemoveMember
{
    /**
     * Remove the user from the organization, whether they are leaving or being removed.
     *
     * An organization always keeps at least one responsible user.
     *
     * @throws ValidationException
     */
    public function remove(Organization $organization, User $user): void
    {
        DB::transaction(function () use ($organization, $user): void {
            // Lock the organization so two concurrent removals cannot both pass the check below.
            Organization::whereKey($organization->id)->lockForUpdate()->first();

            if (! $user->belongsToOrganization($organization)) {
                throw ValidationException::withMessages(['member' => __('ui.organizations.members.index.table.feedback.not_a_member')]);
            }

            if ($organization->isLastResponsible($user)) {
                throw ValidationException::withMessages(['member' => __('ui.organizations.members.index.table.feedback.last_responsible')]);
            }

            $organization->users()->detach($user);
        });
    }
}
