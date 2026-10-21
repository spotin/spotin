<?php

namespace App\Http\Requests\Organizations;

use App\Enums\OrganizationRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateMemberRoleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', Rule::enum(OrganizationRole::class)],
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
            'role' => __('ui.organizations.common.forms.fields.role.label'),
        ]);
    }
}
