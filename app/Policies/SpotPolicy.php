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
        return $user->belongsToOrganization($spot->organization_id);
    }

    /**
     * Determine whether the user can create spots in the organization.
     */
    public function create(User $user, Organization $organization): bool
    {
        return $user->belongsToOrganization($organization);
    }

    /**
     * Determine whether the user can edit the spot: anyone, guests included, while it needs to be set up, as the first
     * person who scans it sets it up. Only who can manage it afterwards.
     */
    public function update(?User $user, Spot $spot): bool
    {
        return $spot->needs_setup || ($user !== null && $this->manage($user, $spot));
    }

    /**
     * Determine whether the user can manage the spot: edit all of it, including its visibility and whether it needs to
     * be set up, rather than only set it up. With a verified email, as on the other pages of the members: otherwise,
     * the user only sets up the spots that need it, as anyone.
     */
    public function manage(User $user, Spot $spot): bool
    {
        return $user->hasVerifiedEmail() && $user->belongsToOrganization($spot->organization_id);
    }

    /**
     * Determine whether the user can delete the spot.
     */
    public function delete(User $user, Spot $spot): bool
    {
        return $user->belongsToOrganization($spot->organization_id);
    }
}
