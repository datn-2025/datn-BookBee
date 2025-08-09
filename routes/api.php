<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GhnController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\LocationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Location API Routes
Route::get('/districts/{provinceId}', [LocationController::class, 'getDistricts']);
Route::get('/wards/{districtId}', [LocationController::class, 'getWards']);

// GHN API Routes
Route::prefix('ghn')->group(function () {
    Route::get('/provinces', [GhnController::class, 'getProvinces']);
    Route::post('/districts', [GhnController::class, 'getDistricts']);
    Route::post('/wards', [GhnController::class, 'getWards']);
    Route::post('/calculate-fee', [GhnController::class, 'calculateShippingFee']);
    Route::post('/shipping-fee', [GhnController::class, 'calculateShippingFee']); // Alias for backward compatibility
    Route::post('/services', [GhnController::class, 'getServices']);
    Route::post('/lead-time', [GhnController::class, 'getLeadTime']);
    Route::get('/tracking/{orderCode}', [GhnController::class, 'trackOrder'])->name('api.ghn.tracking');
});

// Chatbot API Routes
Route::prefix('chatbot')->group(function () {
    Route::post('/message', [ChatbotController::class, 'processMessage']);
    Route::get('/categories', [ChatbotController::class, 'getCategories']);
    Route::post('/books-by-category', [ChatbotController::class, 'getBooksByCategory']);
});