<?php

namespace App\Http\Requests\Organizations;

use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

// The fields of an organization. The source of the update of an organization, which changes what differs for an existing
// organization.
// Not a doc comment: Scramble would show it in the API documentation.
class StoreOrganizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', $this->uniqueName()],
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
            'name' => __('ui.organizations.common.forms.fields.name.label'),
        ]);
    }

    /**
     * The name is unique among the organizations.
     */
    protected function uniqueName(): Unique
    {
        return Rule::unique(Organization::class, 'name');
    }
}
