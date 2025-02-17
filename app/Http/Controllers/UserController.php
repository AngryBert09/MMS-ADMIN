<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index-users', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Fetch all roles from the database
        $roles = DB::table('roles')->where('status', 'active')->get();

        // Pass roles to the view
        return view('admin.users.create-users', compact('roles'));
    }


    /**
     * Store a newly created user in storage.
     */


    public function store(Request $request)
    {
        Log::info('Received store request:', $request->except('password'));

        // Custom Validation Messages
        $messages = [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'The email is already taken. Try a different one.',
            'role.required' => 'Role is required.',
            'role.in' => 'Invalid role selected.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
        ];

        // Validate Input
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:admin,vendor,user,hr',
                'status' => 'required|in:active,inactive',
            ], $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
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
                'name' => $request->name,
                'email' => $request->email,
                'password' => $hashedPassword,
                'role' => $request->role,
                'status' => $request->status,
            ]);

            if (!$user) {
                Log::error('User creation failed.');
                DB::rollBack();
                return redirect()->back()->withErrors(['error' => 'Failed to create user.'])->withInput();
            }

            DB::commit(); // Commit transaction

            Log::info('User created successfully:', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
            ]);

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Something went wrong. Check logs.'])->withInput();
        }
    }







    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = DB::table('roles')->where('status', 'active')->get();

        // Pass roles to the view
        return view('admin.users.edit-users', compact('user', 'roles'));
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
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'role' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
                'password' => 'nullable|min:6|confirmed',
            ]);
            Log::info('Validation passed', ['validated' => $validated]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
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

            Log::info('User updated successfully', ['user_id' => $user->id]);
            return redirect()->route('users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withErrors(['error' => 'Failed to update user.'])->withInput();
        }
    }








    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Check if the user is an admin
        if ($user->role === 'admin') {
            // Count the number of admins in the database
            $adminCount = User::where('role', 'admin')->count();

            // Prevent deletion if this is the only admin
            if ($adminCount === 1) {
                return redirect()->route('users.index')->with('error', 'Cannot delete the last admin user.');
            }
        }

        // Proceed with deletion
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }


    public function getCreateRoles()
    { // Fetch all roles from the database
        $roles = DB::table('roles')->get();

        // Pass roles to the view
        return view('admin.users.create-role', compact('roles'));
    }


    public function addRole(Request $request)
    {
        try {
            Log::debug('Received request data:', $request->all());

            // Validate input
            $request->validate([
                'role' => 'string|required',
                'name' => 'nullable|string|unique:roles,name|max:255',
                'status' => 'required|in:active,inactive',
            ]);

            // Check if the selected role exists
            $existingRole = DB::table('roles')->where('name', $request->role)->first();
            Log::debug('Existing role lookup:', ['role' => $request->role, 'result' => $existingRole]);

            if ($existingRole) {
                // If role exists and user provides a different status, allow update
                if ($existingRole->status !== $request->status) {
                    DB::table('roles')->where('name', $request->role)->update([
                        'status' => $request->status,
                        'updated_at' => now()
                    ]);
                    Log::info('Role status updated:', ['role' => $request->role, 'new_status' => $request->status]);

                    return redirect()->route('create.roles')->with('success', 'Role status updated successfully.');
                }

                Log::info('Role already exists with the same status:', ['role' => $existingRole->name, 'status' => $existingRole->status]);
                return redirect()->route('create-roles')->with('success', 'Role already exists and is active.');
            }

            // If "Other (Enter New Role)" is selected, store a new role
            if ($request->role === 'custom' && !empty($request->name)) {
                $newRoleData = [
                    'name' => strtolower($request->name),
                    'status' => $request->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                Log::info('Adding new role:', $newRoleData);

                // Insert new role
                DB::table('roles')->insert($newRoleData);
                Log::debug('New role successfully inserted:', ['role' => $newRoleData]);

                return redirect()->route('create.roles')->with('success', 'New role added successfully.');
            }

            Log::warning('Invalid role selection:', ['role' => $request->role]);
            return redirect()->route('create-roles')->with('error', 'Invalid role selection.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error adding/updating role:', ['error' => $e->getMessage()]);
            return redirect()->route('create.roles')->with('error', 'An error occurred while processing the role.');
        }
    }

    public function getUpcomingUsers()
    {
        // API INTEGRATION FOR HR
        return view('admin.users.upcoming-users');
    }

    public function getVendorApplications()
    {
        // API INTEGRATION FOR LOGISTIC
        return view('admin.users.vendor-application');
    }
}
