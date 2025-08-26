<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.backend')]
class RevenueReport extends Component
{
    public $timeRange = 'all';
    public $chartLabels = [];
    public $chartDataPhysical = [];
    public $chartDataEbook = [];
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
        // Base filter: only successful and paid orders
        $orderBaseQuery = Order::query()
            ->whereDate('created_at', '<=', now())
            ->whereHas('orderStatus', fn($q) => $q->where('name', 'Thành công'))
            ->whereHas('paymentStatus', fn($q) => $q->where('name', 'Đã Thanh Toán'));

        if ($this->timeRange === 'all' && $this->fromDate && $this->toDate) {
            // Custom range: group by date
            $orders = (clone $orderBaseQuery)
                ->whereBetween('created_at', [$this->fromDate, $this->toDate])
                ->selectRaw('DATE(created_at) as label')
                ->groupBy('label')
                ->orderBy('label')
                ->pluck('label');

            $this->chartLabels = collect($orders)->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();

            // Aggregate from order_items to split ebook vs physical
            $itemsAgg = DB::table('order_items as oi')
                ->join('orders as o', 'oi.order_id', '=', 'o.id')
                ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
                ->whereBetween('o.created_at', [$this->fromDate, $this->toDate])
                ->whereIn('o.id', (clone $orderBaseQuery)->pluck('id'))
                ->selectRaw('DATE(o.created_at) as label,
                            SUM(CASE WHEN bf.file_url IS NOT NULL THEN oi.total ELSE 0 END) as ebook_revenue,
                            SUM(CASE WHEN bf.file_url IS NULL THEN oi.total ELSE 0 END) as physical_revenue')
                ->groupBy('label')
                ->orderBy('label')
                ->get()
                ->keyBy('label');

            $this->chartDataPhysical = [];
            $this->chartDataEbook = [];
            foreach ($this->chartLabels as $lbl) {
                $key = Carbon::parse($lbl)->format('Y-m-d');
                $row = $itemsAgg->get($key);
                $this->chartDataEbook[] = $row ? (float) $row->ebook_revenue : 0;
                $this->chartDataPhysical[] = $row ? (float) $row->physical_revenue : 0;
            }

            $this->dispatch('refreshChart', chartLabels: $this->chartLabels, chartDataPhysical: $this->chartDataPhysical, chartDataEbook: $this->chartDataEbook);
            return;
        }

        $this->chartLabels = [];
        $this->chartDataPhysical = [];
        $this->chartDataEbook = [];

        switch ($this->timeRange) {
            case 'day':
                // Initialize labels 0-23 hours
                $this->chartLabels = collect(range(0, 23))->map(fn($h) => $h . 'h')->toArray();

                $itemsAgg = DB::table('order_items as oi')
                    ->join('orders as o', 'oi.order_id', '=', 'o.id')
                    ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
                    ->whereDate('o.created_at', now())
                    ->whereIn('o.id', (clone $orderBaseQuery)->pluck('id'))
                    ->selectRaw('HOUR(o.created_at) as hour_lbl,
                                SUM(CASE WHEN bf.file_url IS NOT NULL THEN oi.total ELSE 0 END) as ebook_revenue,
                                SUM(CASE WHEN bf.file_url IS NULL THEN oi.total ELSE 0 END) as physical_revenue')
                    ->groupBy('hour_lbl')
                    ->get()
                    ->keyBy('hour_lbl');

                foreach (range(0, 23) as $h) {
                    $row = $itemsAgg->get($h);
                    $this->chartDataEbook[] = $row ? (float) $row->ebook_revenue : 0;
                    $this->chartDataPhysical[] = $row ? (float) $row->physical_revenue : 0;
                }
                break;

            case 'week':
                $start = now()->startOfWeek();
                $end = now()->endOfWeek();
                $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->copy()->addDay());
                $this->chartLabels = collect(iterator_to_array($period))
                    ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                    ->toArray();

                $itemsAgg = DB::table('order_items as oi')
                    ->join('orders as o', 'oi.order_id', '=', 'o.id')
                    ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
                    ->whereBetween('o.created_at', [$start, $end])
                    ->whereIn('o.id', (clone $orderBaseQuery)->pluck('id'))
                    ->selectRaw('DATE(o.created_at) as label,
                                SUM(CASE WHEN bf.file_url IS NOT NULL THEN oi.total ELSE 0 END) as ebook_revenue,
                                SUM(CASE WHEN bf.file_url IS NULL THEN oi.total ELSE 0 END) as physical_revenue')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get()
                    ->keyBy('label');

                foreach ($this->chartLabels as $lbl) {
                    $row = $itemsAgg->get($lbl);
                    $this->chartDataEbook[] = $row ? (float) $row->ebook_revenue : 0;
                    $this->chartDataPhysical[] = $row ? (float) $row->physical_revenue : 0;
                }
                break;

            case 'month':
                $start = now()->startOfMonth();
                $end = now();
                $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->copy()->addDay());
                $this->chartLabels = collect(iterator_to_array($period))
                    ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                    ->toArray();

                $itemsAgg = DB::table('order_items as oi')
                    ->join('orders as o', 'oi.order_id', '=', 'o.id')
                    ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
                    ->whereBetween('o.created_at', [$start, $end])
                    ->whereIn('o.id', (clone $orderBaseQuery)->pluck('id'))
                    ->selectRaw('DATE(o.created_at) as label,
                                SUM(CASE WHEN bf.file_url IS NOT NULL THEN oi.total ELSE 0 END) as ebook_revenue,
                                SUM(CASE WHEN bf.file_url IS NULL THEN oi.total ELSE 0 END) as physical_revenue')
                    ->groupBy('label')
                    ->orderBy('label')
                    ->get()
                    ->keyBy('label');

                foreach ($this->chartLabels as $lbl) {
                    $row = $itemsAgg->get($lbl);
                    $this->chartDataEbook[] = $row ? (float) $row->ebook_revenue : 0;
                    $this->chartDataPhysical[] = $row ? (float) $row->physical_revenue : 0;
                }
                break;

            case 'quarter':
                $start = now()->startOfQuarter();
                $end = now()->endOfQuarter();
                for ($i = 0; $i < 3; $i++) {
                    $month = $start->copy()->addMonths($i)->month;
                    $year = $start->year;
                    $this->chartLabels[] = "$year/" . str_pad($month, 2, '0', STR_PAD_LEFT);
                }

                $itemsAgg = DB::table('order_items as oi')
                    ->join('orders as o', 'oi.order_id', '=', 'o.id')
                    ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
                    ->whereBetween('o.created_at', [$start, $end])
                    ->whereIn('o.id', (clone $orderBaseQuery)->pluck('id'))
                    ->selectRaw('YEAR(o.created_at) as year, MONTH(o.created_at) as month,
                                SUM(CASE WHEN bf.file_url IS NOT NULL THEN oi.total ELSE 0 END) as ebook_revenue,
                                SUM(CASE WHEN bf.file_url IS NULL THEN oi.total ELSE 0 END) as physical_revenue')
                    ->groupByRaw('YEAR(o.created_at), MONTH(o.created_at)')
                    ->orderByRaw('YEAR(o.created_at), MONTH(o.created_at)')
                    ->get()
                    ->keyBy('month');

                foreach ($this->chartLabels as $lbl) {
                    [$y, $m] = explode('/', $lbl);
                    $m = (int) $m;
                    $row = $itemsAgg->get($m);
                    $this->chartDataEbook[] = $row ? (float) $row->ebook_revenue : 0;
                    $this->chartDataPhysical[] = $row ? (float) $row->physical_revenue : 0;
                }
                break;

            case 'all':
            default:
                $year = now()->year;
                $currentMonth = now()->month;
                for ($i = 1; $i <= $currentMonth; $i++) {
                    $this->chartLabels[] = "$year/" . str_pad($i, 2, '0', STR_PAD_LEFT);
                }

                $itemsAgg = DB::table('order_items as oi')
                    ->join('orders as o', 'oi.order_id', '=', 'o.id')
                    ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
                    ->whereIn('o.id', (clone $orderBaseQuery)->pluck('id'))
                    ->selectRaw('YEAR(o.created_at) as year, MONTH(o.created_at) as month,
                                SUM(CASE WHEN bf.file_url IS NOT NULL THEN oi.total ELSE 0 END) as ebook_revenue,
                                SUM(CASE WHEN bf.file_url IS NULL THEN oi.total ELSE 0 END) as physical_revenue')
                    ->groupByRaw('YEAR(o.created_at), MONTH(o.created_at)')
                    ->orderByRaw('YEAR(o.created_at), MONTH(o.created_at)')
                    ->get()
                    ->keyBy('month');

                foreach (range(1, $currentMonth) as $m) {
                    $row = $itemsAgg->get($m);
                    $this->chartDataEbook[] = $row ? (float) $row->ebook_revenue : 0;
                    $this->chartDataPhysical[] = $row ? (float) $row->physical_revenue : 0;
                }
                break;
        }

        $this->dispatch('refreshChart', chartLabels: $this->chartLabels, chartDataPhysical: $this->chartDataPhysical, chartDataEbook: $this->chartDataEbook);
    }

    public function render()
    {
        $sumPhysical = !empty($this->chartDataPhysical) ? array_sum($this->chartDataPhysical) : 0;
        $sumEbook = !empty($this->chartDataEbook) ? array_sum($this->chartDataEbook) : 0;
        $hasData = ($sumPhysical + $sumEbook) > 0;
        return view('livewire.revenue-report', compact('hasData'));
    }
}
