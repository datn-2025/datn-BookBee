<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Helpers\CartHelper;
use App\Helpers\WishlistHelper;
use App\Models\Category;

class CartComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $cartItemCount = CartHelper::getCartDistinctItemCount();
        $wishlistItemCount = WishlistHelper::getWishlistItemCount();
        
        // Get categories with book count for navbar (removed status filter as column doesn't exist)
        $navCategories = Category::withCount('books')
            ->orderBy('name')
            ->get();
        
        $view->with([
            'cartItemCount' => $cartItemCount,
            'wishlistItemCount' => $wishlistItemCount,
            'navCategories' => $navCategories
        ]);
    }
}
