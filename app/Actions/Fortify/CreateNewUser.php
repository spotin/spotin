<?php

namespace App\Actions\Fortify;

use App\Models\OrganizationInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        // Fortify lowercases the email on login and password reset, so store it the same way.
        if (is_string($input['email'] ?? null)) {
            $input['email'] = Str::lower($input['email']);
        }

        Validator::make($input, [
            'name' => ['required', 'string', 'min:2', 'max:50'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = new User([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // The invitation email reached this address, so there is nothing left to verify.
        if (OrganizationInvitation::findInSession()?->email === $input['email']) {
            $user->email_verified_at = now();
        }

        $user->save();

        return $user;
    }
}
