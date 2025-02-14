<?php

namespace App\Http\Controllers;

use App\Mail\VendorInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    public function inviteVendor(Request $request)
    {
        // Log incoming request data
        Log::info('Inviting vendor with data:', $request->all());

        // Validate request
        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ]);

        // Log validated data
        Log::info('Vendor invitation validation passed:', $validated);

        // Generate invite link for external domain
        $inviteLink = 'https://logistic2.gwamerchandise.com/register?vendor=' . base64_encode($validated['email']);
        Log::debug('Generated invite link:', ['link' => $inviteLink]);

        // Send the invitation email
        try {
            Log::info('Attempting to send invitation email to:', ['email' => $validated['email']]);

            Mail::to($validated['email'])->send(new VendorInvitationMail($validated['name'], $inviteLink));

            Log::info('Invitation email sent successfully to:', ['email' => $validated['email']]);

            return back()->with('success', 'Invitation sent successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to send invitation email:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to send invitation. ' . $e->getMessage());
        }
    }
}
