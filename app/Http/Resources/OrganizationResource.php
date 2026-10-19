<?php

namespace App\Http\Resources;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;

/**
 * @mixin Organization
 */
class OrganizationResource extends JsonApiResource
{
    public function toId(Request $request): string
    {
        return $this->uuid;
    }

    public function toType(Request $request): string
    {
        return 'organizations';
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            'spots_count' => $this->whenCounted('spots'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
