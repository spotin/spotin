<?php

namespace App\Http\Controllers\Users;

use App\Actions\Users\ChangeUserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Make a user an administrator of the platform, or no longer.
     */
    public function update(UpdateUserRoleRequest $request, ChangeUserRole $changeUserRole, User $user): RedirectResponse
    {
        $changeUserRole->change($user, $request->boolean('is_admin'));

        // No longer an administrator, the user can no longer see the list of the users
        $redirect = $user->is($request->user()) && ! $user->is_admin ? redirect()->route('dashboard') : back();

        return $redirect->with('success', __('ui.users.feedback.role_updated'));
    }
}
