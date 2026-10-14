<?php

namespace App\Support;

use App\Models\Organization;

class OrganizationBreadcrumbs
{
    /**
     * The first crumbs of every page of an organization, ending with the organization itself.
     *
     * @return list<array{label: string, url?: string}>
     */
    public static function for(Organization $organization): array
    {
        return [
            ['label' => __('ui.organizations.index.title'), 'url' => route('organizations.index')],
            ['label' => $organization->name, 'url' => route('organizations.show', ['organization' => $organization->uuid])],
        ];
    }
}
