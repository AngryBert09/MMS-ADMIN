<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiSalesReportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
        $cacheKey = 'sales_analysis';
        $cacheTime = 60; // Cache for 60 minutes

        return Cache::remember($cacheKey, $cacheTime, function () {
            // Fetch sales data from external API
            $apiUrl = "https://finance.gwamerchandise.com/api/sales-reports";

            try {
                $response = Http::get($apiUrl);

                if (!$response->successful()) {
                    return response()->json(['report' => 'Failed to fetch sales data.'], 500);
                }

                $data = $response->json();
                $sales = $data['sales invoices'] ?? []; // Extract sales invoices

                if (empty($sales)) {
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

                arsort($topProducts); // Sort by most sold items
                $topSellingProducts = array_slice($topProducts, 0, 5, true);

                // Prepare AI Prompt
                $prompt = [
                    "Generate a detailed and professional sales report in a structured business format without using bullet points, asterisks, numbered lists, or markdown-like formatting. The report should provide a well-written analysis of the sales period, summarizing total sales transactions, overall revenue, and key observations. Highlight the proportion of earnings relative to total revenue, identifying any patterns or trends in customer purchases. Discuss factors influencing sales trends, such as seasonal demand, pricing strategies, marketing efforts, and product popularity. Provide insights into sales performance, customer purchasing behaviors, and any anomalies or notable trends in revenue generation. Conclude with a summary of findings and recommendations for optimizing sales performance and revenue growth. The response should flow naturally in clear, structured paragraphs without section titles or symbols, resembling a well-crafted business document, and should be written as a single paragraph. Use the following sales data for analysis: " . json_encode([
                        'totalSales' => $totalSales,
                        'totalRevenue' => $totalRevenue,
                        'totalEarnings' => $totalEarnings,
                        'topSellingProducts' => $topSellingProducts
                    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                ];

                // Call Gemini AI API
                $geminiApiKey = env('GEMINI_API_KEY');
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
        });
    }
}
