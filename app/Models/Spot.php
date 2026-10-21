<?php

namespace App\Models;

use Database\Factories\SpotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\NormalizeHeadings\NormalizeHeadingsExtension;

#[Fillable(['name', 'description', 'latitude', 'longitude', 'website', 'redirect_to_website', 'is_hidden'])]
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'redirect_to_website' => 'boolean',
            'is_hidden' => 'boolean',
        ];
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
     * The description, stored as Markdown, rendered to HTML.
     *
     * The description is written by users: raw HTML is removed and unsafe links (`javascript:`, `data:`…) are
     * dropped, so the result can be output as is. External links open in a new tab, without referrer.
     *
     * The page of the spot has its own first heading: the headings of the description start at the second level, and
     * each one is one level below the heading it is under, whatever levels they were written at.
     */
    public function descriptionHtml(): string
    {
        if (blank($this->description)) {
            return '';
        }

        return Str::markdown($this->description, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'external_link' => [
                'internal_hosts' => [parse_url(config('app.url'), PHP_URL_HOST)],
                'open_in_new_window' => true,
                'nofollow' => 'external',
                'noopener' => 'external',
                'noreferrer' => 'external',
            ],
            'normalize_headings' => [
                'min_level' => 2,
                'max_level' => 6,
                'rebase_to_min_level' => true,
            ],
        ], [new ExternalLinkExtension, new NormalizeHeadingsExtension]);
    }

    /**
     * Whether the visitors of the spot are redirected to its website, without seeing the spot.
     */
    public function redirectsVisitors(): bool
    {
        return $this->redirect_to_website && filled($this->website);
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
            ->visibleTo($user)
            ->search($searchQuery)
            ->when($mine && $user, fn (Builder $query) => $query->mine($user))
            ->orderBy('name');
    }

    /**
     * Scope the query to the spots the user can find in lists: the hidden spots are only listed for the members of
     * their organization and for administrators. They stay reachable by their link.
     *
     * @param  Builder<static>  $query
     */
    public function scopeVisibleTo(Builder $query, ?User $user): void
    {
        if ($user?->isAdmin()) {
            return;
        }

        $query->where(fn (Builder $query) => $query
            ->where('is_hidden', false)
            ->when($user, fn (Builder $query) => $query->orWhereIn('organization_id', $user->organizations()->select('organizations.id'))));
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
