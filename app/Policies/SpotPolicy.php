<?php

namespace App\Policies;

use App\Models\Spot;
use App\Models\User;

class SpotPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Spot $spot): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Spot $spot): bool
    {
        return $user->id === $spot->user_id;
    }

    public function delete(User $user, Spot $spot): bool
    {
        return $user->id === $spot->user_id;
    }
}
