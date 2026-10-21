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

#[Fillable(['name'])]
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory, HasVersion4Uuids;

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
     * The users that are responsible of the organization.
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
     * Scope the query to what a list of organizations shows: with their number of spots the user can find in the lists,
     * sorted by name, optionally only the organizations whose name contains the search query or that the user belongs to.
     *
     * @param  Builder<static>  $query
     */
    public function scopeListed(Builder $query, ?User $user, string $searchQuery = '', bool $mine = false): void
    {
        $query->withCount(['spots' => fn (Builder $query) => $query->visibleTo($user)])
            ->search($searchQuery)
            ->when($mine && $user, fn (Builder $query) => $query->mine($user))
            ->orderBy('organizations.name');
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
}
