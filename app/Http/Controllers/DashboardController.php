<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use App\Models\User;

class DashboardController extends Controller
{
    public function getDashboard()
    {
        return view('dashboard');
    }


    public function getSalesData()
    {
        try {
            // Fetch data grouped by month
            $salesData = DB::table('sales')
                ->selectRaw('MONTH(order_date) as month,
                         SUM(CASE WHEN status = "Completed" THEN total_amount ELSE 0 END) as Completed,
                         SUM(CASE WHEN status = "Pending" THEN total_amount ELSE 0 END) as Pending')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Debugging: Log raw database response
            Log::info('Raw Sales Data:', ['data' => $salesData]);

            // Format data for chart
            $categories = [];
            $receivedData = [];
            $pendingData = [];

            $months = [
                1 => "Jan",
                2 => "Feb",
                3 => "Mar",
                4 => "Apr",
                5 => "May",
                6 => "Jun",
                7 => "Jul",
                8 => "Aug",
                9 => "Sep",
                10 => "Oct",
                11 => "Nov",
                12 => "Dec"
            ];

            foreach ($salesData as $data) {
                $monthName = $months[$data->month] ?? 'Unknown';
                $categories[] = $monthName;
                $receivedData[] = $data->Completed;
                $pendingData[] = $data->Pending;

                // Debugging: Log each formatted entry
                Log::info("Processing Month: $monthName", [
                    'Completed' => $data->Completed,
                    'Pending' => $data->Pending
                ]);
            }

            // Debugging: Log final formatted data
            Log::info('Final Sales Chart Data:', [
                'categories' => $categories,
                'received' => $receivedData,
                'pending' => $pendingData
            ]);

            return response()->json([
                'categories' => $categories,
                'received' => $receivedData,
                'pending' => $pendingData
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error fetching sales data:', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Failed to fetch sales data'
            ], 500);
        }
    }



    public function getInvoiceCount()
    {
        $apiUrl = "https://logistic2.gwamerchandise.com/api/invoices";
        $apiKey = env('LOGISTIC2_API_KEY');

        Log::info('Fetching invoice count from API: ' . $apiUrl);

        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
            'Accept' => 'application/json',
        ])->get($apiUrl);

        Log::info('API Response Status: ' . $response->status());

        if ($response->successful()) {
            Log::info('API Response Data: ' . json_encode($response->json()));

            if (isset($response['data'])) {
                $invoiceCount = count($response['data']); // Count invoices
                Log::info('Total Invoices: ' . $invoiceCount);
                return response()->json(['invoiceCount' => $invoiceCount]);
            }

            Log::warning('API Response does not contain "data" key.');
            return response()->json(['invoiceCount' => 0, 'error' => 'Missing data in API response']);
        }

        Log::error('Failed to fetch invoices. Response: ' . $response->body());

        return response()->json([
            'invoiceCount' => 0,
            'error' => 'Failed to fetch invoices',
            'response_body' => $response->body(),
        ]);
    }


    public function getInvoiceAnalytics(Request $request)
    {
        $period = $request->query('period', 'monthly'); // Default to 'monthly' if not provided
        $cacheKey = "invoice_analytics_{$period}";
        $cacheTime = 60; // Cache for 60 minutes

        return Cache::remember($cacheKey, $cacheTime, function () use ($period) {
            $apiUrl = "https://logistic2.gwamerchandise.com/api/invoices";
            $apiKey = env('LOGISTIC2_API_KEY');

            Log::info("Fetching invoice analytics for period: $period from API: $apiUrl");

            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Accept' => 'application/json',
            ])->get($apiUrl);

            Log::info('API Response Status: ' . $response->status());

            if (!$response->successful()) {
                Log::error('Failed to fetch invoices. Response: ' . $response->body());

                return response()->json([
                    'error' => 'Failed to fetch invoices',
                    'response_body' => $response->body(),
                ], 500);
            }

            $invoices = $response->json()['data'] ?? [];

            if (empty($invoices)) {
                Log::warning('API Response contains no invoice data.');
                return response()->json(['error' => 'No invoice data found'], 404);
            }

            // Process invoices based on selected period
            $groupedInvoices = collect($invoices)->groupBy(function ($invoice) use ($period) {
                $date = Carbon::parse($invoice['invoiceDate']); // Ensure order_date exists
                return match ($period) {
                    'weekly' => $date->startOfWeek()->format('Y-m-d'),
                    'monthly' => $date->format('Y-m'),
                    'yearly' => $date->format('Y'),
                    default => $date->format('Y-m'),
                };
            });

            $analyticsData = $groupedInvoices->map(function ($group) {
                return [
                    'totalInvoices' => $group->count(),
                    'totalAmount' => $group->sum('totalAmount'),
                    'paidInvoices' => $group->where('status', 'paid')->count(),
                    'unpaidInvoices' => $group->where('status', 'unpaid')->count(),
                    'cancelledInvoices' => $group->where('status', 'cancelled')->count(),
                ];
            });

            Log::info("Invoice analytics generated for period: $period");

            return response()->json(['analytics' => $analyticsData]);
        });
    }


    public function fetchHrApplications()
    {
        Log::info('Fetching HR employee applications from API');

        $apiKey = env('HR1_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept'        => 'application/json'
        ])->get('https://hr1.gwamerchandise.com/api/employee');

        if ($response->successful()) {
            Log::info('HR API request successful, processing employee data');

            $employees = $response->json();
            $filteredEmployees = [];

            foreach ($employees as $employee) {
                if (!empty($employee['email']) && !User::where('email', $employee['email'])->exists()) {
                    $filteredEmployees[] = [
                        'name' => $employee['name'] ?? 'N/A',
                        'email' => $employee['email'],
                        'contact' => $employee['contact_number'] ?? 'N/A',
                        'department' => $employee['department'] ?? 'N/A',
                        'status' => $employee['status'] ?? 'Pending'
                    ];
                }
            }

            Log::info('Filtered employee count (without accounts): ' . count($filteredEmployees));
            return response()->json(['employees' => $filteredEmployees]);
        } else {
            Log::error('Failed to fetch HR employee data from API. Status: ' . $response->status());
            return response()->json(['error' => 'Failed to fetch HR employee data'], 500);
        }
    }

    public function getDocumentStats()
    {
        try {
            $documentCount = DB::table('documents')->count();

            // Get last week's document count
            $lastWeekCount = DB::table('documents')
                ->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])
                ->count();

            // Calculate percentage change
            $percentageChange = $lastWeekCount > 0
                ? (($documentCount - $lastWeekCount) / $lastWeekCount) * 100
                : 0;

            // Ensure a minimum progress bar value
            $progress = min(100, max(0, $documentCount));

            return response()->json([
                'count' => $documentCount,
                'percentageChange' => round($percentageChange, 2),
                'progress' => $progress
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching document stats:', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to fetch document stats'], 500);
        }
    }
}
