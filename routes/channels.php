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
    // Try to get user from different guards
    $webUser = Auth::user();
    $adminUser = auth('admin')->user();
    $currentUser = $webUser ?? $adminUser;

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
    
    // If still no user, try web guard
    if (!$user) {
        $user = auth('web')->user();
    }
    
    if (!$user) {
        Log::error('No user found in broadcasting channel');
        return false;
    }
    
    Log::info('Broadcasting channel access check', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'user_guard' => 'admin/web'
    ]);
    
    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        Log::error('Conversation not found', ['conversation_id' => $conversationId]);
        return false;
    }
    
    $hasAccess = $user->id === $conversation->admin_id || $user->id === $conversation->customer_id;
    Log::info('Channel access result', [
        'has_access' => $hasAccess,
        'user_id' => $user->id,
        'admin_id' => $conversation->admin_id,
        'customer_id' => $conversation->customer_id
    ]);
    
    return $hasAccess;
});
