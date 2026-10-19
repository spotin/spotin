<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;

/**
 * A user as the others see them, such as the creator of a spot: only their name.
 *
 * @mixin User
 */
class UserResource extends JsonApiResource
{
    public function toId(Request $request): string
    {
        return $this->uuid;
    }

    public function toType(Request $request): string
    {
        return 'users';
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
