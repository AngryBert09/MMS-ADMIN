<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiSalesReportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SalesReportController extends Controller
{

    public function index()
    {
        $apiUrl = "https://finance.gwamerchandise.com/api/sales-reports";

        $response = Http::get($apiUrl);

        if (!$response->successful()) {
            return back()->with('error', 'Failed to fetch sales reports.');
        }

        $data = $response->json();

        // Extract sales invoices from the API response
        $sales = $data['sales invoices'] ?? [];

        return view('admin.reports.sales-report', compact('sales'));
    }

    public function analyzeSales()
    {
        Log::debug('Starting analyzeSales() function');

        // Fetch sales data from external API
        $apiUrl = "https://finance.gwamerchandise.com/api/sales-reports";
        Log::debug('Preparing to fetch sales data', ['api_url' => $apiUrl]);

        try {
            $startTime = microtime(true);
            $response = Http::get($apiUrl);
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            Log::debug('API response received', [
                'status' => $response->status(),
                'duration_ms' => $duration
            ]);

            if (!$response->successful()) {
                Log::error('Failed to fetch sales data', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['report' => 'Failed to fetch sales data.'], 500);
            }

            $data = $response->json();
            Log::debug('Sales data parsed', [
                'sales_count' => count($data['sales invoices'] ?? []),
                'data_sample' => array_slice($data['sales invoices'] ?? [], 0, 2)
            ]);

            $sales = $data['sales invoices'] ?? [];
            if (empty($sales)) {
                Log::warning('Empty sales data received');
                return response()->json(['report' => 'No sales data available.'], 500);
            }

            // Summarize sales data
            $totalSales = count($sales);
            $totalRevenue = array_sum(array_column($sales, 'total_sum'));
            $totalEarnings = array_sum(array_column($sales, 'earnings'));
            $topProducts = [];

            foreach ($sales as $sale) {
                foreach ($sale['cart_items'] as $item) {
                    $productName = $item['product_name'];
                    $topProducts[$productName] = ($topProducts[$productName] ?? 0) + 1;
                }
            }

            arsort($topProducts);
            $topSellingProducts = array_slice($topProducts, 0, 5, true);

            Log::debug('Sales data summarized', [
                'total_sales' => $totalSales,
                'total_revenue' => $totalRevenue,
                'total_earnings' => $totalEarnings,
                'top_products' => $topSellingProducts
            ]);

            // Prepare AI Prompt
            $prompt = [
                "Generate a professional sales analysis as a single, continuous narrative paragraph. " .
                    "Do not use any section headers, bullet points, asterisks, or special formatting" .
                    "Strictly, use peso sign only for currency" .
                    "Present all insights in flowing business prose that naturally incorporates: " .
                    "1) Key performance metrics " .
                    "2) Product performance analysis " .
                    "3) Revenue and profitability trends " .
                    "4) Actionable business implications " .
                    "Write in complete sentences that flow logically from one point to the next. " .
                    "Here is the sales data to analyze: " .
                    json_encode([
                        'totalSales' => $totalSales,
                        'totalRevenue' => $totalRevenue,
                        'totalEarnings' => $totalEarnings,
                        'topSellingProducts' => $topSellingProducts
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ];

            Log::debug('Preparing Gemini AI request', [
                'prompt_length' => strlen(implode("\n", $prompt)),
                'prompt_sample' => substr(implode("\n", $prompt), 0, 100) . '...'
            ]);

            // Call Gemini AI API
            $geminiApiKey = env('GEMINI_API_KEY');
            $client = new \GuzzleHttp\Client();

            $startTime = microtime(true);
            $aiResponse = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                'query' => ['key' => $geminiApiKey],
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => implode("\n", $prompt)]
                            ]
                        ]
                    ]
                ]
            ]);
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $aiData = json_decode($aiResponse->getBody(), true);
            Log::debug('Gemini AI response', [
                'duration_ms' => $duration,
                'response_sample' => substr($aiData['candidates'][0]['content']['parts'][0]['text'] ?? '', 0, 100) . '...'
            ]);

            $analysisText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis available.';

            Log::info('Successfully generated sales report', [
                'report_length' => strlen($analysisText)
            ]);

            return response()->json(['report' => nl2br($analysisText)]);
        } catch (\Exception $e) {
            Log::error('Error in analyzeSales()', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['report' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function analyzeSalesWithPrompt(Request $request)
    {
        Log::debug('Starting analyzeSalesWithPrompt()', [
            'request_data' => $request->all()
        ]);

        try {
            $validated = $request->validate([
                'prompt' => 'required|string',
                'existing_report' => 'nullable|string'
            ]);

            Log::debug('Request validated', [
                'prompt_length' => strlen($validated['prompt']),
                'existing_report_length' => strlen($validated['existing_report'] ?? '')
            ]);

            // Prepare AI Prompt
            $prompt = [
                "As a sales analysis assistant, analyze the sales data and provide a professional response to the user's request. " .
                    "Do not use any section headers, bullet points, asterisks, or special formatting" .
                    "Strictly, use Peso sign only for currency" .
                    "Focus on delivering clear, actionable insights in well-structured paragraphs without bullet points or special characters. " .
                    "User request: " . $validated['prompt'] . ". " .
                    "Sales report context: " . ($validated['existing_report'] ?? 'No additional context provided') . ". " .
                    "Provide your analysis in continuous prose with proper business formatting."
            ];

            Log::debug('Preparing Gemini AI request with prompt', [
                'prompt_length' => strlen(implode("\n", $prompt)),
                'prompt_sample' => substr(implode("\n", $prompt), 0, 100) . '...'
            ]);

            // Call Gemini AI API
            $geminiApiKey = env('GEMINI_API_KEY');
            $client = new \GuzzleHttp\Client();

            $startTime = microtime(true);
            $aiResponse = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                'query' => ['key' => $geminiApiKey],
                'json' => [
                    'contents' => [['parts' => [['text' => implode("\n", $prompt)]]]]
                ]
            ]);
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            $aiData = json_decode($aiResponse->getBody(), true);
            Log::debug('Gemini AI response', [
                'duration_ms' => $duration,
                'response_sample' => substr($aiData['candidates'][0]['content']['parts'][0]['text'] ?? '', 0, 100) . '...'
            ]);

            $analysisText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis available.';

            Log::info('Successfully generated prompt-based analysis', [
                'response_length' => strlen($analysisText),
                'prompt' => $validated['prompt']
            ]);

            return response()->json(['report' => nl2br($analysisText)]);
        } catch (\Exception $e) {
            Log::error('Error in analyzeSalesWithPrompt()', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['report' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
