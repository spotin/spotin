<?php

namespace App\Enums;

use App\Models\User;
use Illuminate\Support\Str;

enum TokenAbility: string
{
    case OrganizationsRead = 'organizations:read';
    case OrganizationsCreate = 'organizations:create';
    case OrganizationsUpdate = 'organizations:update';
    case OrganizationsDelete = 'organizations:delete';
    case SpotsRead = 'spots:read';
    case SpotsCreate = 'spots:create';
    case SpotsUpdate = 'spots:update';
    case SpotsDelete = 'spots:delete';

    /**
     * The resource the ability applies to, such as "spots".
     */
    public function resource(): string
    {
        return Str::before($this->value, ':');
    }

    /**
     * The action the ability allows, such as "read".
     */
    public function action(): string
    {
        return Str::after($this->value, ':');
    }

    /**
     * The translated name of the ability, such as "Read spots".
     */
    public function label(): string
    {
        return __('ui.settings.developer.common.forms.fields.abilities.options.'.str_replace(':', '_', $this->value));
    }

    /**
     * Only administrators can create, edit and delete organizations, so the other users get no use of
     * a token with these abilities: the policies would always refuse them.
     */
    public function requiresAdmin(): bool
    {
        return $this->resource() === 'organizations' && $this->action() !== 'read';
    }

    /**
     * The abilities the user can give to a token.
     *
     * @return list<self>
     */
    public static function forUser(User $user): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $ability) => $user->isAdmin() || ! $ability->requiresAdmin(),
        ));
    }
}
