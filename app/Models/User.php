<?php

namespace App\Models;

use App\Enums\OrganizationRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The model's default attribute values, matching the database defaults.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_admin' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * The email address, always stored in lowercase.
     *
     * @return Attribute<string, string>
     */
    protected function email(): Attribute
    {
        return Attribute::set(fn (string $value) => Str::lower($value));
    }

    /**
     * The organizations the user belongs to, with their role.
     *
     * @return BelongsToMany<Organization, $this, Membership>
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'memberships')
            ->using(Membership::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Determine whether the user is a platform administrator.
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Determine whether the user has the given role in the organization.
     */
    public function hasRoleIn(Organization $organization, OrganizationRole $role): bool
    {
        return $this->organizations()
            ->whereKey($organization->getKey())
            ->wherePivot('role', $role->value)
            ->exists();
    }

    /**
     * Determine whether the user is responsible of the organization.
     */
    public function isResponsibleOf(Organization $organization): bool
    {
        return $this->hasRoleIn($organization, OrganizationRole::Responsible);
    }

    /**
     * Determine whether the user belongs to the organization, whatever their role.
     */
    public function belongsToOrganization(Organization $organization): bool
    {
        return $this->organizations()->whereKey($organization->getKey())->exists();
    }
}
