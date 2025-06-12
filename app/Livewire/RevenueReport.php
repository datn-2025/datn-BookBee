<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class RevenueReport extends Component
{
    public $timeRange = 'all';
    public $chartLabels = [];
    public $chartData = [];
    public $fromDate;
    public $toDate;
    public $lastAppliedFrom;
    public $lastAppliedTo;


    public function mount()
    {
        $this->loadData();
    }

    public function updatedTimeRange()
    {
        if ($this->timeRange !== 'all') {
            $this->fromDate = null;
            $this->toDate = null;
            $this->loadData();
        }
    }

    public function applyCustomFilter()
    {
        if ($this->fromDate && $this->toDate) {
            // Cho phép áp dụng lại dù chọn giống
            $this->lastAppliedFrom = $this->fromDate;
            $this->lastAppliedTo = $this->toDate;
            $this->loadData();
        }
    }

    public function resetFilters()
    {
        $this->fromDate = null;
        $this->toDate = null;
        $this->lastAppliedFrom = null;
        $this->lastAppliedTo = null;
        $this->loadData();

        // Gửi sự kiện để reset UI của input date
        $this->dispatch('resetDateInputs');
    }


    public function loadData()
    {
        $query = Order::query();
        
        $query->whereHas('orderStatus', fn($q) =>
            $q->where('name', 'Thành công'))
            ->whereHas('paymentStatus', fn($q) =>
            $q->where('name', 'Đã Thanh Toán'));

        if ($this->timeRange === 'all' && $this->fromDate && $this->toDate) {
            $orders = $query
                ->whereBetween('created_at', [$this->fromDate, $this->toDate])
                ->selectRaw('DATE(created_at) as label, SUM(total_amount) as revenue')
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            $this->chartLabels = $orders->pluck('label')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
            $this->chartData = $orders->pluck('revenue')->map(fn($r) => (float)$r)->toArray();
            $this->dispatch('refreshChart', chartLabels: $this->chartLabels, chartData: $this->chartData);
            return;
        }

        $this->chartLabels = [];
        $this->chartData = [];

        switch ($this->timeRange) {
            case 'day':
                $orders = $query
                    ->whereDate('created_at', now())
                    ->selectRaw('HOUR(created_at) as label, SUM(total_amount) as revenue')
                    ->groupBy('label')->get();

                $this->chartLabels = $orders->pluck('label')->map(fn($h) => $h . 'h')->toArray();
                $this->chartData = $orders->pluck('revenue')->map(fn($r) => (float)$r)->toArray();
                break;

            case 'week':
                $orders = $query
                    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->selectRaw('DATE(created_at) as label, SUM(total_amount) as revenue')
                    ->groupBy('label')->orderBy('label')->get();

                $this->chartLabels = $orders->pluck('label')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
                $this->chartData = $orders->pluck('revenue')->map(fn($r) => (float)$r)->toArray();
                break;

            case 'month':
                $orders = $query
                    ->whereBetween('created_at', [now()->startOfMonth(), now()])
                    ->selectRaw('DATE(created_at) as label, SUM(total_amount) as revenue')
                    ->groupBy('label')->orderBy('label')->get();

                $this->chartLabels = $orders->pluck('label')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
                $this->chartData = $orders->pluck('revenue')->map(fn($r) => (float)$r)->toArray();
                break;

            case 'quarter':
                $start = now()->startOfQuarter();
                $end = now()->endOfQuarter();

                $orders = $query
                    ->whereBetween('created_at', [$start, $end])
                    ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue')
                    ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                    ->orderByRaw('MONTH(created_at)')->get();

                $map = $orders->keyBy('month');

                for ($i = 0; $i < 3; $i++) {
                    $month = $start->copy()->addMonths($i)->month;
                    $year = $start->year;
                    $this->chartLabels[] = "$year/" . str_pad($month, 2, '0', STR_PAD_LEFT);
                    $this->chartData[] = isset($map[$month]) ? (float)$map[$month]->revenue : 0;
                }
                break;

            case 'all':
            default:
                $orders = $query
                    ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as revenue')
                    ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                    ->orderByRaw('YEAR(created_at), MONTH(created_at)')
                    ->get();

                $map = $orders->keyBy('month');
                $year = now()->year;

                for ($i = 1; $i <= 12; $i++) {
                    $this->chartLabels[] = "$year/" . str_pad($i, 2, '0', STR_PAD_LEFT);
                    $this->chartData[] = isset($map[$i]) ? (float)$map[$i]->revenue : 0;
                }
                break;
        }

        $this->dispatch('refreshChart', chartLabels: $this->chartLabels, chartData: $this->chartData);
    }

    public function render()
    {
        return view('livewire.revenue-report');
    }
}
