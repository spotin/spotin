<?php

namespace App\Actions\Spots;

use App\Models\Organization;
use App\Models\Spot;
use App\Models\User;

class CreateSpot
{
    /**
     * Create a spot in the organization, on behalf of the user who becomes its creator.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function create(Organization $organization, array $attributes, User $creator): Spot
    {
        $spot = new Spot($attributes);
        $spot->organization()->associate($organization);
        $spot->creator()->associate($creator);
        $spot->save();

        return $spot;
    }
}
