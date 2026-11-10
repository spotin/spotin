<?php

namespace App\Http\Controllers\Spots;

use App\Actions\Spots\CreateSpot;
use App\Actions\Spots\RestoreSpot;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListFiltersRequest;
use App\Http\Requests\Spots\StoreSpotRequest;
use App\Http\Requests\Spots\UpdateSpotRequest;
use App\Models\Spot;
use App\Support\QrCode;
use App\Support\SpotListing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class SpotController extends Controller
{
    /**
     * The spots as GeoJSON, for the map of the home page: all the ones the user can find in the lists, without the
     * hidden ones for the others than the members of their organization.
     *
     * All of them at once: the map groups the close ones. Well beyond thousands of spots, the map would rather load
     * them by visible area, or as vector tiles.
     */
    public function map(Request $request): JsonResponse
    {
        return response()->json(Spot::featureCollection(
            Spot::visibleTo($request->user())->located()->get(['uuid', 'name', 'latitude', 'longitude']),
        ));
    }

    /**
     * The spots as GeoJSON, for the map of the list of the spots the user can manage, with its filters.
     */
    public function manageableMap(ListFiltersRequest $request): JsonResponse
    {
        return response()->json(SpotListing::features($request));
    }

    /**
     * The address of the QR code of the spot: everyone goes to its form while it needs to be set up, then straight to its
     * website when the spot redirects its visitors, otherwise to its page. Both say it was just scanned. Once the spot is
     * deleted, a page that tells the code is no longer in use, as gone.
     */
    public function redirect(Spot $spot): RedirectResponse|Response
    {
        if ($spot->trashed()) {
            return response()->view('removed', [
                'title' => __('ui.qr_codes.inactive.title'),
                'text' => __('ui.qr_codes.inactive.spot'),
            ], Response::HTTP_GONE);
        }

        if ($spot->needs_setup) {
            return redirect()->route('spots.edit', ['spot' => $spot->uuid])->with('scanned', true);
        }

        if ($spot->redirectsVisitors()) {
            return redirect()->away($spot->website);
        }

        return redirect()->route('spots.show', ['spot' => $spot->uuid])->with('scanned', true);
    }

    /**
     * Download the QR code of the spot, to print, as SVG or PNG. The first download is recorded, without changing when the
     * spot was last updated: it is not a change of the spot.
     */
    public function qrCode(Spot $spot, string $format): Response
    {
        Spot::whereKey($spot->getKey())->whereNull('qr_code_downloaded_at')->toBase()->update(['qr_code_downloaded_at' => now()]);

        return QrCode::download(route('spots.redirect', ['spot' => $spot->uuid]), $spot->displayName(), $format);
    }

    /**
     * Create a spot in the organization chosen in its form, on behalf of the current user.
     */
    public function store(StoreSpotRequest $request, CreateSpot $createSpot): RedirectResponse
    {
        $spot = $createSpot->create($request->organization(), Arr::except($request->fields(), 'organization'), $request->user());

        return redirect()->route('spots.show', ['spot' => $spot->uuid])->with('success', __('ui.spots.feedback.spot_created'));
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

        return redirect()->route('organizations.show', ['organization' => $spot->organization->uuid])
            ->with('success', __('ui.spots.feedback.spot_deleted'))
            ->with('undo', route('spots.restore', ['spot' => $spot->uuid]));
    }

    /**
     * Restore the deleted spot: its QR code leads to it again.
     */
    public function restore(Spot $spot, RestoreSpot $restoreSpot): RedirectResponse
    {
        $restoreSpot->restore($spot);

        return redirect()->route('spots.show', ['spot' => $spot->uuid])->with('success', __('ui.spots.feedback.spot_restored'));
    }
}
