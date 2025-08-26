<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherService
{
    public function getAvailableVouchers(User $user)
    {
        $now = now();
        return Voucher::where('status', 'active')
            ->where('valid_from', '<=', $now)
            ->where('valid_to', '>=', $now)
            ->where(function($query) {
                $query->whereRaw('quantity > (SELECT COUNT(*) FROM applied_vouchers WHERE voucher_id = vouchers.id)')
                    ->orWhereNull('quantity');
            })
            ->whereNotExists(function($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('applied_vouchers')
                    ->whereRaw('applied_vouchers.voucher_id = vouchers.id')
                    ->where('user_id', $user->id);
            })
            ->get()
            ->map(function($voucher) {
                // Mặc định là chưa áp dụng
                $voucher->is_applied = false;
                return $voucher;
            });
    }

    // public function calculateDiscount(Voucher $voucher, $subtotal)
    // {
    //     if (!$this->isVoucherValid($voucher, $subtotal)) {
    //         return 0;
    //     }
    //     // dd($voucher);
    //     $discount = $subtotal * ($voucher->discount_percent / 100);
    //     if ($voucher->max_discount && $discount > $voucher->max_discount) {
    //         $discount = $voucher->max_discount;
    //     }

    //     return $discount;
    // }

    public function calculateDiscount(Voucher $voucher, $subtotal, $cartItems = null)
    {
        $validationResult = $this->isVoucherValid($voucher, $subtotal, $cartItems);
        
        if (!$validationResult['valid']) {
            return [
                'discount' => 0,
                'errors' => $validationResult['errors']
            ];
        }

        // Hỗ trợ 2 loại giảm giá: cố định (fixed) và phần trăm (percent)
        $discount = 0;

        if (isset($voucher->discount_type) && $voucher->discount_type === 'fixed') {
            // Giảm giá số tiền cố định, không vượt quá subtotal
            $fixed = (float) ($voucher->fixed_discount ?? 0);
            if ($fixed < 0) { $fixed = 0; }
            $discount = min($fixed, (float) $subtotal);
        } else {
            // Mặc định: phần trăm
            $percent = (float) ($voucher->discount_percent ?? 0);
            if ($percent < 0) { $percent = 0; }
            $discount = ((float) $subtotal) * ($percent / 100);
            $max = $voucher->max_discount ?? null;
            if ($max !== null && $max !== '' && $discount > (float) $max) {
                $discount = (float) $max;
            }
        }

        return ['discount' => $discount];
    }

    public function validateVoucher(string $code, User $user, $subtotal = 0, $cartItems = null)
    {
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá không tồn tại'
            ];
        }

        $validationResult = $this->isVoucherValid($voucher, $subtotal, $cartItems);
        if (!$validationResult['valid']) {
            return [
                'valid' => false,
                'message' => 'Mã giảm giá không còn hiệu lực',
                'errors' => $validationResult['errors']
            ];
        }

        // Kiểm tra số lần sử dụng của người dùng
        $userUsageCount = $voucher->appliedVouchers()
            ->where('user_id', $user->id)
            ->count();

        if ($userUsageCount >= 1) { // Giả sử mỗi người chỉ được dùng 1 lần
            return [
                'valid' => false,
                'message' => 'Bạn đã sử dụng mã giảm giá này'
            ];
        }

        return [
            'valid' => true,
            'voucher' => $voucher
        ];
    }

    // protected function isVoucherValid(Voucher $voucher, $subtotal = 0)
    // {
    //     $now = now();

    //     // Kiểm tra trạng thái và thời hạn
    //     if ($voucher->status !== 'active' ||
    //         $now->lt($voucher->valid_from) ||
    //         $now->gt($voucher->valid_to)) {
    //         return false;
    //     }

    //     // Kiểm tra giá trị đơn hàng tối thiểu
    //     if ($subtotal < $voucher->min_order_value) {
    //         return false;
    //     }

    //     // Kiểm tra số lượng voucher còn lại
    //     $usedCount = $voucher->appliedVouchers()->count();
    //     if ($voucher->quantity && $usedCount >= $voucher->quantity) {
    //         return false;
    //     }

    //     return true;
    // }

    protected function isVoucherValid(Voucher $voucher, $subtotal = 0, $cartItems = null)
    {
        $now = now();
        $failReasons = [];

        // Kiểm tra trạng thái và thời hạn
        if ($voucher->status != 'active') {
            $failReasons[] = 'Voucher Không Hoạt Động';
        }
        if ($now->lt($voucher->valid_from)) {
            $failReasons[] = 'Voucher Chưa Đến Thời Gian Áp Dụng';
        }
        if ($now->gt($voucher->valid_to)) {
            $failReasons[] = 'Voucher Đã Hết Hạn';
        }

        // Kiểm tra giá trị đơn hàng tối thiểu
        if ($subtotal < $voucher->min_order_value) {
            $failReasons[] = 'Voucher Chỉ Áp Dụng Với Đơn Hàng Tối Thiếu Giá Trị ' . number_format($voucher->min_order_value, 0, ',', '.') . 'đ';
        }

        // Kiểm tra số lượng voucher còn lại
        $usedCount = $voucher->appliedVouchers()->count();
        if ($voucher->quantity && $usedCount >= $voucher->quantity) {
            $failReasons[] = 'Voucher Đã Sử Dụng Hết';
        }

        // Kiểm tra điều kiện áp dụng sản phẩm cụ thể
        if ($cartItems && !$this->checkVoucherProductConditions($voucher, $cartItems)) {
            $failReasons[] = 'Voucher không áp dụng được cho các sản phẩm trong giỏ hàng của bạn';
        }

        if (!empty($failReasons)) {
            Log::debug('Voucher validation failed: '.$voucher->code, [
                'reasons' => $failReasons,
                'voucher' => $voucher->toArray(),
                'subtotal' => $subtotal
            ]);

            return ['valid' => false, 'errors' => $failReasons];
        }

        return ['valid' => true];
    }

    /**
     * Kiểm tra điều kiện áp dụng voucher cho sản phẩm cụ thể
     */
    protected function checkVoucherProductConditions(Voucher $voucher, $cartItems)
    {
        // Nếu voucher áp dụng cho tất cả sản phẩm (type = 'all')
        if ($voucher->conditions()->where('type', 'all')->exists()) {
            return true;
        }

        // Load voucher conditions với eager loading
        $voucher->load('conditions');

        // Kiểm tra từng item trong giỏ hàng
        foreach ($cartItems as $item) {
            $canApply = false;

            // Xử lý combo
            if (isset($item->is_combo) && $item->is_combo && isset($item->collection)) {
                // Với combo, kiểm tra từng sách trong collection
                if ($item->collection && $item->collection->books) {
                    foreach ($item->collection->books as $book) {
                        if ($voucher->canApplyToBook($book)) {
                            $canApply = true;
                            break;
                        }
                    }
                }
            } elseif (isset($item->book)) {
                // Với sách đơn lẻ
                $canApply = $voucher->canApplyToBook($item->book);
            }

            // Nếu có ít nhất 1 sản phẩm có thể áp dụng voucher thì OK
            if ($canApply) {
                Log::debug('Voucher can apply to item', [
                    'voucher_code' => $voucher->code,
                    'item_type' => isset($item->is_combo) && $item->is_combo ? 'combo' : 'book',
                    'item_id' => isset($item->collection_id) ? $item->collection_id : ($item->book_id ?? 'unknown')
                ]);
                return true;
            }
        }

        // Log khi không có sản phẩm nào match
        Log::debug('Voucher cannot apply to any cart items', [
            'voucher_code' => $voucher->code,
            'cart_items_count' => $cartItems->count(),
            'voucher_conditions' => $voucher->conditions->pluck('type', 'condition_id')->toArray()
        ]);

        // Nếu không có sản phẩm nào có thể áp dụng voucher
        return false;
    }
}
