<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
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
            'current_password' => ['required', 'current_password'],
            'username' => ['sometimes', 'string', 'max:255', Rule::unique('users')->ignore($this->user()->id)],
            'bio' => ['sometimes', 'nullable', 'string'],
            'new_password' => ['sometimes', 'nullable', 'string', 'min:8', 'max:255'],
        ];
    }
}
