<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;


class HRController extends Controller
{

    public function getHrApplications()
    {
        Log::info('Fetching HR employee applications from API');

        $cacheKey = 'hr_employee_applications_list';
        $cacheDuration = now()->addMinutes(30); // Cache for 30 minutes

        $filteredEmployees = Cache::remember($cacheKey, $cacheDuration, function () {
            return $this->fetchHrApplications();
        });

        return view('admin.users.upcoming-users', ['employees' => $filteredEmployees]);
    }

    public function createHrAccount(Request $request, $employeeId)
    {
        Log::info("Attempting to create HR account for employee ID: {$employeeId}");

        if (!$employeeId) {
            Log::error("Employee ID is missing.");
            return redirect()->back()->with('error', 'Employee ID is required.');
        }

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

        if (empty($employee['email'])) {
            Log::error("Email is missing for employee ID: {$employeeId}");
            return redirect()->back()->with('error', 'Employee email is required.');
        }

        $fullName = trim(
            ($employee['first_name'] ?? '') . ' ' .
                ($employee['middle_name'] ?? '') . ' ' .
                ($employee['last_name'] ?? '')
        );

        Log::debug("Concatenated full name: {$fullName}");

        $password = '#' . strtolower(str_replace(' ', '', ($employee['last_name'] ?? 'user'))) . 'GWA';

        Log::debug("Generated password: {$password}");

        try {
            // Check if user already exists
            $existingUser = User::where('email', $employee['email'])->first();
            if ($existingUser) {
                Log::warning("User with email {$employee['email']} already exists.");
                return redirect()->back()->with('warning', 'User already exists.');
            }

            $user = new User();
            $user->name = $fullName;
            $user->email = $employee['email'];
            $user->phone_number = $employee['contact'] ?? null;
            $user->address = $employee['address'] ?? null;
            $user->role = 'Employee';
            $user->password = Hash::make($password);

            Log::debug("User object before saving: " . json_encode($user->toArray()));

            $user->save();

            // Remove only the newly created user from the cache
            $this->removeUserFromCache($employee['email']);

            Log::info("HR account created successfully for employee ID: {$employeeId}, user ID: {$user->id}, email: {$employee['email']}");
            return redirect()->back()->with('success', 'HR account created successfully.');
        } catch (\Exception $e) {
            Log::error("Error creating HR account for employee ID: {$employeeId}. Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create HR account.');
        }
    }


    // Function to fetch HR employee applications
    private function fetchHrApplications()
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
                    $filteredEmployees[] = $employee;
                }
            }

            Log::info('Filtered employee count (without accounts): ' . count($filteredEmployees));
            return $filteredEmployees;
        } else {
            Log::error('Failed to fetch HR employee data from API. Status: ' . $response->status());
            return [];
        }
    }

    // Function to remove a user from the cached HR applications list
    private function removeUserFromCache($email)
    {
        Log::info("Removing user with email {$email} from HR applications cache");

        $cacheKey = 'hr_employee_applications_list';

        if (Cache::has($cacheKey)) {
            $cachedEmployees = Cache::get($cacheKey);

            // Remove the user from the cached list
            $updatedEmployees = array_filter($cachedEmployees, function ($employee) use ($email) {
                return $employee['email'] !== $email;
            });

            // Update the cache with the modified list
            Cache::put($cacheKey, array_values($updatedEmployees), now()->addMinutes(30));

            Log::info("User {$email} removed from cache successfully");
        } else {
            Log::warning("Cache key {$cacheKey} does not exist, skipping removal");
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
