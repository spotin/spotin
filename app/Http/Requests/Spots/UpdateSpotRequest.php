<?php

namespace App\Http\Requests\Spots;

use App\Models\Spot;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateSpotRequest extends StoreSpotRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // `sometimes`: a PATCH only changes the fields it sends
        $sometimes = fn (string $field) => ['sometimes', ...$rules[$field]];

        // Whoever sets the spot up cannot hide it, nor leave it to be set up again, nor give it custom data: these belong
        // to its members
        $membersOnly = fn (string $field) => $this->settingUp() ? ['prohibited'] : $sometimes($field);

        // Written out, without the organization, which the spot keeps: the comments are the descriptions of the fields
        // in the documentation of the API, as for the creation
        return [
            // Unique among the spots of its organization. Required once the spot is set up.
            'name' => $sometimes('name'),
            // In Markdown.
            'description' => $sometimes('description'),
            // The location is optional, but given with both `latitude` and `longitude`.
            'latitude' => $sometimes('latitude'),
            // Given with `latitude`.
            'longitude' => $sometimes('longitude'),
            // An `http` or `https` address, linked from the page of the spot.
            'website' => $sometimes('website'),
            // Sends who scans the spot straight to its `website`, which it then needs.
            'redirect_to_website' => $sometimes('redirect_to_website'),
            // Hidden spots are only listed for the members of the organization, but stay reachable by their link.
            'is_hidden' => $membersOnly('is_hidden'),
            // To be set up on site, by the first person who scans the spot.
            'needs_setup' => $membersOnly('needs_setup'),
            /**
             * A JSON object of your own, for your applications, such as `{"floor": 2}`: up to 10,000 characters once
             * encoded. `null` or `{}` removes it. Only returned to who manages the spot.
             *
             * @var array<string, mixed>|null
             */
            'custom_data' => $membersOnly('custom_data'),
        ];
    }

    /**
     * Whether the user only sets the spot up, as the first person who scans it, rather than managing it. Then, the
     * spot no longer needs to be set up once saved.
     */
    public function settingUp(): bool
    {
        $spot = $this->route('spot');

        return $spot?->needs_setup === true && $this->user()?->can('manage', $spot) !== true;
    }

    /**
     * The name is unique among the spots of its organization that are not deleted, the spot keeping its own name.
     */
    protected function uniqueName(): Unique
    {
        $spot = $this->route('spot');

        return Rule::unique(Spot::class, 'name')->where('organization_id', $spot?->organization_id)->ignore($spot)->withoutTrashed();
    }

    /**
     * The validated fields to save, which set the spot up when the user sets it up.
     *
     * @return array<string, mixed>
     */
    public function fields(): array
    {
        return [...parent::fields(), ...($this->settingUp() ? ['needs_setup' => false] : [])];
    }

    /**
     * Whether the spot will need to be set up on site: never once set up, otherwise as sent, or as it does now.
     */
    protected function needsSetup(): bool
    {
        if ($this->settingUp()) {
            return false;
        }

        return $this->has('needs_setup') ? $this->boolean('needs_setup') : (bool) $this->route('spot')?->needs_setup;
    }

    /**
     * The name the spot will have: as sent, or the one it has now.
     */
    protected function name(): ?string
    {
        return $this->has('name') ? $this->input('name') : $this->route('spot')?->name;
    }

    /**
     * The coordinate the spot will have: as sent, or the one it has now.
     */
    protected function coordinate(string $field): mixed
    {
        return $this->has($field) ? $this->input($field) : $this->route('spot')?->{$field};
    }

    /**
     * Whether the spot will redirect its visitors: as sent, or as it does now.
     */
    protected function redirects(): bool
    {
        return $this->has('redirect_to_website')
            ? $this->boolean('redirect_to_website')
            : (bool) $this->route('spot')?->redirect_to_website;
    }

    /**
     * The website the spot will have: as sent, or the one it has now.
     */
    protected function website(): ?string
    {
        return $this->has('website') ? $this->input('website') : $this->route('spot')?->website;
    }
}
