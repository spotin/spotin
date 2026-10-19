<?php

namespace App\Models;

use Database\Factories\SpotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name'])]
class Spot extends Model
{
    /** @use HasFactory<SpotFactory> */
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
     * The organization the spot belongs to.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * The user who created the spot, or null once they deleted their account.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope the query to what a list of spots shows: with their organization and creator, sorted by name,
     * optionally only the spots whose name contains the search query or that the user created.
     *
     * @param  Builder<static>  $query
     */
    public function scopeListed(Builder $query, ?User $user, string $searchQuery = '', bool $mine = false): void
    {
        $query->with(['organization', 'creator'])
            ->search($searchQuery)
            ->when($mine && $user, fn (Builder $query) => $query->mine($user))
            ->orderBy('name');
    }

    /**
     * Scope the query to the spots whose name contains the search query.
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
     * Scope the query to the spots the user created.
     *
     * @param  Builder<static>  $query
     */
    public function scopeMine(Builder $query, User $user): void
    {
        $query->where('created_by', $user->id);
    }
}
