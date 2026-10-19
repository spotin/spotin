<?php

namespace App\Http\Controllers\Api\V1\Spots;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiListFiltersRequest;
use App\Http\Requests\Spots\UpdateSpotRequest;
use App\Http\Resources\SpotResource;
use App\Models\Spot;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection;
use Illuminate\Http\Response;

#[Group('Spots')]
class SpotController extends Controller
{
    /**
     * List the spots of all the organizations, optionally only the ones the user created or matching a
     * search query.
     */
    public function index(ApiListFiltersRequest $request): AnonymousResourceCollection
    {
        $spots = Spot::listed($request->user(), $request->searchQuery(), $request->mine())
            ->paginate(25)
            ->withQueryString();

        return SpotResource::collection($spots);
    }

    /**
     * Show a spot.
     */
    public function show(Spot $spot): SpotResource
    {
        return new SpotResource($spot);
    }

    /**
     * Update a spot.
     */
    public function update(UpdateSpotRequest $request, Spot $spot): SpotResource
    {
        $spot->update($request->validated());

        return new SpotResource($spot);
    }

    /**
     * Permanently delete a spot.
     */
    public function destroy(Spot $spot): Response
    {
        $spot->delete();

        return response()->noContent();
    }
}
