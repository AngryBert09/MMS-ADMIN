<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('admin')->name('dashboard');


Route::resource('users', UserController::class)->middleware('admin');

Route::get('/create-roles', [UserController::class, 'getCreateRoles'])->name('create.roles')->middleware('admin');
Route::post('/add-new-role', [UserController::class, 'addRole'])->name('add-role');
Route::get('/upcoming-users', [UserController::class, 'getUpcomingUsers'])->name('upcoming.users')->middleware('admin');
Route::get('/vendor-application', [UserController::class, 'getVendorApplications'])->name('vendor.application')->middleware('admin');
Route::post('/vendors/invite', [VendorController::class, 'inviteVendor'])->name('vendors.invite')->middleware('admin');


Route::middleware('admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
});



Route::middleware(['guest'])->group(function () {
    // Guest-only routes (e.g., login and registration)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
