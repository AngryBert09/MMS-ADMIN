<?php

namespace App\Http\Controllers;

use App\Mail\VendorInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Mail\VendorStatusMail;

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

        $combinedData = $validated['email'] . '|' . $validated['name'];
        // Encrypt the combined data
        $encryptedData = Crypt::encryptString($combinedData);
        // URL encode the encrypted data
        $inviteLink = 'https://logistic2.gwamerchandise.com/register?data=' . urlencode($encryptedData);

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

    public function getVendorApplications()
    {
        // Log that the function is being called
        Log::info('Fetching vendor applications from API');

        // Retrieve your API key (stored in .env and referenced in your config)
        $apiKey = env('LOGISTIC2_API_KEY'); // e.g., 'YOUR_API_KEY'

        // Make a GET request to the API including the API key in the headers
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json'
        ])->get('https://logistic2.gwamerchandise.com/api/vendors');

        // Log the response status and body for debugging
        Log::info('API Response Status: ' . $response->status());
        Log::info('API Response Body: ' . $response->body());

        // Check if the request was successful
        if ($response->successful()) {
            Log::info('API request successful, returning vendor data');

            // Return the view with the vendor data
            return view('admin.users.vendor-application', ['vendors' => $response->json()]);
        } else {
            // Log the error
            Log::error('Failed to fetch vendor data from API. Status: ' . $response->status());

            // Handle error, maybe log or show a message to the user
            return view('admin.users.vendor-application', ['error' => 'Failed to fetch data']);
        }
    }

    public function updateVendorStatus($vendorId, $status, Request $request)
    {
        // Log the status change attempt
        Log::info("Updating vendor status: Vendor ID {$vendorId}, New Status: {$status}");

        // Retrieve API key from environment (ensure it's set in .env)
        $apiKey = env('LOGISTIC2_API_KEY'); // e.g., 'YOUR_API_KEY'
        $rejectReason = $request->input('reject_reason');

        // Make a PATCH request to update the vendor status, including the API key in the headers
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->patch("https://logistic2.gwamerchandise.com/api/vendor/{$vendorId}", [
            'status' => $status,  // Assuming the API expects a 'status' field in the body
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            Log::info("Vendor status updated successfully: Vendor ID {$vendorId}, Status: {$status}");

            // For both Approved and Rejected statuses, send an email notification
            // Fetch vendor details from the external API using the API key
            $vendorResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->get("https://logistic2.gwamerchandise.com/api/vendor", [
                'id' => $vendorId
            ]);

            if ($vendorResponse->successful()) {
                $vendorData = $vendorResponse->json();
                Log::info("Fetched vendor data for email notification: " . json_encode($vendorData));

                if (isset($vendorData['email'])) {
                    Log::debug("Attempting to send status email to vendor with email: " . $vendorData['email']);
                    Log::debug("Vendor Data: " . json_encode($vendorData));
                    try {
                        // Use the same mailable for both Approved and Rejected statuses.
                        Mail::to($vendorData['email'])->send(new VendorStatusMail($vendorData, $status, $rejectReason));
                        Log::info("Status email sent successfully to Vendor ID {$vendorId}, Email: " . $vendorData['email']);
                    } catch (\Exception $e) {
                        Log::error("Failed to send status email to Vendor ID {$vendorId}. Error: " . $e->getMessage());
                    }
                } else {
                    Log::warning("Vendor email not found in API response for Vendor ID {$vendorId}.");
                }
            } else {
                Log::error("Failed to fetch vendor details for Vendor ID {$vendorId}. Status: " . $vendorResponse->status());
            }

            return redirect()->back()->with('success', 'Vendor status updated successfully');
        } else {
            Log::error("Failed to update vendor status: Vendor ID {$vendorId}, Status: {$status}, Error: " . $response->body());
            return redirect()->back()->with('error', 'Failed to update vendor status');
        }
    }
}
