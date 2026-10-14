<?php

namespace App\Http\Controllers\Spots;

use App\Http\Controllers\Controller;
use App\Http\Requests\Spots\StoreSpotRequest;
use App\Http\Requests\Spots\UpdateSpotRequest;
use App\Models\Spot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SpotController extends Controller
{
    /**
     * Create a spot in the chosen organization, on behalf of the current user.
     */
    public function store(StoreSpotRequest $request): RedirectResponse
    {
        $organization = $request->organization();

        Gate::authorize('create', [Spot::class, $organization]);

        $spot = new Spot($request->safe()->only('name'));
        $spot->organization()->associate($organization);
        $spot->creator()->associate($request->user());
        $spot->save();

        return redirect()->route('spots.show', ['spot' => $spot->uuid])->with('status', 'spot-created');
    }

    /**
     * Update the spot.
     */
    public function update(UpdateSpotRequest $request, Spot $spot): RedirectResponse
    {
        $spot->update($request->validated());

        return back()->with('status', 'spot-updated');
    }

    /**
     * Permanently delete the spot.
     */
    public function destroy(Spot $spot): RedirectResponse
    {
        $spot->delete();

        return redirect()->route('spots.index', ['organization' => $spot->organization->uuid])->with('status', 'spot-deleted');
    }
}
