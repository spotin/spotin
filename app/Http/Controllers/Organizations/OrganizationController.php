<?php

namespace App\Http\Controllers\Organizations;

use App\Actions\Organizations\DeleteOrganization;
use App\Actions\Organizations\RestoreOrganization;
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
     * The address of the QR code of the organization: its page, which says it was just scanned. Once the organization
     * is deleted, a page that tells the code is no longer in use, as gone.
     */
    public function redirect(Organization $organization): RedirectResponse|Response
    {
        if ($organization->trashed()) {
            return response()->view('removed', [
                'title' => __('ui.qr_codes.inactive.title'),
                'text' => __('ui.qr_codes.inactive.organization'),
            ], Response::HTTP_GONE);
        }

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
    public function destroy(Organization $organization, DeleteOrganization $deleteOrganization): RedirectResponse
    {
        $deleteOrganization->delete($organization);

        return redirect()->route('organizations.index')
            ->with('success', __('ui.organizations.feedback.organization_deleted'))
            ->with('undo', route('organizations.restore', ['organization' => $organization->uuid]));
    }

    /**
     * Restore the deleted organization, with the spots deleted with it.
     */
    public function restore(Organization $organization, RestoreOrganization $restoreOrganization): RedirectResponse
    {
        $restoreOrganization->restore($organization);

        return redirect()->route('organizations.show', ['organization' => $organization->uuid])->with('success', __('ui.organizations.feedback.organization_restored'));
    }
}
