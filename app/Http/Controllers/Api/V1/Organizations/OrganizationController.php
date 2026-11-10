<?php

namespace App\Http\Controllers\Api\V1\Organizations;

use App\Actions\Organizations\DeleteOrganization;
use App\Actions\Organizations\RestoreOrganization;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApiListFiltersRequest;
use App\Http\Requests\Organizations\StoreOrganizationRequest;
use App\Http\Requests\Organizations\UpdateOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection;
use Illuminate\Http\Response;

#[Group('Organizations')]
class OrganizationController extends Controller
{
    /**
     * Get your organizations.
     *
     * The organizations you are a member of, or all of them for administrators. Use `filter[mine]` to only get the ones
     * you can manage: the ones you are responsible for. Use `filter[deleted]` to get the deleted ones you can restore
     * instead, the most recently deleted first: only administrators restore organizations, the others get none.
     */
    public function index(ApiListFiltersRequest $request): AnonymousResourceCollection
    {
        $organizations = Organization::query()
            ->listed($request->user(), $request->searchQuery(), $request->mine(), $request->deleted())
            ->paginate(25)
            ->withQueryString();

        return OrganizationResource::collection($organizations);
    }

    /**
     * Get the specific organization.
     *
     * A deleted organization, with its `deleted_at`, for the administrators, who can restore it; `410 Gone` for the
     * others.
     */
    public function show(Request $request, Organization $organization): OrganizationResource
    {
        abort_if($organization->trashed() && ! $organization->isRestorableBy($request->user()), Response::HTTP_GONE);

        return new OrganizationResource($organization->loadSpotsCount($request->user()));
    }

    /**
     * Create a new organization.
     */
    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = Organization::create($request->validated());

        return (new OrganizationResource($organization->loadSpotsCount($request->user())))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update a specific organization.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): OrganizationResource
    {
        $organization->update($request->validated());

        return new OrganizationResource($organization->loadSpotsCount($request->user()));
    }

    /**
     * Delete a specific organization.
     */
    public function destroy(Organization $organization, DeleteOrganization $deleteOrganization): Response
    {
        $deleteOrganization->delete($organization);

        return response()->noContent();
    }

    /**
     * Restore a deleted organization.
     *
     * With the spots deleted with it, and its members. Its pending invitations, deleted for good, do not come back.
     */
    public function restore(Request $request, Organization $organization, RestoreOrganization $restoreOrganization): OrganizationResource
    {
        $restoreOrganization->restore($organization);

        return new OrganizationResource($organization->loadSpotsCount($request->user()));
    }
}
