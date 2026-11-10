<?php

namespace App\Http\Controllers\Api\V1\Spots;

use App\Actions\Spots\CreateSpot;
use App\Actions\Spots\RestoreSpot;
use App\Http\Controllers\Controller;
use App\Http\Requests\Spots\ApiSpotListFiltersRequest;
use App\Http\Requests\Spots\StoreSpotRequest;
use App\Http\Requests\Spots\UpdateSpotRequest;
use App\Http\Resources\SpotResource;
use App\Models\Spot;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

#[Group('Spots')]
class SpotController extends Controller
{
    /**
     * Get the spots of your organizations.
     *
     * The spots of the organizations you are a member of, or all of them for administrators. Use
     * `filter[organization]` to only get the spots of one of them, and `filter[deleted]` to get the deleted ones
     * instead, to restore them, the most recently deleted first.
     */
    public function index(ApiSpotListFiltersRequest $request): AnonymousResourceCollection
    {
        $spots = ($request->deleted() ? Spot::restorableBy($request->user()) : Spot::manageableBy($request->user()))
            ->listed($request->user(), $request->searchQuery(), $request->mine())
            ->ofOrganization($request->organization())
            ->paginate(25)
            ->withQueryString();

        return SpotResource::collection($spots);
    }

    /**
     * Get a specific spot.
     *
     * A deleted spot, with its `deleted_at`, for who can restore it: the members of its organization and the
     * administrators; `410 Gone` for the others.
     */
    public function show(Request $request, Spot $spot): SpotResource
    {
        abort_if($spot->trashed() && ! $spot->isRestorableBy($request->user()), Response::HTTP_GONE);

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
        $spot = $createSpot->create($request->organization(), Arr::except($request->fields(), 'organization'), $request->user());

        return (new SpotResource($spot))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update a specific spot.
     *
     * Only the fields sent are changed. The spot keeps its organization.
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

    /**
     * Restore a deleted spot.
     *
     * Its QR code leads to it again. The spots of a deleted organization come back with their organization only.
     */
    public function restore(Spot $spot, RestoreSpot $restoreSpot): SpotResource
    {
        $restoreSpot->restore($spot);

        return new SpotResource($spot);
    }
}
