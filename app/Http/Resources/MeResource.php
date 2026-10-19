<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * The user of the request together with the token the request was made with.
 *
 * @mixin User
 */
class MeResource extends UserResource
{
    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->isAdmin(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toMeta(Request $request): array
    {
        /** @var PersonalAccessToken|null $token */
        $token = $this->currentAccessToken();

        return [
            'token' => [
                'name' => $token->name ?? null,
                'abilities' => array_values($token->abilities ?? []),
                'expires_at' => $token->expires_at ?? null,
                'last_used_at' => $token->last_used_at ?? null,
            ],
        ];
    }
}
