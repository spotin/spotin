<?php

namespace App\Http\Requests\Spots;

use App\Models\Spot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpotRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $spot = $this->route('spot');

        return [
            'name' => [
                'required', 'string', 'min:2', 'max:100',
                Rule::unique(Spot::class, 'name')->where('organization_id', $spot->organization_id)->ignore($spot),
            ],
        ];
    }
}
