<?php

namespace App\Actions\Organizations;

use App\Models\Organization;
use Illuminate\Support\Facades\DB;

class DeleteOrganization
{
    /**
     * Delete the organization with its spots, kept for their QR codes to tell they are no longer in use, and to restore
     * them. Its pending invitations are deleted for good: their links stop working.
     *
     * The organization is deleted before its spots, so the ones deleted with it are the ones deleted at the same time or
     * after it: `RestoreOrganization` brings back these ones only, not the ones deleted before, one by one.
     */
    public function delete(Organization $organization): void
    {
        DB::transaction(function () use ($organization): void {
            $organization->delete();
            $organization->spots()->delete();
            $organization->invitations()->delete();
        });
    }
}
