<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Show the user's profile.
     */
    public function index()
    {
        $user = Auth::user();

        $activities = DB::table('activities')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile.index-profile', compact('user', 'activities'));
    }
    /**
     * Show the edit form for the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit-profile', compact('user'));
    }




    /**
     * Update the user's profile information.
     */


    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:15',
            'address'      => 'required|string',
            'profile_pic'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        $data = $request->only('name', 'email', 'phone_number', 'address');

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $path = $file->store('profile_pics', 'public');

            // Delete old profile picture if exists
            if ($user->profile_pic) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_pic);
            }

            $data['profile_pic'] = $path;
        }

        if ($user->update($data)) {
            return redirect()->back()->with('success', 'Profile updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Profile update failed. Please try again.');
        }
    }



    /**
     * Change the user's password.
     */

    public function getChangePassword()
    {
        return view('profile.change-password');
    }





    public function changePassword(Request $request)
    {
        $user = Auth::user();

        // Check if the current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'The current password is incorrect.');
        }

        // Optionally, ensure new password and confirm password match
        if ($request->new_password !== $request->confirm_password) {
            return redirect()->back()->with('error', 'New password and confirm password do not match.');
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}
