<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class InvoiceController extends Controller
{
    public function getInvoices()
    {
        $apiUrl = "https://logistic2.gwamerchandise.com/api/invoices";
        $apiKey = env('LOGISTIC2_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Accept' => 'application/json',
        ])->get($apiUrl);

        if ($response->successful() && isset($response['data'])) {
            $invoices = $response['data']; // Extract 'data' array
            return view('admin.reports.invoices.index-invoice', compact('invoices'));
        }

        return view('admin.reports.invoices.invoices', [
            'invoices' => [],
            'error' => 'Failed to fetch invoices: ' . $response->body(),
        ]);
    }

    public function showInvoice($id)
    {
        $apiUrl = "https://logistic2.gwamerchandise.com/api/invoices/{$id}";
        $apiKey = env('LOGISTIC2_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Accept' => 'application/json',
        ])->get($apiUrl);

        if ($response->successful() && isset($response['data'])) {
            $invoice = $response['data']; // Extract invoice details
            return view('admin.reports.invoices.show-invoice', compact('invoice'));
        }

        return redirect()->route('admin.reports.invoices.show-invoice')->with('error', 'Failed to fetch invoice details: ' . $response->body());
    }


    public function analyzeInvoices()
    {
        $cacheKey = 'invoice_analysis';
        $cacheTime = 60; // Cache for 60 minutes

        return Cache::remember($cacheKey, $cacheTime, function () {
            // Fetch invoices from external API
            $apiUrl = "https://logistic2.gwamerchandise.com/api/invoices";
            $apiKey = env('LOGISTIC2_API_KEY');

            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Accept' => 'application/json',
            ])->get($apiUrl);

            if (!$response->successful()) {
                return response()->json(['analysis' => 'Failed to fetch invoices.'], 500);
            }

            $invoices = $response['data'];

            // Summarize invoice data
            $invoiceSummary = [
                'totalInvoices' => count($invoices),
                'totalAmount' => array_sum(array_column($invoices, 'totalAmount')),
                'paidInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] == 'paid')),
                'unpaidInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] == 'unpaid')),
                'cancelledInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] == 'cancelled')),
            ];

            // Prepare AI Prompt
            $prompt = [
                "Generate a detailed and professional budget report in a structured business format without using bullet points, asterisks, numbered lists, or markdown-like formatting. The report should provide a well-written analysis of the budget period, summarizing the total budget requests, allocated funds, and key observations. Highlight the proportion of approved and declined budgets, identifying any patterns or trends in departmental allocations. Discuss factors influencing budget approvals or rejections, such as financial constraints, spending justifications, and departmental priorities. Provide insights into budget utilization, financial efficiency, and any anomalies or notable trends in spending behaviors. Conclude with a summary of findings and recommendations for optimizing financial resource management. The response should flow naturally in clear, structured paragraphs without section titles or symbols, resembling a well-crafted business document, and should be written as a single paragraph. Use the following budget data for analysis: " . json_encode($budgetSummary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ];


            // Call Gemini AI API
            $geminiApiKey = env('GEMINI_API_KEY'); // Store API key in .env
            $client = new \GuzzleHttp\Client();

            try {
                $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                    'query' => ['key' => $geminiApiKey],
                    'json' => [
                        'contents' => [['parts' => [['text' => implode("\n", $prompt)]]]]
                    ]
                ]);

                $aiResponse = json_decode($response->getBody(), true);
                $analysisText = $aiResponse['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis available.';

                return response()->json(['analysis' => nl2br($analysisText)]);
            } catch (\Exception $e) {
                return response()->json(['analysis' => 'AI analysis failed: ' . $e->getMessage()], 500);
            }
        });
    }
}
