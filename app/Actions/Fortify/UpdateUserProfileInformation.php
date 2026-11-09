<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * Only the fields sent are updated: the profile form sends them all, the dashboard only whether to show the
     * checklist of the first steps.
     *
     * @param  array<string, mixed>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        // Fortify lowercases the email on login and password reset, so store it the same way.
        if (is_string($input['email'] ?? null)) {
            $input['email'] = Str::lower($input['email']);
        }

        $validated = Validator::make($input, [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:50'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'show_getting_started' => ['sometimes', 'boolean'],
        ], [], array_map(Str::lcfirst(...), [
            'name' => __('ui.auth.common.forms.fields.name.label'),
            'email' => __('ui.auth.common.forms.fields.email.label'),
        ]))->validateWithBag('updateProfileInformation');

        $user->fill($validated);

        // A new address has to be verified again
        if ($user->isDirty('email') && $user instanceof MustVerifyEmail) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();

            return;
        }

        $user->save();
    }
}
