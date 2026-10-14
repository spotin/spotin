<?php

namespace App\Http\Controllers\Invitations;

use App\Actions\Invitations\AcceptInvitation;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    /**
     * Accept one of the pending invitations of the authenticated user.
     */
    public function accept(Request $request, AcceptInvitation $acceptInvitation, int $invitation): RedirectResponse
    {
        $organization = $acceptInvitation->acceptInvitation(
            Invitation::pendingFor($request->user())->findOrFail($invitation),
            $request->user(),
        );

        $request->session()->forget(Invitation::SESSION_KEY);

        return redirect()->route('organizations.show', ['organization' => $organization->uuid])
            ->with('success', __('ui.invitations.feedback.invitation_accepted'));
    }

    /**
     * Decline one of the pending invitations of the authenticated user.
     */
    public function decline(Request $request, int $invitation): RedirectResponse
    {
        Invitation::pendingFor($request->user())->findOrFail($invitation)->delete();

        return back()->with('success', __('ui.invitations.feedback.invitation_declined'));
    }
}
