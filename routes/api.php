<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('users', UserController::class);
// ->middleware('auth:sanctum'); // Ensure that the UserController is protected by Sanctum authentication

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/messages', [ConversationController::class, 'index']);
    Route::post('/messages', [ConversationController::class, 'store']);
    Route::delete('/messages/{id}', [ConversationController::class, 'destroy']);
    
    // Thêm route để tạo conversation mới
    Route::post('/conversations', [ConversationController::class, 'createConversation']);
});