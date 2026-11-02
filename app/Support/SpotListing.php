<?php

namespace App\Support;

use App\Http\Requests\ListFiltersRequest;
use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * The list of spots, with its filters, shared by /spots and /organizations/{uuid}/spots.
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
            canCreate: $organization !== null && $user?->can('create', [Spot::class, $organization]) === true,
            spots: Spot::listed($user, $request->searchQuery(), $request->mine())
                ->when($organization, fn ($query) => $query->whereBelongsTo($organization))
                ->paginate(25)
                ->withQueryString(),
            action: $organization
                ? route('organizations.spots.index', ['organization' => $organization->uuid])
                : route('spots.index'),
            // The map of all the results, with the filters of the list but not its page
            map: $organization
                ? route('organizations.spots.map', ['organization' => $organization->uuid, ...Arr::except($request->query(), 'page')])
                : route('spots.map', Arr::except($request->query(), 'page')),
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
            Spot::filtered($request->user(), $request->searchQuery(), $request->mine())
                ->when($organization, fn ($query) => $query->whereBelongsTo($organization))
                ->get(['uuid', 'name', 'latitude', 'longitude']),
        );
    }

    /**
     * Whether a filter is applied.
     */
    public function isFiltered(): bool
    {
        return $this->mine || $this->searchQuery !== '';
    }
}
