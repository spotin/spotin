<?php

namespace App\Support;

use App\Http\Requests\ListFiltersRequest;
use App\Models\Organization;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * The list of organizations, with its filters.
 */
final readonly class OrganizationListing
{
    /**
     * @param  LengthAwarePaginator<int, Organization>  $organizations
     * @param  Collection<int, string>  $roles  The role of the user in the organizations they belong to, by organization id.
     */
    public function __construct(
        public bool $mine,
        public string $searchQuery,
        public bool $canCreate,
        public LengthAwarePaginator $organizations,
        public Collection $roles,
        public string $action,
    ) {}

    /**
     * Build the list for the request.
     */
    public static function for(ListFiltersRequest $request): self
    {
        $user = $request->user();

        return new self(
            mine: $request->mine(),
            searchQuery: $request->searchQuery(),
            canCreate: $user?->can('create', Organization::class) === true,
            organizations: Organization::listed($user, $request->searchQuery(), $request->mine())
                ->paginate(25)
                ->withQueryString(),
            roles: $user?->organizations()->pluck('memberships.role', 'organizations.id') ?? collect(),
            action: route('organizations.index'),
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
