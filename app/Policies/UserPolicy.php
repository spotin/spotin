<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can see all the users of the platform: administrators only.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can make users administrators, or no longer: administrators only.
     */
    public function updateRole(User $user): bool
    {
        return $user->isAdmin();
    }
}
