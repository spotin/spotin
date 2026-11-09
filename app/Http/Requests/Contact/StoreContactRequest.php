<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreContactRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'organization' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            // Hidden from people: only the bots that fill every field fill it
            'website' => ['prohibited'],
        ];
    }

    /**
     * Go back to the form, at the bottom of the home page, to show its errors.
     */
    protected function getRedirectUrl(): string
    {
        return route('index').'#contact';
    }

    /**
     * Normalize the email address before it is validated.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => Str::lower($this->input('email'))]);
        }
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
            'name' => __('ui.contact.fields.name.label'),
            'email' => __('ui.contact.fields.email.label'),
            'organization' => __('ui.contact.fields.organization.label'),
            'message' => __('ui.contact.fields.message.label'),
        ]);
    }
}
