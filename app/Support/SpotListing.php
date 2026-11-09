<?php

namespace App\Support;

use App\Http\Requests\ListFiltersRequest;
use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * The list of spots, with its filters, shared by /spots and the page of each organization, /organizations/{uuid}.
 *
 * /spots lists the spots the user can manage; the page of an organization, public, its spots the user can find in the
 * lists.
 */
final readonly class SpotListing
{
    /**
     * @param  LengthAwarePaginator<int, Spot>  $spots
     */
    public function __construct(
        public ?Organization $organization,
        public bool $mine,
        public string $searchQuery,
        public bool $canCreate,
        public LengthAwarePaginator $spots,
        public string $action,
        public string $map,
    ) {}

    /**
     * Build the list for the request, optionally restricted to the organization of the page.
     */
    public static function for(ListFiltersRequest $request, ?Organization $organization = null): self
    {
        $user = $request->user();

        return new self(
            organization: $organization,
            mine: $request->mine(),
            searchQuery: $request->searchQuery(),
            // In the organization of the page, or in one of the organizations of the user on the list of all the spots
            canCreate: $organization !== null
                ? $user?->can('create', [Spot::class, $organization]) === true
                : $user?->can('createAny', Spot::class) === true,
            spots: self::query($request, $organization)
                ->listed($user, $request->searchQuery(), $request->mine())
                ->paginate(25)
                ->withQueryString(),
            action: $organization
                ? route('organizations.show', ['organization' => $organization->uuid])
                : route('spots.index'),
            // The map of all the results, with the filters of the list but not its page
            map: $organization
                ? route('organizations.spots.map', ['organization' => $organization->uuid, ...Arr::except($request->query(), 'page')])
                : route('spots.manageable.map', Arr::except($request->query(), 'page')),
        );
    }

    /**
     * All the results of the list as GeoJSON, for its map, optionally of the organization of the page: the same spots as
     * the list, without its pages.
     *
     * @return array{type: 'FeatureCollection', features: list<array<string, mixed>>}
     */
    public static function features(ListFiltersRequest $request, ?Organization $organization = null): array
    {
        return Spot::featureCollection(
            self::query($request, $organization)
                ->filtered($request->user(), $request->searchQuery(), $request->mine())
                ->located()
                ->get(['uuid', 'name', 'latitude', 'longitude']),
        );
    }

    /**
     * The spots the list starts from: the ones of the organization the user can find in the lists, or all the ones
     * they can manage.
     *
     * @return Builder<Spot>
     */
    private static function query(ListFiltersRequest $request, ?Organization $organization): Builder
    {
        return $organization
            ? $organization->spots()->visibleTo($request->user())->getQuery()
            : Spot::manageableBy($request->user());
    }

    /**
     * Whether a filter is applied.
     */
    public function isFiltered(): bool
    {
        return $this->mine || $this->searchQuery !== '';
    }
}
