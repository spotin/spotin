<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\User;

class OrganizationBreadcrumbs
{
    /**
     * The first crumbs of every page of an organization, ending with the organization itself.
     *
     * Members reach an organization from their own list of organizations. An administrator
     * who is not a member reaches it from the administration instead.
     *
     * @return list<array{label: string, url?: string}>
     */
    public static function for(Organization $organization, User $user): array
    {
        $crumb = ['label' => $organization->name, 'url' => route('organizations.show', ['organization' => $organization->uuid])];

        if ($user->belongsToOrganization($organization)) {
            return [
                ['label' => __('ui.organizations.index.title'), 'url' => route('organizations.index')],
                $crumb,
            ];
        }

        return [
            ['label' => __('ui.admin.title'), 'url' => route('admin.index')],
            ['label' => __('ui.admin.organizations.index.title'), 'url' => route('admin.organizations.index')],
            $crumb,
        ];
    }
}
