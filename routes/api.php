<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIs\ApiUserController;
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



// Protected routes (require Sanctum authentication)

// User routes
// User routes
Route::get('/users', [ApiUserController::class, 'index']);
Route::get('/users/{user}', [ApiUserController::class, 'show']);
Route::put('/users/{user}', [ApiUserController::class, 'update']);


// Authentication route
Route::post('/auth', [ApiUserController::class, 'authenticate']);
Route::patch('/change-password', [ApiUserController::class, 'changePassword']);
