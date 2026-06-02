<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Mail\ContactUsMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Store a new Contact Us message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            // Save to database
            $contact = Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            // Send notification email to admin
            $adminEmail = config('mail.from.address');

            if ($adminEmail) {
                Mail::to($adminEmail)->send(new ContactUsMail($contact));
            } else {
                Log::warning('Contact Us form submitted but MAIL_FROM_ADDRESS is not set.');
            }

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully! The administrator will contact you soon.',
                'data' => $contact
            ], 201);

        } catch (\Exception $e) {
            Log::error('Contact Us submission error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending your message. Please try again later.'
            ], 500);
        }
    }
}
