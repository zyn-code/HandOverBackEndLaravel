<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractorAuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\OfferController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/contractor/register', [ContractorAuthController::class, 'register']);
Route::post('/registerclient', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('services/building', [ServiceController::class, 'buildingServices']);
Route::get('services/vehicle',  [ServiceController::class, 'vehicleServices']);
Route::get('services/emergency', [ServiceController::class, 'emergencyServices']);
// ✅ Protect these routes using JWT (auth:api guard)
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
     Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'create']);
    Route::get('/my-tasks', [TaskController::class, 'myTasks']);
    Route::get('/available-tasks', [TaskController::class, 'availableTasks']);
    Route::post('/tasks/{taskId}/offer', [OfferController::class, 'makeOffer']);
    Route::post('/offers/{offerId}/respond', [OfferController::class, 'respondToOffer']);
});
