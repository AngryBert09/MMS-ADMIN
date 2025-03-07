<?php

namespace App\Http\Controllers;

use App\Mail\VendorInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use App\Mail\VendorStatusMail;
use App\Traits\ActivityLogger;

class VendorController extends Controller
{
    use ActivityLogger;

    public function inviteVendor(Request $request)
    {
        Log::info('Inviting vendor with data:', $request->all());

        $validated = $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
        ]);

        $combinedData = $validated['email'] . '|' . $validated['name'];
        $encryptedData = Crypt::encryptString($combinedData);
        $inviteLink = 'https://logistic2.gwamerchandise.com/register?data=' . urlencode($encryptedData);

        Log::debug('Generated invite link:', ['link' => $inviteLink]);

        try {
            Log::info('Attempting to send invitation email to:', ['email' => $validated['email']]);
            Mail::to($validated['email'])->send(new VendorInvitationMail($validated['name'], $inviteLink));

            Log::info('Invitation email sent successfully to:', ['email' => $validated['email']]);

            // Store activity log
            $this->storeActivity("Invited vendor: {$validated['name']} ({$validated['email']})");

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
        Log::info('Fetching vendor applications from API');
        $apiKey = env('LOGISTIC2_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json'
        ])->get('https://logistic2.gwamerchandise.com/api/vendors');

        Log::info('API Response Status: ' . $response->status());
        Log::info('API Response Body: ' . $response->body());

        if ($response->successful()) {
            Log::info('API request successful, returning vendor data');

            // Store activity log
            $this->storeActivity("Fetched vendor applications");

            return view('admin.users.vendor-application', ['vendors' => $response->json()]);
        } else {
            Log::error('Failed to fetch vendor data from API. Status: ' . $response->status());

            return view('admin.users.vendor-application', ['error' => 'Failed to fetch data']);
        }
    }

    public function updateVendorStatus($vendorId, $status, Request $request)
    {
        Log::info("Updating vendor status: Vendor ID {$vendorId}, New Status: {$status}");
        $apiKey = env('LOGISTIC2_API_KEY');
        $rejectReason = $request->input('reject_reason');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept' => 'application/json',
        ])->patch("https://logistic2.gwamerchandise.com/api/vendor/{$vendorId}", [
            'status' => $status,
        ]);

        if ($response->successful()) {
            Log::info("Vendor status updated successfully: Vendor ID {$vendorId}, Status: {$status}");

            // Store activity log
            $this->storeActivity("Updated vendor status: Vendor ID {$vendorId}, Status: {$status}");

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
                        Mail::to($vendorData['email'])->send(new VendorStatusMail($vendorData, $status, $rejectReason));
                        Log::info("Status email sent successfully to Vendor ID {$vendorId}, Email: " . $vendorData['email']);

                        // Store activity log
                        $this->storeActivity("Sent status email to vendor: Vendor ID {$vendorId}, Status: {$status}, Email: " . $vendorData['email']);
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
