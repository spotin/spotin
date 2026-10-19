<?php

namespace App\Http\Controllers\Spots;

use App\Http\Controllers\Controller;
use App\Http\Requests\Spots\UpdateSpotRequest;
use App\Models\Spot;
use Illuminate\Http\RedirectResponse;

class SpotController extends Controller
{
    /**
     * Update the spot.
     */
    public function update(UpdateSpotRequest $request, Spot $spot): RedirectResponse
    {
        $spot->update($request->validated());

        return back()->with('success', __('ui.spots.feedback.spot_updated'));
    }

    /**
     * Permanently delete the spot.
     */
    public function destroy(Spot $spot): RedirectResponse
    {
        $spot->delete();

        return redirect()->route('organizations.spots.index', ['organization' => $spot->organization->uuid])->with('success', __('ui.spots.feedback.spot_deleted'));
    }
}
