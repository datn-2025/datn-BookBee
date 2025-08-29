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

    // KPI bổ sung
    public $refundRateEbook = 0.0;        // % = (Doanh thu ebook trong các đơn có yêu cầu hoàn tiền) / (Tổng doanh thu ebook)
    public $refundRatePhysical = 0.0;     // % = (Doanh thu vật lý trong các đơn có yêu cầu hoàn tiền) / (Tổng doanh thu vật lý)
    public $ebookOrderShare = 0.0;        // % = (Số đơn có ebook) / (Tổng số đơn)


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

            // KPIs cho custom range (không lọc trạng thái, chỉ theo thời gian)
            $this->computeKpis(
                baseOrderQuery: Order::query()->whereBetween('created_at', [$this->fromDate, $this->toDate])
            );

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
                // KPIs cho ngày hiện tại (không lọc trạng thái)
                $this->computeKpis(
                    baseOrderQuery: Order::query()->whereDate('created_at', now())
                );
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
                // KPIs cho tuần hiện tại (không lọc trạng thái)
                $this->computeKpis(
                    baseOrderQuery: Order::query()->whereBetween('created_at', [$start, $end])
                );
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
                // KPIs cho tháng hiện tại (không lọc trạng thái)
                $this->computeKpis(
                    baseOrderQuery: Order::query()->whereBetween('created_at', [$start, $end])
                );
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
                // KPIs cho quý hiện tại (không lọc trạng thái)
                $this->computeKpis(
                    baseOrderQuery: Order::query()->whereBetween('created_at', [$start, $end])
                );
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
                // KPIs cho năm đến tháng hiện tại (không lọc trạng thái)
                $startYear = now()->startOfYear();
                $endNow = now();
                $this->computeKpis(
                    baseOrderQuery: Order::query()->whereBetween('created_at', [$startYear, $endNow])
                );
                break;
        }

        $this->dispatch('refreshChart', chartLabels: $this->chartLabels, chartDataPhysical: $this->chartDataPhysical, chartDataEbook: $this->chartDataEbook);
    }

    public function render()
    {
        $sumPhysical = !empty($this->chartDataPhysical) ? array_sum($this->chartDataPhysical) : 0;
        $sumEbook = !empty($this->chartDataEbook) ? array_sum($this->chartDataEbook) : 0;
        $hasData = ($sumPhysical + $sumEbook) > 0;
        return view('livewire.revenue-report', compact('hasData', 'sumPhysical', 'sumEbook'));
    }

    /**
     * Tính các KPI: refundRateEbook, refundRatePhysical, ebookOrderShare
     * baseOrderQuery: query đã lọc theo khoảng thời gian tương ứng
     */
    protected function computeKpis($baseOrderQuery): void
    {
        // Lấy danh sách order IDs trong khoảng (chuẩn hoá về string để intersect chính xác)
        $orderIds = (clone $baseOrderQuery)
            ->pluck('id')
            ->map(fn($v) => (string) $v)
            ->values();

        if ($orderIds->isEmpty()) {
            $this->refundRateEbook = 0.0;
            $this->refundRatePhysical = 0.0;
            $this->ebookOrderShare = 0.0;
            return;
        }

        // Đơn có ebook và đơn có sách vật lý (nhận diện theo format)
        $orderIdsWithEbook = DB::table('order_items as oi')
            ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
            ->whereIn('oi.order_id', $orderIds)
            ->where(function ($q) {
                $q->whereNotNull('bf.file_url')
                  ->orWhereRaw("LOWER(bf.format_name) LIKE '%ebook%'");
            })
            ->distinct()
            ->pluck('oi.order_id')
            ->map(fn($v) => (string) $v)
            ->values();

        $orderIdsWithPhysical = DB::table('order_items as oi')
            ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
            ->whereIn('oi.order_id', $orderIds)
            ->where(function ($q) {
                $q->whereNull('bf.file_url')
                  ->where(function ($q2) {
                      $q2->whereNull('bf.format_name')
                         ->orWhereRaw("LOWER(bf.format_name) NOT LIKE '%ebook%'");
                  });
            })
            ->distinct()
            ->pluck('oi.order_id')
            ->map(fn($v) => (string) $v)
            ->values();

        $totalOrders = $orderIds->count();
        $ebookOrders = $orderIdsWithEbook->count();
        $this->ebookOrderShare = $totalOrders > 0 ? round($ebookOrders * 100.0 / $totalOrders, 2) : 0.0;

        // Đơn có yêu cầu hoàn tiền (bất kỳ trạng thái) trong cùng tập orders
        $refundOrderIds = DB::table('refund_requests')
            ->whereIn('order_id', $orderIds)
            ->distinct()
            ->pluck('order_id')
            ->map(fn($v) => (string) $v)
            ->values();

        // Nếu không có đơn refund, cả 2 tỉ lệ = 0
        if ($refundOrderIds->isEmpty()) {
            $this->refundRateEbook = 0.0;
            $this->refundRatePhysical = 0.0;
            return;
        }

        // Theo hướng A: tỉ lệ theo số đơn trong từng nhóm (đếm trực tiếp bằng SQL)
        $ebookRefundCount = DB::table('order_items as oi')
            ->whereIn('oi.order_id', $refundOrderIds)
            ->whereIn('oi.order_id', $orderIds)
            ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
            ->where(function ($q) {
                $q->whereNotNull('bf.file_url')
                  ->orWhereRaw("LOWER(bf.format_name) LIKE '%ebook%'");
            })
            ->distinct('oi.order_id')
            ->count('oi.order_id');

        $physicalRefundCount = DB::table('order_items as oi')
            ->whereIn('oi.order_id', $refundOrderIds)
            ->whereIn('oi.order_id', $orderIds)
            ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
            ->where(function ($q) {
                $q->whereNull('bf.file_url')
                  ->where(function ($q2) {
                      $q2->whereNull('bf.format_name')
                         ->orWhereRaw("LOWER(bf.format_name) NOT LIKE '%ebook%'");
                  });
            })
            ->distinct('oi.order_id')
            ->count('oi.order_id');

        $ebookDenom = max(1, $orderIdsWithEbook->count());
        $physicalDenom = max(1, $orderIdsWithPhysical->count());

        $this->refundRateEbook = round(min(100, ($ebookRefundCount * 100.0 / $ebookDenom)), 2);
        $this->refundRatePhysical = round(min(100, ($physicalRefundCount * 100.0 / $physicalDenom)), 2);
    }
}
