<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistHelper
{
    /**
     * Get the number of items in user's wishlist
     */
    public static function getWishlistItemCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        $user = Auth::user();
        
        // Count distinct items in wishlist for this user
        $itemCount = DB::table('wishlists')
            ->where('user_id', $user->id)
            ->count();

        return (int) $itemCount;
    }
}
