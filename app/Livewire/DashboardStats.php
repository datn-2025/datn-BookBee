<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Livewire\Component;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public $orderCount;
    public $customerCount;
    public $revenue;
    public $balance;
    public $timePeriod = null;

    public function mount()
    {
        $this->updateStats();
    }

    public function updateStats()
    {
        $queryOrder = Order::query();
        $queryUser = User::query();

        // Nếu có chọn thời gian => lọc theo thời gian
        if ($this->timePeriod) {
            $now = now();
            switch ($this->timePeriod) {
                case 'day':
                    $start = $now->copy()->startOfDay();
                    $end = $now->copy()->endOfDay();
                    break;
                case 'week':
                    $start = $now->copy()->startOfWeek();
                    $end = $now->copy()->endOfWeek();
                    break;
                case 'month':
                    $start = $now->copy()->startOfMonth();
                    $end = $now->copy()->endOfMonth();
                    break;
                case 'quarter':
                    $start = $now->copy()->startOfQuarter();
                    $end = $now->copy()->endOfQuarter();
                    break;
            }

            $queryOrder->whereBetween('created_at', [$start, $end]);
            $queryUser->whereBetween('created_at', [$start, $end]);
        }

        // Tổng số đơn hàng
        $this->orderCount = $queryOrder->count();

        // Tổng khách hàng có role là 'user'
        $this->customerCount = $queryUser->whereHas('role', function ($q) {
            $q->where('name', 'user');
        })->count();

        // Doanh thu thực nhận (chỉ đơn đã thanh toán & thành công)
        $revenueQuery = Order::whereHas('orderStatus', fn($q) =>
        $q->where('name', 'Thành công'))
            ->whereHas('paymentStatus', fn($q) =>
            $q->where('name', 'Đã Thanh Toán'));

        if ($this->timePeriod) {
            $revenueQuery->whereBetween('created_at', [$start, $end]);
        }

        $this->revenue = $revenueQuery->sum('total_amount');

        // Số dư: tổng tiền tất cả các đơn
        $balanceQuery = Payment::whereHas('paymentStatus', fn($q) =>
        $q->where('name', 'Đã thanh toán'))
            ->whereNotNull('paid_at');

        if ($this->timePeriod) {
            $balanceQuery ->whereBetween('created_at', [$start, $end]);
        }

        $this->balance = $balanceQuery ->sum('amount');
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
