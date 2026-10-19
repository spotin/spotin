<?php

namespace App\Http\Controllers\Api\V1\Spots;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListFiltersRequest;
use App\Http\Requests\Spots\UpdateSpotRequest;
use App\Http\Resources\SpotResource;
use App\Models\Spot;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SpotController extends Controller
{
    /**
     * List the spots of all the organizations, optionally only the ones the user created or matching a
     * search query.
     */
    public function index(ListFiltersRequest $request): AnonymousResourceCollection
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
        return new SpotResource($spot->load(['organization', 'creator']));
    }

    /**
     * Update a spot.
     */
    public function update(UpdateSpotRequest $request, Spot $spot): SpotResource
    {
        $spot->update($request->validated());

        return new SpotResource($spot->load(['organization', 'creator']));
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
