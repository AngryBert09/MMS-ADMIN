<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiUserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::all()->map(function ($user) {
            return [
                'id'              => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'emailVerifiedAt' => $user->email_verified_at,
                'role'           => $user->role,
                'status'        => $user->status,
                'phoneNumber'    => $user->phone_number,
                'address'       => $user->address,
                'profilePic'     => $user->profile_pic,
                'coverPic'       => $user->cover_pic,
                'createdAt'      => $user->created_at,
                'updatedAt'      => $user->updated_at,
            ];
        });

        return response()->json(['users' => $users], 200);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        Log::info('Received store request:', $request->except('password'));

        // Custom Validation Messages
        $messages = [
            'name.required'  => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email'    => 'Please enter a valid email address.',
            'email.unique'   => 'The email is already taken. Try a different one.',
            'role.required'  => 'Role is required.',
            'role.in'        => 'Invalid role selected.',
            'status.required' => 'Status is required.',
            'status.in'      => 'Invalid status selected.',
        ];

        // Validate Input
        try {
            $request->validate([
                'name'   => 'required|string|max:255',
                'email'  => 'required|email|unique:users,email',
                'role'   => 'required|in:admin,vendor,user,hr',
                'status' => 'required|in:active,inactive',
            ], $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Failed:', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }

        DB::beginTransaction(); // Start database transaction

        try {
            // Automatically generate a password using the role
            $password = '#' . strtolower($request->role) . 'GWA';
            Log::info('Generated role-based password:', ['password' => $password]);

            // Hash the password
            $hashedPassword = Hash::make($password);
            Log::info('Password hashed successfully.');

            // Create user
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $hashedPassword,
                'role'     => $request->role,
                'status'   => $request->status,
            ]);

            if (!$user) {
                Log::error('User creation failed.');
                DB::rollBack();
                return response()->json(['error' => 'Failed to create user.'], 500);
            }

            DB::commit(); // Commit transaction

            Log::info('User created successfully:', [
                'userId' => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'status' => $user->status,
            ]);

            return response()->json(['message' => 'User created successfully.', 'user' => $user], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user:', [
                'message' => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Something went wrong. Check logs.'], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $formattedUser = [
            'id'              => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'emailVerifiedAt' => $user->email_verified_at,
            'role'           => $user->role,
            'status'        => $user->status,
            'phoneNumber'    => $user->phone_number,
            'address'       => $user->address,
            'profilePic'     => $user->profile_pic,
            'coverPic'       => $user->cover_pic,
            'createdAt'      => $user->created_at,
            'updatedAt'      => $user->updated_at,
        ];

        return response()->json(['user' => $formattedUser], 200);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Log request data
        Log::info('User update request', $request->all());

        // Validate incoming data
        try {
            $validated = $request->validate([
                'name'     => 'nullable|string|max:255',
                'email'    => 'nullable|email|unique:users,email,' . $user->id,
                'role'     => 'nullable|string',
                'status'   => 'nullable|in:active,inactive',
                'password' => 'nullable|min:6|confirmed',
            ]);
            Log::info('Validation passed', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json(['errors' => $e->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Update only the provided fields except password
            $user->update(collect($validated)->except('password')->toArray());

            // Update password only if provided
            if ($request->filled('password')) {
                Log::debug('Password field is present, updating password');
                $user->update(['password' => Hash::make($request->password)]);
            }

            DB::commit();

            Log::info('User updated successfully', ['userId' => $user->id]);

            // Store activity log
            $this->storeActivity("You updated this user (ID: " . $user->id . ")");

            return response()->json(['message' => 'User updated successfully.', 'user' => $user], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to update user.'], 500);
        }
    }

    /**
     * Authenticate a user and log them into the system.
     */
    public function authenticate(Request $request)
    {
        // Validate the credentials
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate
        if (!Auth::attempt($credentials)) {
            Log::error('Authentication failed for email:', ['email' => $request->email]);
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }

        $user = Auth::user();

        // Check if the account is active
        if ($user->status !== 'Active') {
            Log::warning('Authentication failed - Inactive account', [
                'email'  => $request->email,
                'userId' => $user->id,
                'status' => $user->status,
            ]);
            return response()->json(['error' => 'Account is inactive. Please contact support.'], 403);
        }

        Log::info('User authenticated successfully.', ['userId' => $user->id]);

        return response()->json(['message' => 'Authentication successful.', 'user' => $user], 200);
    }

    /**
     * Change the user's password.
     */
    public function changePassword(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'userId'          => 'required|exists:users,id',
            'currentPassword' => 'required',
            'newPassword'     => 'required|string|min:6|confirmed', // newPasswordConfirmation must be provided
        ]);

        // Fetch the user by ID
        $user = User::findOrFail($validated['userId']);

        // Verify if the authenticated user is changing their own password
        if (Auth::id() !== $user->id) {
            Log::warning("Unauthorized password change attempt for userId: {$user->id} by userId: " . Auth::id());
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Verify current password
        if (!Hash::check($validated['currentPassword'], $user->password)) {
            Log::error("Password change failed: Incorrect current password for userId: {$user->id}");
            return response()->json(['error' => 'Current password does not match.'], 400);
        }

        // Update password
        $user->password = Hash::make($validated['newPassword']);
        $user->save();

        // Log the activity
        $this->storeActivity("User (ID: {$user->id}) changed their password.");

        Log::info("Password updated successfully for userId: {$user->id}");
        return response()->json(['message' => 'Password updated successfully.'], 200);
    }


    /**
     * Helper method to store an activity log.
     *
     * @param string $description
     * @return bool
     */
    public function storeActivity($description, $id)
    {
        try {
            DB::table('activities')->insert([
                'user_id'      => $id, // ID of the user performing the action
                'description' => $description,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            Log::info("Activity stored: {$description}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to store activity: " . $e->getMessage());
            return false;
        }
    }
}
