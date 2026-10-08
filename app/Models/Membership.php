<?php

namespace App\Models;

use App\Enums\OrganizationRole;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * The membership of a user in an organization.
 *
 * @property OrganizationRole $role
 */
class Membership extends Pivot
{
    protected $table = 'memberships';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => OrganizationRole::class,
        ];
    }
}
