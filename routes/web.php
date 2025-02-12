<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('admin')->name('dashboard');


Route::resource('users', UserController::class)->middleware('admin');

Route::get('/create-roles', [UserController::class, 'getCreateRoles'])->name('create-roles')->middleware('admin');
Route::post('/add-new-role', [UserController::class, 'addRole'])->name('add-role');


Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
