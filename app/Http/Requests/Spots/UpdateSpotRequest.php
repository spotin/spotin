<?php

namespace App\Http\Requests\Spots;

use App\Models\Spot;
use Illuminate\Support\Arr;
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
        // The spot keeps its organization
        $rules = Arr::except(parent::rules(), 'organization');

        // `sometimes`: a PATCH only changes the fields it sends
        $rules = array_map(fn (array $rules) => ['sometimes', ...$rules], $rules);

        // Whoever sets the spot up cannot hide it, nor leave it to be set up again: these belong to its members
        if ($this->settingUp()) {
            $rules['is_hidden'] = ['prohibited'];
            $rules['needs_setup'] = ['prohibited'];
        }

        return $rules;
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
     * The name is unique in the organization of the spot, which keeps its own name.
     */
    protected function uniqueName(): Unique
    {
        $spot = $this->route('spot');

        return Rule::unique(Spot::class, 'name')->where('organization_id', $spot?->organization_id)->ignore($spot);
    }

    /**
     * The validated fields to save, which set the spot up when the user sets it up.
     *
     * @return array<string, mixed>
     */
    public function fields(): array
    {
        return [...$this->validated(), ...($this->settingUp() ? ['needs_setup' => false] : [])];
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
