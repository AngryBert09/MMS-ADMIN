<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class BudgetReportController extends Controller
{
    public function index(Request $request)
    {
        // Define API URL with parameters
        $apiUrl = "https://finance.gwamerchandise.com/api/get-budget-report";
        $startDate = $request->query('start_date', '2025-02-20'); // Default start date
        $endDate = $request->query('end_date', '2025-02-27'); // Default end date

        // Make API request
        $response = Http::get($apiUrl, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        // Check if request was successful
        if ($response->successful()) {
            $data = $response->json(); // Decode JSON response

            // Pass data to the view
            return view('admin.reports.budget-report', ['budgets' => $data['budgets'], 'summary' => $data['summary_report']]);
        }

        // If request fails, return an error response
        return back()->with('error', 'Failed to retrieve budget data.');
    }

    public function analyzeBudgets()
    {
        // Fetch budget report from external API
        $apiUrl = "https://finance.gwamerchandise.com/api/get-budget-report";
        $startDate = '2025-02-20';
        $endDate = '2025-02-27';

        $response = Http::get($apiUrl, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        if (!$response->successful()) {
            return response()->json(['analysis' => 'Failed to fetch budget report.'], 500);
        }

        $data = $response->json();
        $budgets = $data['budgets'] ?? [];
        $summary = $data['summary_report'] ?? [];

        // Summarize budget data
        $budgetSummary = [
            'totalBudgetRequests' => $summary['total_requests'] ?? 0,
            'totalBudgetAmount' => $summary['total_budget_amount'] ?? 0,
            'approvedRequests' => count(array_filter($budgets, fn($b) => $b['status'] == 'approved')),
            'declinedRequests' => count(array_filter($budgets, fn($b) => $b['status'] == 'declined')),
            'departmentsInvolved' => array_unique(array_column($budgets, 'department')),
        ];

        // Prepare AI Prompt
        $prompt = [
            "Provide a detailed financial analysis of the budget report from {$startDate} to {$endDate}. Offer an insightful review of the total budget requests, allocated funds, and the proportion of approvals versus declines. Discuss trends in departmental budget allocations, identifying any inconsistencies or patterns. Examine key factors influencing approval or rejection rates, including financial constraints, departmental priorities, or spending justifications. Analyze budget utilization efficiency, highlighting any significant discrepancies or anomalies in spending behaviors. Conclude with a comprehensive summary of findings and practical recommendations for optimizing financial resource management. Do not use any section headers, bullet points, asterisks, or special formatting. Use the following budget data for analysis: " . json_encode($budgetSummary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ];

        // Call Gemini AI API
        $geminiApiKey = env('GEMINI_API_KEY');
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
    }


    public function analyzeBudgetsWithPrompt(Request $request)
    {
        $promptInput = $request->input('prompt');
        $existingReport = $request->input('existing_report', '');

        if (!$promptInput) {
            return response()->json(['report' => 'Prompt is required.'], 400);
        }

        // Optional: you can also re-fetch the same budget data as in the standard analysis
        $apiUrl = "https://finance.gwamerchandise.com/api/get-budget-report";
        $startDate = '2025-02-20';
        $endDate = '2025-02-27';

        $response = Http::get($apiUrl, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);

        if (!$response->successful()) {
            return response()->json(['report' => 'Failed to fetch budget data.'], 500);
        }

        $data = $response->json();
        $budgets = $data['budgets'] ?? [];
        $summary = $data['summary_report'] ?? [];

        $budgetSummary = [
            'totalBudgetRequests' => $summary['total_requests'] ?? 0,
            'totalBudgetAmount' => $summary['total_budget_amount'] ?? 0,
            'approvedRequests' => count(array_filter($budgets, fn($b) => $b['status'] == 'approved')),
            'declinedRequests' => count(array_filter($budgets, fn($b) => $b['status'] == 'declined')),
            'departmentsInvolved' => array_unique(array_column($budgets, 'department')),
        ];

        // Construct final prompt
        $finalPrompt = "You are a financial analysis assistant. Based on the following user prompt, generate a professional, detailed budget report:\n\n Do not use any section headers, bullet points, asterisks, or special formatting."
            . "User Prompt: {$promptInput}\n\n"
            . "Available Budget Data:\n" . json_encode($budgetSummary, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . "\n\n" . ($existingReport ? "Existing Summary:\n{$existingReport}" : '');

        $geminiApiKey = env('GEMINI_API_KEY');
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent", [
                'query' => ['key' => $geminiApiKey],
                'json' => [
                    'contents' => [['parts' => [['text' => $finalPrompt]]]]
                ]
            ]);

            $aiResponse = json_decode($response->getBody(), true);
            $analysisText = $aiResponse['candidates'][0]['content']['parts'][0]['text'] ?? 'No report generated.';

            return response()->json(['report' => nl2br($analysisText)]);
        } catch (\Exception $e) {
            return response()->json(['report' => 'AI analysis failed: ' . $e->getMessage()], 500);
        }
    }
}
