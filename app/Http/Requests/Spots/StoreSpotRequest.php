<?php

namespace App\Http\Requests\Spots;

use App\Models\Organization;
use App\Models\Spot;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

// The fields of a spot, created in the organization it chooses among the ones the user creates spots in, from the form as
// from the API. The source of the update of a spot, which changes what differs for an existing spot.
// Not a doc comment: Scramble would show it in the API documentation.
class StoreSpotRequest extends FormRequest
{
    /**
     * The longest custom data, in characters once encoded.
     */
    public const CUSTOM_DATA_MAX = 10000;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // The comments are the descriptions of the fields in the documentation of the API: for its users
        return [
            // The UUID of the organization of the spot, one you are a member of. It cannot be changed afterwards.
            'organization' => ['bail', 'required', 'uuid', Rule::exists(Organization::class, 'uuid'), $this->organizationAllowsCreation(...)],
            // Required, unless the spot is set up on site (`needs_setup`): the first person who scans it names it then.
            // Unique among the spots of its organization.
            'name' => ['nullable', 'string', 'min:2', 'max:100', $this->uniqueName()],
            // In Markdown.
            'description' => ['nullable', 'string', 'max:10000'],
            // The location is optional, but given with both `latitude` and `longitude`.
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            // Given with `latitude`.
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            // An `http` or `https` address, linked from the page of the spot.
            'website' => ['nullable', 'url:http,https', 'max:2048'],
            // Sends who scans the spot straight to its `website`, which it then needs.
            'redirect_to_website' => ['boolean'],
            // Hidden spots are only listed for the members of the organization, but stay reachable by their link.
            'is_hidden' => ['boolean'],
            // To be set up on site, by the first person who scans the spot.
            'needs_setup' => ['boolean'],
            /**
             * A JSON object of your own, for your applications, such as `{"floor": 2}`: up to 10,000 characters once
             * encoded. `null` or `{}` removes it. Only returned to who manages the spot.
             *
             * @var array<string, mixed>|null
             */
            'custom_data' => ['nullable', 'array', $this->customDataIsObject(...), $this->customDataFits(...)],
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
            'organization' => __('ui.spots.common.forms.fields.organization.label'),
            'name' => __('ui.spots.common.forms.fields.name.label'),
            'description' => __('ui.spots.common.forms.fields.description.label'),
            'latitude' => __('ui.common.map.fields.latitude.label'),
            'longitude' => __('ui.common.map.fields.longitude.label'),
            'website' => __('ui.spots.common.forms.fields.website.label'),
            'custom_data' => __('ui.spots.common.forms.fields.custom_data.label'),
        ]);
    }

    /**
     * The validated fields to save: the custom data as sent, read again from the JSON body, where an empty object stays
     * an object rather than an empty list.
     *
     * @return array<string, mixed>
     */
    public function fields(): array
    {
        $fields = $this->validated();

        if (array_key_exists('custom_data', $fields) && $this->isJson()) {
            $fields['custom_data'] = json_decode($this->getContent())->custom_data;
        }

        return $fields;
    }

    /**
     * The organization the spot is created in: the one chosen, if it exists.
     */
    public function organization(): ?Organization
    {
        return Organization::firstWhere('uuid', $this->input('organization'));
    }

    /**
     * Reject an organization the user does not create spots in. A validation error rather than a refusal of the request:
     * the organization is a field like the others. Only checked for an existing organization, thanks to `bail`.
     */
    protected function organizationAllowsCreation(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->user()?->cannot('create', [Spot::class, $this->organization()])) {
            $fail(__('ui.spots.common.forms.fields.organization.not_allowed'));
        }
    }

    /**
     * Reject a list, such as `[1, 2]`: the custom data are an object, of named values. `{}` and `[]` are the same once
     * decoded, and both remove them.
     */
    protected function customDataIsObject(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value) && $value !== [] && array_is_list($value)) {
            $fail(__('ui.spots.feedback.custom_data_object'));
        }
    }

    /**
     * Reject custom data longer than 10,000 characters once encoded, as stored.
     */
    protected function customDataFits(string $attribute, mixed $value, Closure $fail): void
    {
        if (mb_strlen((string) json_encode($value)) > self::CUSTOM_DATA_MAX) {
            $fail(__('ui.spots.feedback.custom_data_too_large', ['max' => self::CUSTOM_DATA_MAX]));
        }
    }

    /**
     * The name is unique among the spots of the organization that are not deleted: here, the one it is created in.
     */
    protected function uniqueName(): Unique
    {
        return Rule::unique(Spot::class, 'name')->where('organization_id', $this->organization()?->getKey())->withoutTrashed();
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
