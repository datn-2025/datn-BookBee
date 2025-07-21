<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user-status', function ($user) {
    return $user != null; // Allow all authenticated users
});

// We don't need private channel authentication anymore as we're using public channels

Broadcast::channel('bookbee.{conversationId}', function ($user, $conversationId) {
    // Cho phép tất cả user authenticated truy cập (public channel behavior)
    return true;
});

// Backup cho private channel nếu cần
Broadcast::channel('bookbee.private.{conversationId}', function ($user, $conversationId) {
    // Try to get user from different guards
    $webUser = Auth::user();
    $adminUser = auth('admin')->user();
    $currentUser = $webUser ?? $adminUser ?? $user;

    if (!$currentUser) {
        Log::error('No authenticated user found for broadcasting');
        return false;
    }

    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        Log::error('Conversation not found', ['id' => $conversationId]);
        return false;
    }

    // Check if user has access to this conversation
    $hasAccess = $currentUser->id === $conversation->admin_id || 
                 $currentUser->id === $conversation->customer_id;

    Log::info('Broadcasting auth check', [
        'user_id' => $currentUser->id,
        'conversation_id' => $conversationId,
        'has_access' => $hasAccess
    ]);

    return $hasAccess;
});
