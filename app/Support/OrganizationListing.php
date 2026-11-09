<?php

namespace App\Support;

use App\Http\Requests\ListFiltersRequest;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * The list of the organizations of the user, all of them for administrators, with its filters.
 */
final readonly class OrganizationListing
{
    /**
     * @param  LengthAwarePaginator<int, Organization>  $organizations
     * @param  Collection<int, string>  $roles  The role of the user in the organizations they belong to, by organization id.
     */
    public function __construct(
        public bool $mine,
        public bool $deleted,
        public string $searchQuery,
        public bool $canCreate,
        public bool $canRestore,
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
            deleted: $request->deleted(),
            searchQuery: $request->searchQuery(),
            canCreate: $user->can('create', Organization::class),
            // Only the administrators restore the deleted organizations, as they delete them: the filter is theirs
            canRestore: $user->isAdmin(),
            // The deleted ones the user can restore, as the deleted spots: among the organizations of the list, which a
            // deleted organization is not one of for its members, the administrators only find them
            organizations: Organization::query()
                ->when($request->deleted(), fn (Builder $query) => $query->restorable())
                ->listed($user, $request->searchQuery(), $request->mine())
                ->paginate(25)
                ->withQueryString(),
            roles: $user->organizations()->pluck('memberships.role', 'organizations.id'),
            action: route('organizations.index'),
        );
    }

    /**
     * Whether a filter is applied.
     */
    public function isFiltered(): bool
    {
        return $this->mine || $this->deleted || $this->searchQuery !== '';
    }
}
