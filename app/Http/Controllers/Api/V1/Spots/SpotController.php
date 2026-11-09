<?php

namespace App\Http\Controllers\Api\V1\Spots;

use App\Actions\Spots\CreateSpot;
use App\Http\Controllers\Controller;
use App\Http\Requests\Spots\ApiSpotListFiltersRequest;
use App\Http\Requests\Spots\StoreSpotRequest;
use App\Http\Requests\Spots\UpdateSpotRequest;
use App\Http\Resources\SpotResource;
use App\Models\Spot;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection;
use Illuminate\Http\Response;

#[Group('Spots')]
class SpotController extends Controller
{
    /**
     * Get the spots of your organizations.
     *
     * The spots of the organizations you are a member of, or all of them for administrators. Use
     * `filter[organization]` to only get the spots of one of them.
     */
    public function index(ApiSpotListFiltersRequest $request): AnonymousResourceCollection
    {
        $spots = Spot::manageableBy($request->user())
            ->listed($request->user(), $request->searchQuery(), $request->mine())
            ->ofOrganization($request->organization())
            ->paginate(25)
            ->withQueryString();

        return SpotResource::collection($spots);
    }

    /**
     * Get a specific spot.
     */
    public function show(Spot $spot): SpotResource
    {
        return new SpotResource($spot);
    }

    /**
     * Create a new spot.
     *
     * The spot is created in the organization given by `organization`, which you must be a member of, and you become
     * its creator.
     */
    public function store(StoreSpotRequest $request, CreateSpot $createSpot): JsonResponse
    {
        $spot = $createSpot->create($request->organization(), $request->safe()->except('organization'), $request->user());

        return (new SpotResource($spot))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update a specific spot.
     */
    public function update(UpdateSpotRequest $request, Spot $spot): SpotResource
    {
        $spot->update($request->fields());

        return new SpotResource($spot);
    }

    /**
     * Delete a specific spot.
     */
    public function destroy(Spot $spot): Response
    {
        $spot->delete();

        return response()->noContent();
    }
}
