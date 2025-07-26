<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartHelper
{
    /**
     * Get the total number of items in user's cart
     * Returns the sum of quantities, not just count of distinct items
     */
    public static function getCartItemCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        $user = Auth::user();
        
        // Sum all quantities in cart for this user
        $totalCount = DB::table('carts')
            ->where('user_id', $user->id)
            ->sum('quantity');

        return (int) $totalCount;
    }

    /**
     * Get the number of distinct items in user's cart
     * Returns count of different products, not total quantity
     */
    public static function getCartDistinctItemCount()
    {
        if (!Auth::check()) {
            return 0;
        }

        $user = Auth::user();
        
        // Count distinct items in cart for this user
        $itemCount = DB::table('carts')
            ->where('user_id', $user->id)
            ->count();

        return (int) $itemCount;
    }
}
