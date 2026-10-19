<?php

namespace App\Http\Controllers\Api\V1\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiListFiltersRequest;
use App\Http\Requests\Spots\StoreSpotRequest;
use App\Http\Resources\SpotResource;
use App\Models\Organization;
use App\Models\Spot;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\JsonApi\AnonymousResourceCollection;
use Illuminate\Http\Response;

#[Group('Organizations')]
class SpotController extends Controller
{
    /**
     * Get the spots of an organization.
     */
    public function index(ApiListFiltersRequest $request, Organization $organization): AnonymousResourceCollection
    {
        $spots = Spot::listed($request->user(), $request->searchQuery(), $request->mine())
            ->whereBelongsTo($organization)
            ->paginate(25)
            ->withQueryString();

        return SpotResource::collection($spots);
    }

    /**
     * Create a new spot in a specific organization.
     */
    public function store(StoreSpotRequest $request, Organization $organization): JsonResponse
    {
        $spot = new Spot($request->safe()->only('name'));
        $spot->organization()->associate($organization);
        $spot->creator()->associate($request->user());
        $spot->save();

        return (new SpotResource($spot))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
