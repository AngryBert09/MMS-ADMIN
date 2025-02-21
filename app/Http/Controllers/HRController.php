<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class HRController extends Controller
{

    public function getHrApplications()
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

            // Filter employees who do not have an account in the Users table
            foreach ($employees as $employee) {
                // Make sure the employee has an email to check
                if (isset($employee['email']) && !empty($employee['email'])) {
                    // If no user exists with this email, add the employee to the filtered list
                    if (!User::where('email', $employee['email'])->exists()) {
                        $filteredEmployees[] = $employee;
                    }
                }
            }

            Log::info('Filtered employee count (without accounts): ' . count($filteredEmployees));

            return view('admin.users.upcoming-users', ['employees' => $filteredEmployees]);
        } else {
            Log::error('Failed to fetch HR employee data from API. Status: ' . $response->status());
            return view('admin.users.upcoming-users', ['error' => 'Failed to fetch data']);
        }
    }




    public function createHrAccount(Request $request, $employeeId)
    {
        Log::info("Attempting to create HR account for employee ID: {$employeeId}");

        if (!$employeeId) {
            Log::error("Employee not found for ID: {$employeeId}");
            return redirect()->back()->with('error', 'Employee not found.');
        }

        // Retrieve HR API key from .env
        $apiKey = env('HR1_API_KEY');

        // Fetch employee details from HR API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Accept'        => 'application/json',
        ])->get("https://hr1.gwamerchandise.com/api/employee/{$employeeId}");

        Log::info("HR API response status: " . $response->status());
        Log::info("HR API response body: " . $response->body());

        if (!$response->successful()) {
            Log::error("Failed to fetch employee details for employee ID: {$employeeId}");
            return redirect()->back()->with('error', 'Failed to fetch employee details.');
        }

        $employee = $response->json();

        // Concatenate full name from first, middle, and last names
        $fullName = trim(
            ($employee['first_name'] ?? '') . ' ' .
                ($employee['middle_name'] ?? '') . ' ' .
                ($employee['last_name'] ?? '')
        );
        Log::debug("Concatenated full name: {$fullName}");

        try {
            $user = new User();
            $user->name = $fullName;
            $user->email = $employee['email'] ?? null; // Ensure an email is provided
            $user->phone_number = $employee['contact'] ?? null;
            $user->address = $employee['address'] ?? null;
            $user->role = 'HR';
            $user->password = Hash::make('#hrGWA'); // Default password

            Log::debug("User object before saving: " . json_encode($user->toArray()));

            $user->save();

            Log::info("HR account created successfully for employee ID: {$employeeId}, user ID: {$user->id}");
            return redirect()->back()->with('success', 'HR account created successfully.');
        } catch (\Exception $e) {
            Log::error("Error creating HR account for employee ID: {$employeeId}. Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create HR account.');
        }
    }


    /**
     * Dummy helper function to simulate fetching employee data by ID from HR API.
     * Replace this with your actual data retrieval logic.
     */
    private function getEmployeeById($employeeId)
    {
        $dummyEmployees = [
            1 => [
                'first_name' => 'John',
                'middle_name' => 'A.',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'contact' => '123-456-7890',
                'department' => 'HR',
                'status' => 'Pending'
            ],
            // Add additional dummy entries as needed
        ];

        return $dummyEmployees[$employeeId] ?? null;
    }
}
