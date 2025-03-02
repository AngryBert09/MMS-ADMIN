<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GeminiSalesReportService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GEMINI_API_KEY');
    }

    public function generateSalesReport($startDate = null, $endDate = null)
    {
        // Generate a unique cache key based on the date range
        $cacheKey = 'sales_report_' . ($startDate ?? 'all') . '_' . ($endDate ?? 'all');

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($startDate, $endDate) {
            // Fetch sales data (all records if no date range is provided)
            $query = DB::table('sales')->select('order_date', 'total_amount', 'status', 'payment_method', 'customer_id');

            if ($startDate && $endDate) {
                $query->whereBetween('order_date', [$startDate, $endDate]);
            }

            $salesData = $query->get()->toArray();

            if (empty($salesData)) {
                return "No sales data found.";
            }

            // Prepare AI Prompt
            $prompt = [
                "Generate a detailed and professional sales report in a structured business format without using bullet points, asterisks, numbered lists, or markdown-like formatting. The report should provide a well-written analysis of the reporting period, summarizing total revenue, transaction count, and key observations. Highlight the most utilized payment methods, peak sales periods, and customer spending patterns, identifying notable trends that influenced revenue. Offer insights into potential factors affecting sales performance and conclude with a summary of findings and recommendations for future improvement. The response should flow naturally in clear, structured paragraphs without section titles or symbols, resembling a well-crafted business document, Also make it as one paragraph. Use the following sales data for analysis: " . json_encode($salesData, JSON_PRETTY_PRINT)
            ];

            try {
                // Send Request to Gemini API
                $response = $this->client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                    'query' => ['key' => $this->apiKey],
                    'json' => [
                        'contents' => [['parts' => [['text' => implode("\n", $prompt)]]]]
                    ]
                ]);

                $responseBody = json_decode($response->getBody(), true);

                // Extract AI-generated text response
                return $responseBody['candidates'][0]['content']['parts'][0]['text'] ?? "Failed to generate report.";
            } catch (\Exception $e) {
                return "Error fetching AI report: " . $e->getMessage();
            }
        });
    }
}
