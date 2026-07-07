<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSpotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'min:1'],
            'latitude' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'website_target' => ['nullable', 'url', 'max:255'],
            'direct_access_to_website_target' => ['boolean', 'required_with:website_target'],
            'configured' => ['boolean'],
            'public' => ['boolean'],
            'payload' => ['nullable', 'json', 'max:1000'],
        ];
    }
}
