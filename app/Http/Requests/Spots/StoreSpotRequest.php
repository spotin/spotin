<?php

namespace App\Http\Requests\Spots;

use App\Models\Spot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

// The fields of a spot, created in the organization of the URL. The source of the update of a spot, which changes what
// differs for an existing spot.
// Not a doc comment: Scramble would show it in the API documentation.
class StoreSpotRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', $this->uniqueName()],
            'description' => ['nullable', 'string', 'max:10000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'website' => ['nullable', 'url:http,https', 'max:2048'],
            // Needs a website, checked by `redirectNeedsWebsite()`
            'redirect_to_website' => ['boolean'],
            // Hidden spots are only listed for the members of the organization, but stay reachable by their link
            'is_hidden' => ['boolean'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [$this->redirectNeedsWebsite(...)];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * The labels of the fields of the form, in the sentences of the messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return array_map(Str::lcfirst(...), [
            'name' => __('ui.spots.common.forms.fields.name.label'),
            'description' => __('ui.spots.common.forms.fields.description.label'),
            'latitude' => __('ui.common.map.fields.latitude.label'),
            'longitude' => __('ui.common.map.fields.longitude.label'),
            'website' => __('ui.spots.common.forms.fields.website.label'),
        ]);
    }

    /**
     * The name is unique in the organization of the spot: here, the one of the URL.
     */
    protected function uniqueName(): Unique
    {
        return Rule::unique(Spot::class, 'name')->where('organization_id', $this->route('organization')?->getKey());
    }

    /**
     * Whether the spot will redirect its visitors to its website.
     */
    protected function redirects(): bool
    {
        return $this->boolean('redirect_to_website');
    }

    /**
     * The website the spot will have.
     */
    protected function website(): ?string
    {
        return $this->input('website');
    }

    /**
     * Reject a redirect without a website, which would leave the visitors nowhere to go.
     */
    protected function redirectNeedsWebsite(Validator $validator): void
    {
        if ($this->redirects() && blank($this->website()) && ! $validator->errors()->has('website')) {
            $validator->errors()->add(
                'redirect_to_website',
                __('ui.spots.common.forms.fields.redirect_to_website.needs_website'),
            );
        }
    }
}
