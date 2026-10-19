<?php

namespace App\Http\Requests\Spots;

use App\Models\Spot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSpotRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * The spot is created in the organization of the URL.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'min:2', 'max:100',
                Rule::unique(Spot::class, 'name')->where('organization_id', $this->route('organization')?->getKey()),
            ],
        ];
    }
}
