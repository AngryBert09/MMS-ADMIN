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


    public function analyzeInvoices(Request $request)
    {
        // Validate custom prompt (optional)
        $request->validate([
            'custom_prompt' => 'nullable|string|max:5000',
        ]);

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
            'paidInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] === 'paid')),
            'unpaidInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] === 'unpaid')),
            'cancelledInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] === 'cancelled')),
        ];

        // Base AI Prompt
        $prompt = [
            "Generate a detailed and professional invoice report in structured business prose. Do not use bullets, lists, or formatting symbols. The report should analyze invoice trends and summarize the number of invoices processed, total invoice amount, and the breakdown between paid, unpaid, and cancelled statuses. Discuss possible reasons for unpaid or cancelled invoices such as cash flow issues, vendor delays, or system discrepancies. Offer insights into invoice processing efficiency and payment reliability. Finish with a summary of findings and strategic suggestions for improving invoice management. Use the following invoice data, use peso sign only: " . json_encode($invoiceSummary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ];

        // Add custom prompt if provided
        if ($request->filled('custom_prompt')) {
            $prompt[] = $request->input('custom_prompt');
        }

        // Call Gemini AI API
        $geminiApiKey = env('GEMINI_API_KEY');
        $client = new \GuzzleHttp\Client();

        try {
            $aiResponse = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                'query' => ['key' => $geminiApiKey],
                'json' => [
                    'contents' => [['parts' => [['text' => implode("\n", $prompt)]]]]
                ]
            ]);

            $aiData = json_decode($aiResponse->getBody(), true);
            $analysisText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis available.';

            return response()->json(['analysis' => nl2br($analysisText)]);
        } catch (\Exception $e) {
            return response()->json(['analysis' => 'AI analysis failed: ' . $e->getMessage()], 500);
        }
    }

    public function analyzeInvoicesWithPrompt(Request $request)
    {
        $request->validate([
            'custom_prompt' => 'required|string|max:5000',
        ]);

        // Fetch invoices
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

        $invoiceSummary = [
            'totalInvoices' => count($invoices),
            'totalAmount' => array_sum(array_column($invoices, 'totalAmount')),
            'paidInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] === 'paid')),
            'unpaidInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] === 'unpaid')),
            'cancelledInvoices' => count(array_filter($invoices, fn($inv) => $inv['status'] === 'cancelled')),
        ];

        $prompt = "Generate a professional and concise invoice analysis report using only the provided data values. Do not use section headers, use peso sign, bullet points, asterisks, or special formatting. The report must be written in a single structured paragraph, suitable for a business audience. Use formal language and ensure that all numbers and metrics used in the analysis exactly match the data provided. Avoid generalizations or assumptions. Focus on financial performance, payment trends, and any insights into unpaid or cancelled invoices. Maintain clear and accurate reporting. Strictly, use peso sign only\n\n"
            . $request->input('custom_prompt')
            . "\n\nInvoice data: "
            . json_encode($invoiceSummary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


        // Call Gemini
        $geminiApiKey = env('GEMINI_API_KEY');
        $client = new \GuzzleHttp\Client();

        try {
            $aiResponse = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                'query' => ['key' => $geminiApiKey],
                'json' => [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]
            ]);

            $aiData = json_decode($aiResponse->getBody(), true);
            $analysisText = $aiData['candidates'][0]['content']['parts'][0]['text'] ?? 'No analysis available.';

            return response()->json(['analysis' => nl2br($analysisText)]);
        } catch (\Exception $e) {
            return response()->json(['analysis' => 'AI analysis failed: ' . $e->getMessage()], 500);
        }
    }
}
