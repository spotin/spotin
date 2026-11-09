<?php

namespace App\Actions\Spots;

use App\Models\Spot;
use Illuminate\Validation\ValidationException;

class RestoreSpot
{
    /**
     * Restore the spot: its QR code leads to it again.
     *
     * Its name may have been taken meanwhile, as the names are unique among the spots of an organization that are not
     * deleted only.
     *
     * @throws ValidationException
     */
    public function restore(Spot $spot): void
    {
        // Its organization is deleted: the spot comes back with it only
        if ($spot->organization === null) {
            throw ValidationException::withMessages(['restore' => __('ui.spots.feedback.organization_deleted')]);
        }

        if ($spot->name !== null && Spot::whereBelongsTo($spot->organization)->where('name', $spot->name)->exists()) {
            throw ValidationException::withMessages(['restore' => __('ui.spots.feedback.name_taken', [
                'name' => $spot->name,
                'organization' => $spot->organization->name,
            ])]);
        }

        $spot->restore();
    }
}
