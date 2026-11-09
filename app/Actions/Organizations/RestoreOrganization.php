<?php

namespace App\Actions\Organizations;

use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RestoreOrganization
{
    /**
     * Restore the organization with the spots deleted with it, and its members, who never left it. The spots deleted
     * before it stay deleted, as its pending invitations, which were deleted for good.
     *
     * Its name may have been taken meanwhile, as the names are unique among the organizations that are not deleted only.
     *
     * @throws ValidationException
     */
    public function restore(Organization $organization): void
    {
        if (Organization::where('name', $organization->name)->exists()) {
            throw ValidationException::withMessages(['restore' => __('ui.organizations.feedback.name_taken', ['name' => $organization->name])]);
        }

        DB::transaction(function () use ($organization): void {
            $organization->spotsDeletedWithIt()->restore();
            $organization->restore();
        });
    }
}
