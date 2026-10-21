<?php

namespace App\Http\Requests\Organizations;

use App\Models\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

// The fields of an organization, as when it is created, for the organization of the URL.
// Not a doc comment: Scramble would show it in the API documentation.
class UpdateOrganizationRequest extends StoreOrganizationRequest
{
    /**
     * The name is unique among the organizations, which keeps its own name.
     */
    protected function uniqueName(): Unique
    {
        return Rule::unique(Organization::class, 'name')->ignore($this->route('organization'));
    }
}
