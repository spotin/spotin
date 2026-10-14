<?php

namespace App\Http\Controllers\Organizations;

use App\Actions\Organizations\InviteToOrganization;
use App\Enums\OrganizationRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizations\StoreInvitationRequest;
use App\Models\Invitation;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /**
     * Invite an email address to join the organization.
     */
    public function store(StoreInvitationRequest $request, InviteToOrganization $inviteToOrganization, Organization $organization): RedirectResponse
    {
        $inviteToOrganization->invite($organization, $request->validated('email'), OrganizationRole::from($request->validated('role')), $request->user());

        return redirect()->route('organizations.invitations.index', ['organization' => $organization->uuid])
            ->with('success', __('ui.organizations.invitations.feedback.invitation_sent'));
    }

    /**
     * Send a pending invitation again.
     */
    public function resend(Request $request, InviteToOrganization $inviteToOrganization, Organization $organization, Invitation $invitation): RedirectResponse
    {
        $inviteToOrganization->resend($invitation, $request->user());

        return back()->with('success', __('ui.organizations.invitations.feedback.invitation_sent'));
    }

    /**
     * Revoke a pending invitation.
     */
    public function destroy(Organization $organization, Invitation $invitation): RedirectResponse
    {
        $invitation->delete();

        return back()->with('success', __('ui.organizations.invitations.feedback.invitation_revoked'));
    }
}
