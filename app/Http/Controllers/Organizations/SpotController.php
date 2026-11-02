<?php

namespace App\Http\Controllers\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListFiltersRequest;
use App\Models\Organization;
use App\Support\SpotListing;
use Illuminate\Http\JsonResponse;

class SpotController extends Controller
{
    /**
     * The spots of the organization as GeoJSON, for its map: the ones the user finds in its list, with its filters.
     *
     * All of them at once: the map groups the close ones. Well beyond thousands of spots, the map would rather load
     * them by visible area, or as vector tiles.
     */
    public function map(ListFiltersRequest $request, Organization $organization): JsonResponse
    {
        return response()->json(SpotListing::features($request, $organization));
    }
}
