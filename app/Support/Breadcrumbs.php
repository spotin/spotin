<?php

namespace App\Support;

use App\Models\Organization;
use App\Models\Spot;

/**
 * The trails of the pages, from the root of a domain down to the page that asks for it.
 *
 * Each method returns the whole trail up to its own page, which pages complete with their own crumb.
 * The last crumb of a trail is always shown as the current page, whatever its URL.
 */
final class Breadcrumbs
{
    /**
     * @return list<array{label: string, url: string}>
     */
    public static function organizations(): array
    {
        return [
            ['label' => __('ui.organizations.index.title'), 'url' => route('organizations.index')],
        ];
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public static function organization(Organization $organization): array
    {
        return [
            ...self::organizations(),
            ['label' => $organization->name, 'url' => route('organizations.show', ['organization' => $organization->uuid])],
        ];
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public static function organizationSpots(Organization $organization): array
    {
        return [
            ...self::organization($organization),
            ['label' => __('ui.spots.index.title'), 'url' => route('organizations.spots.index', ['organization' => $organization->uuid])],
        ];
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public static function organizationInvitations(Organization $organization): array
    {
        return [
            ...self::organization($organization),
            ['label' => __('ui.organizations.invitations.index.title'), 'url' => route('organizations.invitations.index', ['organization' => $organization->uuid])],
        ];
    }

    /**
     * The organization is reachable from the details of a spot, so it is not part of the trail.
     *
     * @return list<array{label: string, url: string}>
     */
    public static function spot(Spot $spot): array
    {
        return [
            ['label' => __('ui.spots.index.title'), 'url' => route('spots.index')],
            ['label' => $spot->name, 'url' => route('spots.show', ['spot' => $spot->uuid])],
        ];
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public static function settings(): array
    {
        return [
            ['label' => __('ui.settings.title'), 'url' => route('settings.index')],
        ];
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public static function developer(): array
    {
        return [
            ...self::settings(),
            ['label' => __('ui.settings.developer.index.title'), 'url' => route('settings.developer')],
        ];
    }
}
