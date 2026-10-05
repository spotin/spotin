<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthSignUpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|alpha_dash:ascii|min:3|max:30|unique:users',
            'name' => 'required|string|min:2|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|max:128|confirmed',
        ];
    }
}
