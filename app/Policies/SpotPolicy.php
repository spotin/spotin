<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

class SpotPolicy
{
    /**
     * Platform administrators can do everything with spots.
     */
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Determine whether the user can see who created the spot.
     */
    public function viewCreator(User $user, Spot $spot): bool
    {
        return $user->belongsToOrganization($spot->organization);
    }

    /**
     * Determine whether the user can create spots in the organization.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $user->belongsToOrganization($organization);
    }

    /**
     * Determine whether the user can edit the spot.
     */
    public function update(User $user, Spot $spot): bool
    {
        return $user->belongsToOrganization($spot->organization);
    }

    /**
     * Determine whether the user can delete the spot.
     */
    public function delete(User $user, Spot $spot): bool
    {
        return $user->belongsToOrganization($spot->organization);
    }
}
