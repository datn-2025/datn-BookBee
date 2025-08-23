<?php

namespace App\Observers;

use App\Models\BookFormat;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookFormatObserver
{
    /**
     * Handle the BookFormat "updated" event.
     * Đồng bộ giá trong cart khi admin thay đổi giá sách
     */
    public function updated(BookFormat $bookFormat)
    {
        // Chỉ xử lý khi giá thay đổi
        if ($bookFormat->isDirty('price')) {
            $oldPrice = $bookFormat->getOriginal('price');
            $newPrice = $bookFormat->price;
            
            Log::info('BookFormat price changed', [
                'book_format_id' => $bookFormat->id,
                'book_id' => $bookFormat->book_id,
                'old_price' => $oldPrice,
                'new_price' => $newPrice
            ]);

            // Cập nhật giá trong tất cả cart items có sản phẩm này
            $updatedCount = DB::table('carts')
                ->where('book_format_id', $bookFormat->id)
                ->update([
                    'price' => $newPrice,
                    'updated_at' => now()
                ]);

            if ($updatedCount > 0) {
                Log::info('Cart prices synchronized', [
                    'book_format_id' => $bookFormat->id,
                    'updated_carts' => $updatedCount,
                    'new_price' => $newPrice
                ]);
            }
        }
    }

    /**
     * Handle the BookFormat "updating" event.
     * Validate giá mới trước khi cập nhật
     */
    public function updating(BookFormat $bookFormat)
    {
        // Đảm bảo giá không âm
        if ($bookFormat->isDirty('price') && $bookFormat->price < 0) {
            Log::warning('Attempted to set negative price', [
                'book_format_id' => $bookFormat->id,
                'attempted_price' => $bookFormat->price
            ]);
            
            // Có thể throw exception hoặc set về 0
            $bookFormat->price = 0;
        }
    }
}
