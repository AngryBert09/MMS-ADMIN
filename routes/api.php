<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\ApiUserController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\BudgetReportController;
use App\Http\Controllers\LogisticReportController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/





Route::get('/users', [ApiUserController::class, 'index']);
Route::get('/users/{user}', [ApiUserController::class, 'show']);
Route::put('/users/{user}', [ApiUserController::class, 'update']);

Route::get('/generate-sales-report', [SalesReportController::class, 'analyzeSales']);
Route::get('/analyze-budgets', [BudgetReportController::class, 'analyzeBudgets']);
Route::get('/analyze-logistics', [LogisticReportController::class, 'analyzeLogistics']);


// Authentication route
Route::post('/auth', [ApiUserController::class, 'authenticate']);
Route::patch('/change-password', [ApiUserController::class, 'changePassword']);
Route::post('/store-activity/{description}/{id}', [ApiUserController::class, 'storeActivity']);
