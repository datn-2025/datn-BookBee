<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Helpers\CartHelper;
use App\Helpers\WishlistHelper;

class CartComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $cartItemCount = CartHelper::getCartDistinctItemCount();
        $wishlistItemCount = WishlistHelper::getWishlistItemCount();
        
        $view->with([
            'cartItemCount' => $cartItemCount,
            'wishlistItemCount' => $wishlistItemCount
        ]);
    }
}
