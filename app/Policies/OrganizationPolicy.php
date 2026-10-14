<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    /**
     * Platform administrators can do everything with organizations, except leaving
     * one they do not belong to.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($ability !== 'leave' && $user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether anyone, guests included, can view the organization and its spots.
     */
    public function view(?User $user, Organization $organization): bool
    {
        return true;
    }

    /**
     * Determine whether the user can see the members of the organization.
     */
    public function viewMembers(User $user, Organization $organization): bool
    {
        return $user->belongsToOrganization($organization);
    }

    /**
     * Determine whether the user can create organizations.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can edit the organization.
     */
    public function update(User $user, Organization $organization): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the organization.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return false;
    }

    /**
     * Determine whether the user can invite people to the organization, revoke
     * invitations, and change or remove members.
     */
    public function manageMembers(User $user, Organization $organization): bool
    {
        return $user->isResponsibleOf($organization);
    }

    /**
     * Determine whether the user can leave the organization.
     */
    public function leave(User $user, Organization $organization): bool
    {
        return $user->belongsToOrganization($organization);
    }
}
