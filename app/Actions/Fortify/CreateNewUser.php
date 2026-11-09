<?php

namespace App\Actions\Fortify;

use App\Actions\Invitations\AcceptInvitation;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function __construct(private AcceptInvitation $acceptInvitation) {}

    /**
     * Validate and create a newly registered user, who joins the organization of the invitation they follow.
     *
     * Accounts are only created by invitation: with the address the invitation was sent to, which the email therefore
     * verifies.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        $invitation = Invitation::findInSession();

        if (! $invitation) {
            throw ValidationException::withMessages(['email' => __('ui.auth.register.invitation_only.required')]);
        }

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
                Rule::in([$invitation->email]),
            ],
            'password' => $this->passwordRules(),
        ], [
            // The address is filled in from the invitation: any other one did not come from it
            'email.in' => __('ui.auth.register.invitation_only.required'),
        ], array_map(Str::lcfirst(...), [
            'name' => __('ui.auth.common.forms.fields.name.label'),
            'email' => __('ui.auth.common.forms.fields.email.label'),
            'password' => __('ui.auth.common.forms.fields.password.label'),
        ]))->validate();

        $organization = null;

        $user = DB::transaction(function () use ($input, $invitation, &$organization): User {
            $user = new User([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            // The invitation email reached this address, so there is nothing left to verify.
            $user->email_verified_at = now();
            $user->save();

            $organization = $this->acceptInvitation->acceptInvitation($invitation, $user);

            return $user;
        });

        // Fortify then logs the user in and takes them to the organization they just joined.
        session()->forget(Invitation::SESSION_KEY);
        session(['url.intended' => route('organizations.show', ['organization' => $organization->uuid])]);
        session()->flash('success', __('ui.invitations.feedback.invitation_accepted'));

        return $user;
    }
}
