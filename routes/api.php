<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContractorAuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ContactController;
use App\Models\Casa;
use App\Models\CarBrand;
use App\Models\CarCategory;
use App\Models\FuelType;

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
Route::post('/contact', ContactController::class)->name('apicontact');
Route::get('/casas', function () {
    return response()->json(Casa::all());
});
Route::get('/car-brands', function () {
    return response()->json(CarBrand::all());
});
Route::get('/car-categories', function () {
    return response()->json(CarCategory::all());
});
Route::get('/fuel-types', function () {
    return response()->json(FuelType::all());
});
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
