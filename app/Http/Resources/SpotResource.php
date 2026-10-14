<?php

namespace App\Http\Resources;

use App\Models\Spot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Spot
 */
class SpotResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'organization' => $this->whenLoaded('organization', fn () => [
                'id' => $this->organization->uuid,
                'name' => $this->organization->name,
            ]),
            $this->mergeWhen($this->canViewCreator($request), fn () => [
                'creator' => $this->creator ? ['name' => $this->creator->name] : null,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Same rule as SpotPolicy::viewCreator, with the organizations of the user loaded once per request
     * instead of once per spot of the list.
     */
    private function canViewCreator(Request $request): bool
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return true;
        }

        $organizations = $request->attributes->get('member_organization_ids')
            ?? tap($user->organizations()->pluck('organizations.id')->all(), fn (array $ids) => $request->attributes->set('member_organization_ids', $ids));

        return in_array($this->organization_id, $organizations);
    }
}
