<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\DocumentController;
use App\Http\Middleware\RedirectIfAuthenticatedFor2FA;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BudgetReportController;
use App\Http\Controllers\LogisticReportController;
use App\Http\Controllers\ProcurementController;

Route::get('/dashboard', [DashboardController::class, 'getDashboard'])->name('dashboard')->middleware('admin');


Route::resource('users', UserController::class)->middleware('admin');

Route::get('/create-roles', [UserController::class, 'getCreateRoles'])->name('create.roles')->middleware('admin');
Route::post('/add-new-role', [UserController::class, 'addRole'])->name('add-role');
Route::get('/upcoming-users', [HRController::class, 'getHrApplications'])->name('upcoming.users')->middleware('admin');
Route::get('/vendor-application', [VendorController::class, 'getVendorApplications'])->name('vendor.application')->middleware('admin');
Route::post('/vendors/invite', [VendorController::class, 'inviteVendor'])->name('vendors.invite')->middleware('admin');
Route::put('/vendor/{vendor}/update-status/{status}', [VendorController::class, 'updateVendorStatus'])->middleware('admin');

Route::post('/hr/employee/{id}/create-account', [HrController::class, 'createHrAccount'])->name('hr.employee.create_account');
Route::get('/employee-salaries', [HrController::class, 'fetchPayrollData'])->name('reports.salaries');




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




Route::middleware(['redirect.if.authenticated.2fa'])->group(function () {
    Route::get('/2fa/verify', function () {
        return view('auth.two-factor');
    })->name('auth.2fa.verify');
    Route::post('/2fa/verify', [AuthController::class, 'verify2FA'])->name('auth.2fa.verify');
    Route::post('/2fa/resend', [AuthController::class, 'resend2FA'])->name('auth.2fa.resend');
});

Route::get('/chats', function () {
    return view('admin.applications.chat');
})->middleware('admin')->name('applications.chat');




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
Route::get('/document-history', [DocumentController::class, 'getDocumentHistory']);


Route::get('/notifications-counts', [NotificationController::class, 'getNotifications'])->name('notifications.get');

Route::get('/notifications', [NotificationController::class, 'showNotifications']);
Route::delete('/notifications/clear', [NotificationController::class, 'clear'])->name('notifications.clear');

Route::get('/invoices', [InvoiceController::class, 'getInvoices'])->name('reports.invoices')->middleware('admin');
Route::get('/admin/invoices/analyze', [InvoiceController::class, 'analyzeInvoices'])
    ->name('admin.invoices.analyze');
Route::get('/admin/invoices/{id}', [InvoiceController::class, 'showInvoice'])->name('invoices.show');


Route::put('/vendor/{id}/update-status/{status}', [VendorController::class, 'updateVendorStatus']);


Route::get('/user/{id}/activity-logs', [UserController::class, 'getUserActivityLogs']);




Route::get('/budgets', [BudgetReportController::class, 'index'])->name('budgets.index');
Route::get('/logistics', [LogisticReportController::class, 'index'])->name('logistics.index');
Route::get('/approvals/procurement', [ProcurementController::class, 'index'])->name('approvals.procurement');



//FOR DASHBOARD
Route::get('/sales-data', [DashboardController::class, 'getSalesData']);
Route::get('/invoices/count', [DashboardController::class, 'getInvoiceCount']);
Route::get('/invoice-analytics', [DashboardController::class, 'getInvoiceAnalytics']);
Route::get('/fetch-employees', [DashboardController::class, 'fetchHrApplications']);
Route::get('/document-stats', [DashboardController::class, 'getDocumentStats']);
