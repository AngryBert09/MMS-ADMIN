<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiSalesReportService;
use Illuminate\Support\Facades\DB;

class SalesReportController extends Controller
{
    protected $salesReportService;

    public function __construct(GeminiSalesReportService $salesReportService)
    {
        $this->salesReportService = $salesReportService;
    }

    public function index()
    {
        $sales = DB::table('sales')->orderBy('order_date', 'desc')->get();
        return view('admin.reports.sales-report', compact('sales'));
    }


    public function generateReport()
    {
        try {
            // Get all sales data
            $sales = DB::table('sales')->get();

            // Simulate AI analysis (replace with actual AI logic)
            $aiAnalysis = $this->salesReportService->generateSalesReport();

            return response()->json([
                'success' => true,
                'sales_data' => $sales,
                'report' => $aiAnalysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
