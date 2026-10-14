<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The user of the request together with the token the request was made with.
 *
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $token = $this->currentAccessToken();

        return [
            'name' => $this->name,
            'email' => $this->email,
            'is_admin' => $this->isAdmin(),
            'token' => [
                'name' => $token->name ?? null,
                'abilities' => $token->abilities ?? [],
                'expires_at' => $token->expires_at ?? null,
                'last_used_at' => $token->last_used_at ?? null,
            ],
        ];
    }
}
