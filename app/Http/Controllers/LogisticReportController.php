<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\Cache;

class LogisticReportController extends Controller
{
    use ActivityLogger;

    public function index(Request $request)
    {
        Log::info('Fetching logistics reports');

        // Define API endpoint with date parameters
        $startDate = $request->input('start_date', '2024-01-01');
        $endDate = $request->input('end_date', '2024-02-28');
        $apiUrl = "https://finance.gwamerchandise.com/api/get-logistics-reports?start_date={$startDate}&end_date={$endDate}";

        try {
            // Send GET request
            $response = Http::get($apiUrl);

            Log::info('API Response Status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                //
                // dd($data);

                // Extract the "logistics invoices" from the response
                $logisticsInvoices = $data['logistics invoices'] ?? [];

                // Store activity log
                $this->storeActivity("Fetched logistics reports from {$startDate} to {$endDate}");

                return view('admin.reports.logistic-report', compact('logisticsInvoices'));
            } else {
                Log::error('Failed to fetch logistics reports. Status: ' . $response->status());

                return view('admin.reports.logistic-report', ['error' => 'Failed to fetch logistics reports']);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching logistics reports: ' . $e->getMessage());

            return view('admin.reports.logistic-report', ['error' => 'An error occurred while fetching reports.']);
        }
    }

    public function analyzeLogistics()
    {
        // Fetch logistics data from external API
        $apiUrl = "https://finance.gwamerchandise.com/api/get-logistics-reports?start_date=2024-01-01&end_date=2024-02-28";

        try {
            $response = Http::get($apiUrl);

            if (!$response->successful()) {
                return response()->json(['report' => 'Failed to fetch logistics data.'], 500);
            }

            $data = $response->json();
            $logisticsInvoices = $data['logistics invoices'] ?? [];

            if (empty($logisticsInvoices)) {
                return response()->json(['report' => 'No logistics data available.'], 404);
            }

            // Summarize logistics data
            $totalShipments = count($logisticsInvoices);
            $totalLogisticsCost = array_sum(array_column(array_column($logisticsInvoices, 'invoice'), 'total_amount'));
            $onTimeDeliveries = 0;
            $delayedDeliveries = 0;
            $vendorPerformance = [];

            foreach ($logisticsInvoices as $logistics) {
                $vendorId = $logistics['invoice']['vendor_id'] ?? 'Unknown Vendor';
                $deliveryDate = $logistics['purchase_order']['delivery_date'] ?? null;
                $shipmentDate = $logistics['purchase_order']['shipment_date'] ?? null;

                if ($deliveryDate && $shipmentDate) {
                    $deliveryTimestamp = strtotime($deliveryDate);
                    $shipmentTimestamp = strtotime($shipmentDate);
                    if ($deliveryTimestamp >= $shipmentTimestamp) {
                        $onTimeDeliveries++;
                    } else {
                        $delayedDeliveries++;
                    }
                }

                $vendorPerformance[$vendorId] = ($vendorPerformance[$vendorId] ?? 0) + 1;
            }

            arsort($vendorPerformance); // Rank vendors by number of shipments

            // Prepare AI Prompt
            $prompt = [
                "Generate a professional logistics performance report analyzing shipment trends, vendor efficiency, and delivery reliability for the given period. The report should be structured as a business document, avoiding bullet points, asterisks, numbered lists, or markdown-like formatting. Summarize total shipments processed, overall logistics costs, and key performance indicators. Analyze vendor efficiency, highlighting which vendors contributed most to shipments and evaluating their performance in terms of timely deliveries. Discuss the proportion of on-time vs. delayed deliveries and potential factors affecting these trends, such as supply chain disruptions, transportation issues, or order processing inefficiencies. Provide insights into logistics performance, trends in vendor reliability, and any notable patterns in shipment efficiency. Conclude with a summary of findings and recommendations for optimizing logistics operations and enhancing supply chain performance. The response should flow naturally in structured paragraphs without section titles or symbols, resembling a well-crafted business document. Use the following logistics data for analysis: " . json_encode([
                    'totalShipments' => $totalShipments,
                    'totalLogisticsCost' => $totalLogisticsCost,
                    'onTimeDeliveries' => $onTimeDeliveries,
                    'delayedDeliveries' => $delayedDeliveries,
                    'vendorPerformance' => array_slice($vendorPerformance, 0, 5, true)
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ];

            // Call Gemini AI API
            $geminiApiKey = env('GEMINI_API_KEY');
            if (!$geminiApiKey) {
                return response()->json(['report' => 'Gemini API key is missing.'], 500);
            }

            $client = new \GuzzleHttp\Client();
            $aiResponse = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                'query' => ['key' => $geminiApiKey],
                'json' => [
                    'contents' => [['parts' => [['text' => implode("\n", $prompt)]]]]
                ]
            ]);

            $aiData = json_decode($aiResponse->getBody(), true);
            $analysisText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis available.';

            return response()->json(['report' => nl2br($analysisText)]);
        } catch (\Exception $e) {
            return response()->json(['report' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function analyzeCustomLogistics(Request $request)
    {
        // Validate custom prompt
        $request->validate([
            'custom_prompt' => 'required|string|max:5000',
        ]);

        // Fetch logistics data from external API
        $apiUrl = "https://finance.gwamerchandise.com/api/get-logistics-reports?start_date=2024-01-01&end_date=2024-02-28";

        try {
            $response = Http::get($apiUrl);

            if (!$response->successful()) {
                return response()->json(['report' => 'Failed to fetch logistics data.'], 500);
            }

            $data = $response->json();
            $logisticsInvoices = $data['logistics invoices'] ?? [];

            if (empty($logisticsInvoices)) {
                return response()->json(['report' => 'No logistics data available.'], 404);
            }

            // Summarize logistics data
            $totalShipments = count($logisticsInvoices);
            $totalLogisticsCost = array_sum(array_column(array_column($logisticsInvoices, 'invoice'), 'total_amount'));
            $onTimeDeliveries = 0;
            $delayedDeliveries = 0;
            $vendorPerformance = [];

            foreach ($logisticsInvoices as $logistics) {
                $vendorId = $logistics['invoice']['vendor_id'] ?? 'Unknown Vendor';
                $deliveryDate = $logistics['purchase_order']['delivery_date'] ?? null;
                $shipmentDate = $logistics['purchase_order']['shipment_date'] ?? null;

                if ($deliveryDate && $shipmentDate) {
                    $deliveryTimestamp = strtotime($deliveryDate);
                    $shipmentTimestamp = strtotime($shipmentDate);
                    if ($deliveryTimestamp >= $shipmentTimestamp) {
                        $onTimeDeliveries++;
                    } else {
                        $delayedDeliveries++;
                    }
                }

                $vendorPerformance[$vendorId] = ($vendorPerformance[$vendorId] ?? 0) + 1;
            }

            arsort($vendorPerformance); // Rank vendors by number of shipments

            // Prepare AI Prompt
            $prompt = [
                "Generate a professional logistics performance report analyzing shipment trends, vendor efficiency, and delivery reliability for the given period. The report should be structured as a business document, avoiding bullet points, asterisks, numbered lists, or markdown-like formatting. Summarize total shipments processed, overall logistics costs, and key performance indicators. Analyze vendor efficiency, highlighting which vendors contributed most to shipments and evaluating their performance in terms of timely deliveries. Discuss the proportion of on-time vs. delayed deliveries and potential factors affecting these trends, such as supply chain disruptions, transportation issues, or order processing inefficiencies. Provide insights into logistics performance, trends in vendor reliability, and any notable patterns in shipment efficiency. Conclude with a summary of findings and recommendations for optimizing logistics operations and enhancing supply chain performance. The response should flow naturally in structured paragraphs without section titles or symbols, resembling a well-crafted business document. Use the following logistics data for analysis: " . json_encode([
                    'totalShipments' => $totalShipments,
                    'totalLogisticsCost' => $totalLogisticsCost,
                    'onTimeDeliveries' => $onTimeDeliveries,
                    'delayedDeliveries' => $delayedDeliveries,
                    'vendorPerformance' => array_slice($vendorPerformance, 0, 5, true)
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ];

            // Add the custom prompt from the request if provided
            $customPrompt = $request->input('custom_prompt');
            if ($customPrompt) {
                $prompt[] = $customPrompt; // Append custom prompt
            }

            // Call Gemini AI API
            $geminiApiKey = env('GEMINI_API_KEY');
            if (!$geminiApiKey) {
                return response()->json(['report' => 'Gemini API key is missing.'], 500);
            }

            $client = new \GuzzleHttp\Client();
            $aiResponse = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                'query' => ['key' => $geminiApiKey],
                'json' => [
                    'contents' => [['parts' => [['text' => implode("\n", $prompt)]]]]
                ]
            ]);

            $aiData = json_decode($aiResponse->getBody(), true);
            $analysisText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis available.';

            return response()->json(['report' => nl2br($analysisText)]);
        } catch (\Exception $e) {
            return response()->json(['report' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
