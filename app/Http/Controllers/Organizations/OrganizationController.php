<?php

namespace App\Http\Controllers\Organizations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organizations\StoreOrganizationRequest;
use App\Http\Requests\Organizations\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Support\QrCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class OrganizationController extends Controller
{
    /**
     * The address of the QR code of the organization: its page, which says it was just scanned.
     */
    public function redirect(Organization $organization): RedirectResponse
    {
        return redirect()->route('organizations.show', ['organization' => $organization->uuid])->with('scanned', true);
    }

    /**
     * Download the QR code of the organization, to print, as SVG or PNG: it leads to the address of its scans.
     */
    public function qrCode(Organization $organization, string $format): Response
    {
        return QrCode::download(route('organizations.redirect', ['organization' => $organization->uuid]), $organization->name, $format);
    }

    /**
     * Create an organization, then show it so people can be invited.
     */
    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $organization = Organization::create($request->validated());

        return redirect()->route('organizations.show', ['organization' => $organization->uuid])->with('success', __('ui.organizations.feedback.organization_created'));
    }

    /**
     * Update the organization.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $organization->update($request->validated());

        return back()->with('success', __('ui.organizations.feedback.organization_updated'));
    }

    /**
     * Permanently delete the organization.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()->route('organizations.index')->with('success', __('ui.organizations.feedback.organization_deleted'));
    }
}
