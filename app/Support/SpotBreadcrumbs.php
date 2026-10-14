<?php

namespace App\Support;

use App\Models\Spot;

class SpotBreadcrumbs
{
    /**
     * The first crumbs of every page of a spot, ending with the spot itself.
     *
     * The organization is reachable from the details of the spot, so it is not part of the trail.
     *
     * @return list<array{label: string, url?: string}>
     */
    public static function for(Spot $spot): array
    {
        return [
            ['label' => __('ui.spots.index.title'), 'url' => route('spots.index')],
            ['label' => $spot->name, 'url' => route('spots.show', ['spot' => $spot->uuid])],
        ];
    }
}
