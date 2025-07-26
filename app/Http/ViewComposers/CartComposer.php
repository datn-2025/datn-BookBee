<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Helpers\CartHelper;

class CartComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $cartItemCount = CartHelper::getCartItemCount();
        
        $view->with('cartItemCount', $cartItemCount);
    }
}
