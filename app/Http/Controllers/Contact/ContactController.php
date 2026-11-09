<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\StoreContactRequest;
use App\Models\User;
use App\Notifications\ContactNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class ContactController extends Controller
{
    /**
     * Send the message of the contact form to the administrators of the platform, then go back to the form.
     */
    public function store(StoreContactRequest $request): RedirectResponse
    {
        Notification::send(User::where('is_admin', true)->get(), new ContactNotification(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('organization'),
            $request->validated('message'),
        ));

        return redirect()->route('contact')->with('success', __('ui.contact.feedback.sent'));
    }
}
