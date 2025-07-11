<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Voucher;
use App\Models\Order;
use App\Models\AppliedVoucher;
use Carbon\Carbon;

class VoucherEffectivenessReport extends Component
{
    public $fromDate;
    public $toDate;

    public function mount()
    {
        $this->fromDate = Carbon::now()->startOfMonth()->toDateString();
        $this->toDate = Carbon::now()->toDateString();
    }

    public function applyCustomFilter()
    {
        // Không cần làm gì, Livewire sẽ tự động render lại khi fromDate/toDate thay đổi
    }

    public function resetFilters()
    {
        $this->fromDate = Carbon::now()->startOfMonth()->toDateString();
        $this->toDate = Carbon::now()->toDateString();
    }

    public function render()
    {
        // Lọc voucher có hiệu lực giao với khoảng lọc
        $vouchers = Voucher::where(function($query) {
            $query->where('valid_from', '<=', $this->toDate)
                  ->where('valid_to', '>=', $this->fromDate);
        })->get();

        $totalOrders = Order::whereBetween('created_at', [$this->fromDate, $this->toDate])->count();

        $report = $vouchers->map(function ($voucher) use ($totalOrders) {
            $appliedVouchers = AppliedVoucher::where('voucher_id', $voucher->id)
                ->whereBetween('used_at', [$this->fromDate, $this->toDate])
                ->get();
            $orderIds = $appliedVouchers->pluck('order_id')->filter();
            $orders = Order::whereIn('id', $orderIds)
                ->whereBetween('created_at', [$this->fromDate, $this->toDate])
                ->get();
            $orderCount = $orders->count();
            $totalRevenue = $orders->sum('total_amount');
            $totalDiscount = $orders->sum('discount_amount');
            $conversionRate = $totalOrders > 0 ? round($orderCount / $totalOrders * 100, 2) : 0;
            return [
                'name' => $voucher->code,
                'period' => $voucher->valid_from . ' - ' . $voucher->valid_to,
                'orderCount' => $orderCount,
                'totalRevenue' => $totalRevenue,
                'totalDiscount' => $totalDiscount,
                'conversionRate' => $conversionRate,
            ];
        });

        return view('livewire.voucher-effectiveness-report',compact('report'));
    }
}
