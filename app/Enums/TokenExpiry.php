<?php

namespace App\Enums;

use Carbon\CarbonInterface;

enum TokenExpiry: string
{
    case Days30 = '30';
    case Days90 = '90';
    case Year = '365';
    case Never = 'never';

    /**
     * The translated name of the expiry, such as "90 days".
     */
    public function label(): string
    {
        return __('ui.settings.developer.common.forms.fields.expiry.options.'.$this->value);
    }

    /**
     * When a token created now expires, or null when it never does.
     */
    public function expiresAt(): ?CarbonInterface
    {
        return $this === self::Never ? null : now()->addDays((int) $this->value);
    }
}
