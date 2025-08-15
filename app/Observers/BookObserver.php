<?php

namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookObserver
{
    /**
     * Handle the Book "deleting" event.
     * Xóa tất cả wishlist items khi sách bị xóa (soft delete)
     */
    public function deleting(Book $book)
    {
        try {
            Log::info('BookObserver deleting triggered', [
                'book_id' => $book->id,
                'book_title' => $book->title
            ]);

            // Xóa tất cả wishlist items có chứa sách này
            $deletedWishlistItems = DB::table('wishlists')
                ->where('book_id', $book->id)
                ->delete();

            if ($deletedWishlistItems > 0) {
                Log::info('Wishlist items auto-deleted on book deletion', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'deleted_wishlist_items' => $deletedWishlistItems
                ]);
            }

            // Xóa tất cả cart items có chứa sách này
            $deletedCartItems = DB::table('carts')
                ->where('book_id', $book->id)
                ->delete();

            if ($deletedCartItems > 0) {
                Log::info('Cart items auto-deleted on book deletion', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'deleted_cart_items' => $deletedCartItems
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error deleting wishlist/cart items on book deletion', [
                'book_id' => $book->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Book "forceDeleting" event.
     * Xóa tất cả wishlist items khi sách bị xóa cứng
     */
    public function forceDeleting(Book $book)
    {
        try {
            // Xóa tất cả wishlist items có chứa sách này
            $deletedWishlistItems = DB::table('wishlists')
                ->where('book_id', $book->id)
                ->delete();

            if ($deletedWishlistItems > 0) {
                Log::info('Wishlist items force-deleted on book force deletion', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'deleted_wishlist_items' => $deletedWishlistItems
                ]);
            }

            // Xóa tất cả cart items có chứa sách này
            $deletedCartItems = DB::table('carts')
                ->where('book_id', $book->id)
                ->delete();

            if ($deletedCartItems > 0) {
                Log::info('Cart items force-deleted on book force deletion', [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'deleted_cart_items' => $deletedCartItems
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error force deleting wishlist/cart items on book force deletion', [
                'book_id' => $book->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
