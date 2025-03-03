<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('admin')->name('dashboard');


Route::resource('users', UserController::class)->middleware('admin');

Route::get('/create-roles', [UserController::class, 'getCreateRoles'])->name('create.roles')->middleware('admin');
Route::post('/add-new-role', [UserController::class, 'addRole'])->name('add-role');
Route::get('/upcoming-users', [HRController::class, 'getHrApplications'])->name('upcoming.users')->middleware('admin');
Route::get('/vendor-application', [VendorController::class, 'getVendorApplications'])->name('vendor.application')->middleware('admin');
Route::post('/vendors/invite', [VendorController::class, 'inviteVendor'])->name('vendors.invite')->middleware('admin');
Route::put('/vendor/{vendor}/update-status/{status}', [VendorController::class, 'updateVendorStatus'])->middleware('admin');

Route::post('/hr/employee/{id}/create-account', [HrController::class, 'createHrAccount'])->name('hr.employee.create_account');




Route::middleware('admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::get('/profile/password', [ProfileController::class, 'getChangePassword'])->name('profile.change-pass');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
});



Route::middleware(['guest'])->group(function () {
    // Guest-only routes (e.g., login and registration)
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
    Route::get('/', function () {
        return redirect()->route('auth.login');
    });

    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');






Route::get('/chats', function () {
    return view('admin.applications.chat');
})->middleware('admin')->name('applications.chat');

Route::get('/two-factor', function () {
    return view('auth.two-factor');
})->middleware('admin')->name('auth.two-factor');




Route::get('/inbox', [EmailController::class, 'fetchEmails'])->name('applications.inbox');
Route::get('/emails/{id}', [EmailController::class, 'show'])->name('emails.show');
Route::post('/emails/send', [EmailController::class, 'sendEmail'])->name('emails.send');
Route::get('/email/{uid}', [EmailController::class, 'show']);

Route::get('/sales-report', [SalesReportController::class, 'index'])->name('reports.sales'); // Fetch alssl sales


Route::get('/documents', [DocumentController::class, 'index'])->name('admin.documents');
Route::post('/documents/upload', [DocumentController::class, 'store'])->name('documents.upload');
Route::get('/documents/view/{id}', [DocumentController::class, 'view'])->name('documents.view');
Route::get('/documents/download/{id}', [DocumentController::class, 'download'])->name('documents.download');
Route::delete('/documents/delete/{id}', [DocumentController::class, 'delete'])->name('documents.delete');



Route::get('/invoices', [InvoiceController::class, 'getInvoices'])->name('reports.invoices')->middleware('admin');
Route::get('/admin/invoices/analyze', [InvoiceController::class, 'analyzeInvoices'])
    ->name('admin.invoices.analyze');
Route::put('/vendor/{id}/update-status/{status}', [VendorController::class, 'updateVendorStatus']);
