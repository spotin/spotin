<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangeUserRole
{
    /**
     * Make the user an administrator of the platform, or no longer.
     *
     * The platform always keeps at least one administrator.
     *
     * @throws ValidationException
     */
    public function change(User $user, bool $isAdmin): void
    {
        DB::transaction(function () use ($user, $isAdmin): void {
            // Lock the administrators so two concurrent changes cannot both pass the check below.
            $adminIds = User::where('is_admin', true)->lockForUpdate()->pluck('id');

            if (! $isAdmin && $adminIds->count() === 1 && $adminIds->first() === $user->id) {
                throw ValidationException::withMessages(['user' => __('ui.users.feedback.last_admin')]);
            }

            // Not fillable, so no form can make someone an administrator by mistake
            $user->is_admin = $isAdmin;
            $user->save();
        });
    }
}
