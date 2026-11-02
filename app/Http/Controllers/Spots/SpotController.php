<?php

namespace App\Http\Controllers\Spots;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListFiltersRequest;
use App\Http\Requests\Spots\UpdateSpotRequest;
use App\Models\Spot;
use App\Support\QrCode;
use App\Support\SpotListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class SpotController extends Controller
{
    /**
     * The spots as GeoJSON, for the map of the list of all the spots: the ones the user finds in it, with its filters.
     *
     * All of them at once: the map groups the close ones. Well beyond thousands of spots, the map would rather load
     * them by visible area, or as vector tiles.
     */
    public function map(ListFiltersRequest $request): JsonResponse
    {
        return response()->json(SpotListing::features($request));
    }

    /**
     * The address of the QR code of the spot: everyone goes to its form while it needs to be set up, then straight to its
     * website when the spot redirects its visitors, otherwise to its page. Both say it was just scanned.
     */
    public function redirect(Spot $spot): RedirectResponse
    {
        if ($spot->needs_setup) {
            return redirect()->route('spots.edit', ['spot' => $spot->uuid])->with('scanned', true);
        }

        if ($spot->redirectsVisitors()) {
            return redirect()->away($spot->website);
        }

        return redirect()->route('spots.show', ['spot' => $spot->uuid])->with('scanned', true);
    }

    /**
     * Download the QR code of the spot, to print, as SVG or PNG.
     */
    public function qrCode(Spot $spot, string $format): Response
    {
        return QrCode::download(route('spots.redirect', ['spot' => $spot->uuid]), $spot->displayName(), $format);
    }

    /**
     * Update the spot, or set it up.
     */
    public function update(UpdateSpotRequest $request, Spot $spot): RedirectResponse
    {
        $feedback = $request->settingUp() ? __('ui.spots.feedback.spot_set_up') : __('ui.spots.feedback.spot_updated');

        $spot->update($request->fields());

        return redirect()->route('spots.show', ['spot' => $spot->uuid])->with('success', $feedback);
    }

    /**
     * Permanently delete the spot.
     */
    public function destroy(Spot $spot): RedirectResponse
    {
        $spot->delete();

        return redirect()->route('organizations.show', ['organization' => $spot->organization->uuid])->with('success', __('ui.spots.feedback.spot_deleted'));
    }
}
