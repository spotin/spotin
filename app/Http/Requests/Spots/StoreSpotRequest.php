<?php

namespace App\Http\Requests\Spots;

use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

// The fields of a spot, created in the organization of the URL, as in the API. The source of the creation from the form,
// which chooses the organization, and of the update of a spot, which changes what differs for an existing spot.
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
            // Needed once the spot is set up, checked by `nameIsGiven()`
            'name' => ['nullable', 'string', 'min:2', 'max:100', $this->uniqueName()],
            'description' => ['nullable', 'string', 'max:10000'],
            // The location is optional, but both coordinates are given together, checked by `locationIsComplete()`
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'website' => ['nullable', 'url:http,https', 'max:2048'],
            // Needs a website, checked by `redirectNeedsWebsite()`
            'redirect_to_website' => ['boolean'],
            // Hidden spots are only listed for the members of the organization, but stay reachable by their link
            'is_hidden' => ['boolean'],
            // To be set up on site, by the first person who scans the spot
            'needs_setup' => ['boolean'],
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [$this->nameIsGiven(...), $this->locationIsComplete(...), $this->redirectNeedsWebsite(...)];
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
     * The organization the spot is created in: the one of the URL.
     */
    public function organization(): ?Organization
    {
        return $this->route('organization');
    }

    /**
     * The name is unique in the organization of the spot: here, the one it is created in.
     */
    protected function uniqueName(): Unique
    {
        return Rule::unique(Spot::class, 'name')->where('organization_id', $this->organization()?->getKey());
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
     * Whether the spot will need to be set up on site.
     */
    protected function needsSetup(): bool
    {
        return $this->boolean('needs_setup');
    }

    /**
     * The name the spot will have.
     */
    protected function name(): ?string
    {
        return $this->input('name');
    }

    /**
     * Reject a spot without a name, unless it will be set up on site: the name is given then.
     */
    protected function nameIsGiven(Validator $validator): void
    {
        if (! $this->needsSetup() && blank($this->name()) && ! $validator->errors()->has('name')) {
            $validator->errors()->add('name', __('validation.required', ['attribute' => $this->attributes()['name']]));
        }
    }

    /**
     * The coordinate the spot will have: `latitude` or `longitude`.
     */
    protected function coordinate(string $field): mixed
    {
        return $this->input($field);
    }

    /**
     * Reject a location with a single coordinate, which no map can show.
     */
    protected function locationIsComplete(Validator $validator): void
    {
        if ($validator->errors()->hasAny(['latitude', 'longitude'])) {
            return;
        }

        foreach (['latitude', 'longitude'] as $field) {
            if (blank($this->coordinate($field)) && filled($this->coordinate($field === 'latitude' ? 'longitude' : 'latitude'))) {
                $validator->errors()->add($field, __('ui.common.map.incomplete'));
            }
        }
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
