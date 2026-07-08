<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSpotRequest extends FormRequest
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
            'name' => ['sometimes', 'nullable', 'string', 'min:1', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'min:1'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'min:-90', 'max:90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'min:-180', 'max:180'],
            'website_target' => ['sometimes', 'nullable', 'url', 'max:255'],
            'direct_access_to_website_target' => ['sometimes', 'boolean'],
            'configured' => ['sometimes', 'boolean'],
            'public' => ['sometimes', 'boolean'],
            'payload' => ['sometimes', 'nullable', 'json', 'max:1000'],
        ];
    }
}
