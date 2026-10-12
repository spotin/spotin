<?php

namespace App\Http\Controllers\Organizations;

use App\Actions\Organizations\AcceptInvitation;
use App\Actions\Organizations\InviteToOrganization;
use App\Enums\OrganizationRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizations\StoreInvitationRequest;
use App\Models\Organization;
use App\Models\OrganizationInvitation;
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
            ->with('status', 'invitation-sent');
    }

    /**
     * Send a pending invitation again.
     */
    public function resend(Request $request, InviteToOrganization $inviteToOrganization, Organization $organization, OrganizationInvitation $invitation): RedirectResponse
    {
        $inviteToOrganization->resend($invitation, $request->user());

        return back()->with('status', 'invitation-sent');
    }

    /**
     * Revoke a pending invitation.
     */
    public function destroy(Organization $organization, OrganizationInvitation $invitation): RedirectResponse
    {
        $invitation->delete();

        return back()->with('status', 'invitation-revoked');
    }

    /**
     * Accept the invitation matching the token, for the authenticated user.
     */
    public function accept(Request $request, AcceptInvitation $acceptInvitation, string $token): RedirectResponse
    {
        $organization = $acceptInvitation->accept($token, $request->user());

        return redirect()->route('organizations.show', ['organization' => $organization->uuid])
            ->with('status', 'invitation-accepted');
    }
}
