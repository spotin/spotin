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
            $this->filter('query') => ['nullable', 'string', 'max:100'],
            $this->filter('mine') => ['nullable', 'boolean'],
        ];
    }

    /**
     * Accept `true` and `false` for `mine`, which the `boolean` rule refuses as text, as a query string sends them.
     */
    protected function prepareForValidation(): void
    {
        $query = $this->query->all();
        $mine = data_get($query, $this->filter('mine'));

        if (in_array($mine, ['true', 'false'], true)) {
            data_set($query, $this->filter('mine'), $mine === 'true');
            $this->query->replace($query);
        }
    }

    /**
     * The key of a filter in the query string.
     */
    protected function filter(string $name): string
    {
        return $name;
    }

    /**
     * The text the names must contain, or an empty string.
     */
    public function searchQuery(): string
    {
        return (string) $this->validated($this->filter('query'));
    }

    /**
     * Whether to keep only what belongs to the user: the organizations they are a member of, the spots they
     * created. Visitors have nothing of their own.
     */
    public function mine(): bool
    {
        return $this->user() !== null && $this->boolean($this->filter('mine'));
    }
}
