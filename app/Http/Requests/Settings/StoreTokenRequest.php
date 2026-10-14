<?php

namespace App\Http\Requests\Settings;

use App\Enums\TokenAbility;
use App\Enums\TokenExpiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTokenRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('personal_access_tokens', 'name')
                    ->where('tokenable_type', $user->getMorphClass())
                    ->where('tokenable_id', $user->getKey()),
            ],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => ['string', Rule::enum(TokenAbility::class)->only(TokenAbility::forUser($user))],
            'expiry' => ['required', Rule::enum(TokenExpiry::class)],
        ];
    }
}
