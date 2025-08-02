<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GhnController;

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

// GHN API Routes
Route::prefix('ghn')->group(function () {
    Route::post('/provinces', [GhnController::class, 'getProvinces']);
    Route::post('/districts', [GhnController::class, 'getDistricts']);
    Route::post('/wards', [GhnController::class, 'getWards']);
    Route::post('/shipping-fee', [GhnController::class, 'calculateShippingFee']);
    Route::post('/services', [GhnController::class, 'getServices']);
    Route::post('/lead-time', [GhnController::class, 'getLeadTime']);
    Route::get('/tracking/{orderCode}', [GhnController::class, 'trackOrder'])->name('api.ghn.tracking');
});