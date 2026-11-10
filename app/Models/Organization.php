<?php

namespace App\Models;

use App\Enums\OrganizationRole;
use Database\Factories\OrganizationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name'])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory, HasVersion4Uuids, SoftDeletes;

    /**
     * Get the columns that should receive a unique identifier.
     *
     * The UUID is the public identifier, the integer primary key stays internal.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * The users that belong to the organization, with their role.
     *
     * @return BelongsToMany<User, $this, Membership>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memberships')
            ->using(Membership::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * The users that are responsible for the organization.
     *
     * @return BelongsToMany<User, $this, Membership>
     */
    public function responsibles(): BelongsToMany
    {
        return $this->users()->wherePivot('role', OrganizationRole::Responsible->value);
    }

    /**
     * Determine whether the user is the only responsible user of the organization.
     */
    public function isLastResponsible(User $user): bool
    {
        $responsibles = $this->responsibles()->pluck('users.id');

        return $responsibles->count() === 1 && $responsibles->first() === $user->id;
    }

    /**
     * The pending invitations to join the organization.
     *
     * @return HasMany<Invitation, $this>
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * The spots of the organization.
     *
     * @return HasMany<Spot, $this>
     */
    public function spots(): HasMany
    {
        return $this->hasMany(Spot::class);
    }

    /**
     * Scope the query to what the list of organizations shows: the organizations the user belongs to, all of them for
     * administrators, with their number of spots the user can find in the lists and of members, sorted by name,
     * optionally only the ones whose name contains the search query or that the user can manage.
     *
     * With `$deleted`, the deleted ones instead, to restore them, the most recently deleted first, with the number of
     * spots that come back with them. Only the administrators find them: a deleted organization is not one of its
     * members' anymore.
     *
     * @param  Builder<static>  $query
     */
    public function scopeListed(Builder $query, User $user, string $searchQuery = '', bool $mine = false, bool $deleted = false): void
    {
        $query->ofMember($user)
            ->when(
                $deleted,
                fn (Builder $query) => $query->onlyTrashed()
                    ->withCount(['spots' => fn (Builder $query) => $query->deletedWithItsOrganization()])
                    ->latest('deleted_at'),
                fn (Builder $query) => $query->withSpotsCount($user),
            )
            ->withCount('users as members_count')
            ->search($searchQuery)
            ->when($mine, fn (Builder $query) => $query->manageableBy($user))
            ->orderBy('organizations.name');
    }

    /**
     * Determine whether the user can restore the organization, deleted: as the policy says.
     */
    public function isRestorableBy(?User $user): bool
    {
        return $user !== null && $user->can('restore', $this);
    }

    /**
     * The spots deleted with the organization, which come back with it: the ones deleted at the same time or after it,
     * as `DeleteOrganization` deletes it before them. The ones deleted before, one by one, stay deleted.
     *
     * @return HasMany<Spot, $this>
     */
    public function spotsDeletedWithIt(): HasMany
    {
        return $this->spots()->deletedWithItsOrganization();
    }

    /**
     * Scope the query to the organizations the user belongs to, or to all of them for administrators.
     *
     * @param  Builder<static>  $query
     */
    public function scopeOfMember(Builder $query, User $user): void
    {
        if (! $user->isAdmin()) {
            $query->mine($user);
        }
    }

    /**
     * Scope the query to count the spots of the organizations that the user can find in the lists, as `spots_count`.
     *
     * @param  Builder<static>  $query
     */
    public function scopeWithSpotsCount(Builder $query, ?User $user): void
    {
        $query->withCount(['spots' => fn (Builder $query) => $query->visibleTo($user)]);
    }

    /**
     * Load the number of spots of the organization that the user can find in the lists, as `spots_count`: the same as
     * the lists count. Deleted, the ones that come back with it.
     */
    public function loadSpotsCount(?User $user): static
    {
        return $this->loadCount(['spots' => fn (Builder $query) => $this->trashed()
            ? $query->deletedWithItsOrganization()
            : $query->visibleTo($user)]);
    }

    /**
     * Scope the query to the organizations whose name contains the search query.
     *
     * @param  Builder<static>  $query
     */
    public function scopeSearch(Builder $query, string $searchQuery): void
    {
        if ($searchQuery !== '') {
            $query->whereLike('name', "%{$searchQuery}%");
        }
    }

    /**
     * Scope the query to the organizations the user belongs to.
     *
     * @param  Builder<static>  $query
     */
    public function scopeMine(Builder $query, User $user): void
    {
        $query->whereIn('organizations.id', $user->organizations()->select('organizations.id'));
    }

    /**
     * Scope the query to the organizations the user can manage: the ones they are responsible for, all of them for
     * administrators, as `OrganizationPolicy::manage()` decides.
     *
     * @param  Builder<static>  $query
     */
    public function scopeManageableBy(Builder $query, User $user): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $query->whereIn('organizations.id', $user->organizations()
            ->wherePivot('role', OrganizationRole::Responsible->value)
            ->select('organizations.id'));
    }
}
