<?php

namespace App\Http\Requests\Spots;

use App\Http\Requests\ApiListFiltersRequest;
use App\Models\Organization;
use Illuminate\Validation\Rule;

/**
 * The filters of the list of spots in the API, which can also keep the spots of one organization: `filter[organization]`.
 */
class ApiSpotListFiltersRequest extends ApiListFiltersRequest
{
    /**
     * Get the validation rules of the filters.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'filter' => ['nullable', 'array:query,mine,organization'],
            // An unknown organization is an error rather than an empty list, so that a mistyped uuid does not look like no spots
            $this->filter('organization') => ['nullable', 'uuid', Rule::exists(Organization::class, 'uuid')],
        ];
    }

    /**
     * The organization whose spots to keep, or `null` for the spots of all of them.
     */
    public function organization(): ?Organization
    {
        $uuid = $this->validated($this->filter('organization'));

        return $uuid === null ? null : Organization::firstWhere('uuid', $uuid);
    }
}
