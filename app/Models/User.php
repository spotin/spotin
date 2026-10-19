<?php

namespace App\Models;

use App\Enums\OrganizationRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasVersion4Uuids, Notifiable;

    /**
     * The model's default attribute values, matching the database defaults.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_admin' => false,
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * The UUID is the public identifier in the API, the integer primary key stays internal.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

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
     * Determine whether the user belongs to the organization, whatever their role. Uses the organizations of the
     * user when they are loaded, to check many organizations without a query each.
     */
    public function belongsToOrganization(Organization|int $organization): bool
    {
        $id = $organization instanceof Organization ? $organization->getKey() : $organization;

        return $this->relationLoaded('organizations')
            ? $this->organizations->contains($id)
            : $this->organizations()->whereKey($id)->exists();
    }

    /**
     * The initials of the user, shown in their avatar.
     */
    public function initials(): string
    {
        return Str::of($this->name)->words(2, '')->initials(capitalize: true)->toString();
    }
}
