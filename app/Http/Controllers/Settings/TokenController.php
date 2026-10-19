<?php

namespace App\Http\Controllers\Settings;

use App\Enums\TokenExpiry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreTokenRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * Create a token for the user. Its plain text value is shown only once, right after.
     */
    public function store(StoreTokenRequest $request): RedirectResponse
    {
        $token = $request->user()->createToken(
            $request->validated('name'),
            array_values(array_unique($request->validated('abilities'))),
            TokenExpiry::from($request->validated('expiry'))->expiresAt(),
        );

        return redirect()->route('settings.developer')
            ->with('success', __('ui.settings.developer.feedback.token_created'))
            ->with('new-token', $token->plainTextToken);
    }

    /**
     * Revoke one of the tokens of the user.
     */
    public function destroy(Request $request, int $token): RedirectResponse
    {
        $request->user()->tokens()->findOrFail($token)->delete();

        return redirect()->route('settings.developer')->with('success', __('ui.settings.developer.feedback.token_revoked'));
    }
}
