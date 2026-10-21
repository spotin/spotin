<?php

namespace App\Http\Controllers\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Spots\StoreSpotRequest;
use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Http\RedirectResponse;

class SpotController extends Controller
{
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
