<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * The filters of the lists of organizations and spots, on the pages as well as in the API.
 */
class ListFiltersRequest extends FormRequest
{
    /**
     * Get the validation rules of the filters.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'query' => ['nullable', 'string', 'max:100'],
            'mine' => ['nullable', 'boolean'],
        ];
    }

    /**
     * The text the names must contain, or an empty string.
     */
    public function searchQuery(): string
    {
        return trim((string) $this->validated('query'));
    }

    /**
     * Whether to keep only what belongs to the user: the organizations they are a member of, the spots they
     * created. Visitors have nothing of their own.
     */
    public function mine(): bool
    {
        return $this->user() !== null && $this->boolean('mine');
    }
}
