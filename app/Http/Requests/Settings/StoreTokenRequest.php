<?php

namespace App\Http\Requests\Settings;

use App\Enums\TokenAbility;
use App\Enums\TokenExpiry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
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
            'name' => __('ui.settings.developer.common.forms.fields.name.label'),
            'abilities' => __('ui.settings.developer.common.forms.fields.abilities.label'),
            'abilities.*' => __('ui.settings.developer.common.forms.fields.abilities.label'),
            'expiry' => __('ui.settings.developer.common.forms.fields.expiry.label'),
        ]);
    }
}
