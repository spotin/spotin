<?php

namespace App\Http\Requests;

/**
 * The filters of the lists in the API, under `filter` as JSON:API recommends: `filter[query]` and `filter[mine]`.
 */
class ApiListFiltersRequest extends ListFiltersRequest
{
    /**
     * Get the validation rules of the filters.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array:query,mine'],
            ...parent::rules(),
        ];
    }

    protected function filter(string $name): string
    {
        return "filter.$name";
    }
}
