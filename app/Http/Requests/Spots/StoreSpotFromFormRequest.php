<?php

namespace App\Http\Requests\Spots;

use App\Models\Organization;
use App\Models\Spot;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

// The fields of a spot created from its form, which chooses its organization among the ones the user creates spots in.
class StoreSpotFromFormRequest extends StoreSpotRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Checked by `organizationAllowsCreation()`
            'organization' => ['required', 'uuid', Rule::exists(Organization::class, 'uuid')],
            ...parent::rules(),
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [$this->organizationAllowsCreation(...), ...parent::after()];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'organization' => Str::lcfirst(__('ui.spots.common.forms.fields.organization.label')),
            ...parent::attributes(),
        ];
    }

    /**
     * The organization the spot is created in: the one chosen in the form, if it exists.
     */
    public function organization(): ?Organization
    {
        return Organization::firstWhere('uuid', $this->input('organization'));
    }

    /**
     * Reject an organization the user does not create spots in.
     */
    protected function organizationAllowsCreation(Validator $validator): void
    {
        $organization = $this->organization();

        if ($organization && $this->user()?->cannot('create', [Spot::class, $organization]) && ! $validator->errors()->has('organization')) {
            $validator->errors()->add('organization', __('ui.spots.common.forms.fields.organization.not_allowed'));
        }
    }
}
