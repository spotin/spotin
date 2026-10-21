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
        // `sometimes`: a PATCH only changes the fields it sends
        return array_map(fn (array $rules) => ['sometimes', ...$rules], parent::rules());
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
