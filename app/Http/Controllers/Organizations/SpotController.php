<?php

namespace App\Http\Controllers\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListFiltersRequest;
use App\Http\Requests\Spots\StoreSpotRequest;
use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

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
        $spots = Spot::filtered($request->user(), $request->searchQuery(), $request->mine())
            ->whereBelongsTo($organization)
            ->get(['uuid', 'name', 'latitude', 'longitude']);

        return response()->json(Spot::featureCollection($spots));
    }

    /**
     * Create a spot in the organization of the URL, on behalf of the current user.
     */
    public function store(StoreSpotRequest $request, Organization $organization): RedirectResponse
    {
        $spot = new Spot($request->validated());
        $spot->organization()->associate($organization);
        $spot->creator()->associate($request->user());
        $spot->save();

        return redirect()->route('spots.show', ['spot' => $spot->uuid])->with('success', __('ui.organizations.spots.feedback.spot_created'));
    }
}
