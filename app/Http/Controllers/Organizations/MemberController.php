<?php

namespace App\Http\Controllers\Organizations;

use App\Actions\Organizations\ChangeMemberRole;
use App\Actions\Organizations\RemoveMember;
use App\Enums\OrganizationRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizations\UpdateMemberRoleRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Change the role of a member of the organization.
     */
    public function update(UpdateMemberRoleRequest $request, ChangeMemberRole $changeMemberRole, Organization $organization, User $user): RedirectResponse
    {
        $changeMemberRole->change($organization, $user, OrganizationRole::from($request->validated('role')));

        return back()->with('success', __('ui.organizations.members.feedback.member_role_updated'));
    }

    /**
     * Remove a member from the organization.
     */
    public function destroy(RemoveMember $removeMember, Organization $organization, User $user): RedirectResponse
    {
        $removeMember->remove($organization, $user);

        return back()->with('success', __('ui.organizations.members.feedback.member_removed'));
    }

    /**
     * Let the authenticated user leave the organization.
     */
    public function leave(Request $request, RemoveMember $removeMember, Organization $organization): RedirectResponse
    {
        $removeMember->remove($organization, $request->user());

        return redirect()->route('organizations.index', ['mine' => 1])->with('success', __('ui.organizations.members.feedback.organization_left'));
    }
}
