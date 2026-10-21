<?php

namespace App\Http\Resources;

use App\Models\Spot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\JsonApi\JsonApiResource;

/**
 * @mixin Spot
 */
class SpotResource extends JsonApiResource
{
    public function toId(Request $request): string
    {
        return $this->uuid;
    }

    public function toType(Request $request): string
    {
        return 'spots';
    }

    /**
     * @return array<string, mixed>
     */
    public function toAttributes(Request $request): array
    {
        return [
            'name' => $this->name,
            // Markdown
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'website' => $this->website,
            'redirect_to_website' => $this->redirect_to_website,
            'is_hidden' => $this->is_hidden,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * The relationships a client can include. The creator is `null` when the user cannot see it.
     *
     * @return array<string, mixed>
     */
    public function toRelationships(Request $request): array
    {
        return [
            'organization' => OrganizationResource::class,
            // Always a UserResource, possibly of nothing, so that the documentation knows the relationship.
            'creator' => fn () => new UserResource($this->canViewCreator($request) ? $this->creator : null),
        ];
    }

    /**
     * The organizations of the user are loaded once for the whole request, so that the policy checks the
     * membership without a query per spot of the list.
     */
    private function canViewCreator(Request $request): bool
    {
        return $request->user()->loadMissing('organizations')->can('viewCreator', $this->resource);
    }
}
