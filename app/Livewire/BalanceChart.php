<?php
// File: app/Livewire/BalanceChart.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class BalanceChart extends Component
{
    public $timeRange = 'all';
    public $fromDate;
    public $toDate;
    public $chartLabels = [];
    public $chartData = [];

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
            $this->loadData();
        }
    }

    public function resetFilters()
    {
        $this->timeRange = 'all';
        $this->fromDate = null;
        $this->toDate = null;
        $this->loadData();
        $this->dispatch('resetDateInputs');
    }

    public function loadData()
    {
        $query = Payment::query()->whereNotNull('paid_at');

        if ($this->timeRange === 'all' && $this->fromDate && $this->toDate) {
            $transactions = $query
                ->whereBetween('paid_at', [$this->fromDate, $this->toDate])
                ->selectRaw('DATE(paid_at) as label, SUM(amount) as total')
                ->groupBy('label')
                ->orderBy('label')
                ->get();

            $this->chartLabels = $transactions->pluck('label')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
            $this->chartData = $transactions->pluck('total')->map(fn($a) => (float)$a)->toArray();

            $this->dispatch('refreshChart', chartLabels: $this->chartLabels, chartData: $this->chartData);
            return;
        }

        $this->chartLabels = [];
        $this->chartData = [];

        switch ($this->timeRange) {
            case 'day':
                $transactions = $query->whereDate('paid_at', now())
                    ->selectRaw('HOUR(paid_at) as label, SUM(amount) as total')
                    ->groupBy('label')
                    ->get();

                $this->chartLabels = $transactions->pluck('label')->map(fn($h) => $h . 'h')->toArray();
                $this->chartData = $transactions->pluck('total')->map(fn($a) => (float)$a)->toArray();
                break;

            case 'week':
                $transactions = $query->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->selectRaw('DATE(paid_at) as label, SUM(amount) as total')
                    ->groupBy('label')->orderBy('label')->get();

                $this->chartLabels = $transactions->pluck('label')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
                $this->chartData = $transactions->pluck('total')->map(fn($a) => (float)$a)->toArray();
                break;

            case 'month':
                $transactions = $query->whereBetween('paid_at', [now()->startOfMonth(), now()])
                    ->selectRaw('DATE(paid_at) as label, SUM(amount) as total')
                    ->groupBy('label')->orderBy('label')->get();

                $this->chartLabels = $transactions->pluck('label')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
                $this->chartData = $transactions->pluck('total')->map(fn($a) => (float)$a)->toArray();
                break;

            case 'quarter':
                $start = now()->startOfQuarter();
                $end = now()->endOfQuarter();

                // Lấy dữ liệu cho quý và thêm tháng còn thiếu nếu không có dữ liệu
                $transactions = $query->whereBetween('paid_at', [$start, $end])
                    ->selectRaw('YEAR(paid_at) as year, MONTH(paid_at) as month, SUM(amount) as total')
                    ->groupByRaw('YEAR(paid_at), MONTH(paid_at)')
                    ->orderByRaw('MONTH(paid_at)')
                    ->get();

                $map = $transactions->keyBy('month');

                // Tạo labels và data cho biểu đồ, kiểm tra nếu không có giao dịch cho tháng thì gán 0
                for ($i = 0; $i < 3; $i++) {
                    $month = $start->copy()->addMonths($i)->month;
                    $year = $start->year;
                    $this->chartLabels[] = "$year/" . str_pad($month, 2, '0', STR_PAD_LEFT);
                    $this->chartData[] = isset($map[$month]) ? (float)$map[$month]->total : 0;
                }

                $this->dispatch('refreshChart', chartLabels: $this->chartLabels, chartData: $this->chartData);
                break;

            default:
                $transactions = $query->selectRaw('DATE(paid_at) as label, SUM(amount) as total')
                    ->groupBy('label')->orderBy('label')->get();

                $this->chartLabels = $transactions->pluck('label')->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
                $this->chartData = $transactions->pluck('total')->map(fn($a) => (float)$a)->toArray();
                break;
        }

        $this->dispatch('refreshChart', chartLabels: $this->chartLabels, chartData: $this->chartData);
    }

    public function render()
    {
        return view('livewire.balance-chart');
    }
}
