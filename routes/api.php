<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\OrderChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GhnController;
use App\Http\Controllers\Api\ChatbotController;

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


Route::post('/login', [AuthController::class, 'login']);

// Route public để tìm admin theo email (không cần auth)
Route::get('/admin/find-by-email', [ConversationController::class, 'findAdminByEmail']);

Route::apiResource('users', UserController::class);
// ->middleware('auth:sanctum'); // Ensure that the UserController is protected by Sanctum authentication

Route::middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/messages', [ConversationController::class, 'index']);
    Route::post('/messages', [ConversationController::class, 'store']);
    Route::delete('/messages/{id}', [ConversationController::class, 'destroy']);
    
    // Thêm route để tạo conversation mới
    Route::post('/conversations', [ConversationController::class, 'createConversation']);
    
    // Order Chat Routes
    Route::get('/orders/{orderId}/can-chat', [OrderChatController::class, 'canChat']);
    Route::post('/orders/{orderId}/start-chat', [OrderChatController::class, 'startChat']);
    Route::get('/orders/{orderId}/messages', [OrderChatController::class, 'getMessages']);
});
// Chatbot API Routes
Route::prefix('chatbot')->group(function () {
    Route::post('/message', [ChatbotController::class, 'processMessage']);
    Route::get('/categories', [ChatbotController::class, 'getCategories']);
    Route::post('/books-by-category', [ChatbotController::class, 'getBooksByCategory']);
});