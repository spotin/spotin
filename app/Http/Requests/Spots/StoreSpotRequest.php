<?php

namespace App\Http\Requests\Spots;

use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'organization' => ['required', 'string', Rule::exists(Organization::class, 'uuid')],
            'name' => [
                'required', 'string', 'min:2', 'max:100',
                Rule::unique(Spot::class, 'name')->where('organization_id', $this->organization()?->getKey()),
            ],
        ];
    }

    /**
     * The organization the spot is created in.
     */
    public function organization(): ?Organization
    {
        return Organization::firstWhere('uuid', $this->input('organization'));
    }
}
