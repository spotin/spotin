<?php

namespace App\Http\Controllers\Api\V1\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiListFiltersRequest;
use App\Http\Requests\Organizations\StoreOrganizationRequest;
use App\Http\Requests\Organizations\UpdateOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection;
use Illuminate\Http\Response;

#[Group('Organizations')]
class OrganizationController extends Controller
{
    /**
     * List the organizations, optionally only the ones of the user or matching a search query.
     */
    public function index(ApiListFiltersRequest $request): AnonymousResourceCollection
    {
        $organizations = Organization::listed($request->user(), $request->searchQuery(), $request->mine())
            ->paginate(25)
            ->withQueryString();

        return OrganizationResource::collection($organizations);
    }

    /**
     * Show an organization.
     */
    public function show(Organization $organization): OrganizationResource
    {
        return new OrganizationResource($organization->loadCount('spots'));
    }

    /**
     * Create an organization.
     */
    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $organization = Organization::create($request->validated());

        return (new OrganizationResource($organization->loadCount('spots')))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update an organization.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): OrganizationResource
    {
        $organization->update($request->validated());

        return new OrganizationResource($organization->loadCount('spots'));
    }

    /**
     * Permanently delete an organization.
     */
    public function destroy(Organization $organization): Response
    {
        $organization->delete();

        return response()->noContent();
    }
}
