<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationRequest;
use App\Http\Requests\Admin\UpdateOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class OrganizationController extends Controller
{
    /**
     * Create an organization, then show it so people can be invited.
     */
    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $organization = Organization::create($request->validated());

        return redirect()->route('organizations.show', ['organization' => $organization->uuid])->with('status', 'organization-created');
    }

    /**
     * Update the organization.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $organization->update($request->validated());

        return back()->with('status', 'organization-updated');
    }

    /**
     * Permanently delete the organization.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()->route('admin.organizations.index')->with('status', 'organization-deleted');
    }
}
