<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcurementController extends Controller
{

    public function index()
    {
        try {
            // API URL
            $apiUrl = "https://logistic1.gwamerchandise.com/api/procurements";

            // Fetch API Key from .env
            $apiKey = env('LOGISTIC1_API_KEY');

            if (!$apiKey) {
                return back()->with('error', 'Missing API Key');
            }

            // Send GET request with headers
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => "Bearer $apiKey",
            ])->get($apiUrl);

            // Check if request was successful
            if ($response->successful()) {
                $procurements = $response->json();
                return view('admin.approvals.procurement', compact('procurements'));
            } else {
                return back()->with('error', 'Failed to fetch procurements');
            }
        } catch (\Exception $e) {
            Log::error('Procurements Fetch Error: ' . $e->getMessage());
            return back()->with('error', 'Server Error. Please try again later.');
        }
    }
}
