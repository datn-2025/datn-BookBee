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
    public $appliedFromDate;
    public $appliedToDate;
    public $showDefaultMessage = true;

    public function mount()
    {
        // Khởi tạo null để hiển thị placeholder mm/dd/yyyy
        $this->fromDate = null;
        $this->toDate = null;
        // Chưa áp dụng filter nào
        $this->appliedFromDate = null;
        $this->appliedToDate = null;
    }

    public function applyCustomFilter()
    {
        if ($this->fromDate && $this->toDate) {
            try {
                // Input date HTML trả về định dạng Y-m-d, chúng ta chỉ cần sử dụng trực tiếp
                $this->appliedFromDate = $this->fromDate;
                $this->appliedToDate = $this->toDate;
                $this->showDefaultMessage = false;
            } catch (\Exception $e) {
                // Nếu có lỗi, giữ nguyên trạng thái hiện tại
                session()->flash('error', 'Lỗi định dạng ngày. Vui lòng thử lại.');
            }
        }
    }

    public function resetFilters()
    {
        // Reset về null để hiển thị placeholder mm/dd/yyyy
        $this->fromDate = null;
        $this->toDate = null;
        $this->appliedFromDate = null;
        $this->appliedToDate = null;
        $this->showDefaultMessage = true;
    }

    public function render()
    {
        // Nếu chưa áp dụng filter, hiển thị top 5 voucher có số đơn hàng cao nhất toàn hệ thống
        if ($this->showDefaultMessage || !$this->appliedFromDate || !$this->appliedToDate) {
            // Lấy tất cả voucher đã được sử dụng
            $allUsedVoucherIds = AppliedVoucher::distinct()->pluck('voucher_id');
            $vouchers = Voucher::whereIn('id', $allUsedVoucherIds)->get();

            $totalOrders = Order::count(); // Tổng tất cả đơn hàng

            $report = $vouchers->map(function ($voucher) use ($totalOrders) {
                // Đếm tất cả đơn hàng sử dụng voucher này
                $appliedVouchers = AppliedVoucher::where('voucher_id', $voucher->id)->get();
                $orderIds = $appliedVouchers->pluck('order_id')->filter();
                $orders = Order::whereIn('id', $orderIds)->get();

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
            })
                // Sắp xếp theo số đơn hàng giảm dần và lấy 5 voucher hàng đầu
                ->sortByDesc('orderCount')
                ->take(5)
                ->values();

            return view('livewire.voucher-effectiveness-report', compact('report'));
        }

        // Khi đã áp dụng filter: hiển thị TẤT CẢ voucher trong khoảng thời gian
        $usedVoucherIds = AppliedVoucher::whereBetween('used_at', [$this->appliedFromDate, $this->appliedToDate])
            ->distinct()
            ->pluck('voucher_id');

        $vouchers = Voucher::whereIn('id', $usedVoucherIds)->get();
        $totalOrders = Order::whereBetween('created_at', [$this->appliedFromDate, $this->appliedToDate])->count();

        $report = $vouchers->map(function ($voucher) use ($totalOrders) {
            $appliedVouchers = AppliedVoucher::where('voucher_id', $voucher->id)
                ->whereBetween('used_at', [$this->appliedFromDate, $this->appliedToDate])
                ->get();

            $orderIds = $appliedVouchers->pluck('order_id')->filter();
            $orders = Order::whereIn('id', $orderIds)
                ->whereBetween('created_at', [$this->appliedFromDate, $this->appliedToDate])
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
        })
            // Khi lọc: sắp xếp theo số đơn hàng giảm dần nhưng KHÔNG giới hạn số lượng
            ->sortByDesc('orderCount')
            ->values();

        return view('livewire.voucher-effectiveness-report', compact('report'));
    }
}
