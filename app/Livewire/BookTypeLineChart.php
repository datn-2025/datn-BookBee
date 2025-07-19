<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookTypeLineChart extends Component
{
    public $labels = [];
    public $datasets = [];

    public function mount()
    {
        // Lấy id trạng thái "Thành công"
        $successStatus = \App\Models\OrderStatus::where('name', 'Thành công')->value('id');

        // Lấy tất cả order_items thuộc các đơn hàng trạng thái Thành công, join đủ để lấy book_id, format, created_at
        $orderItems = \App\Models\OrderItem::query()
            ->selectRaw('book_formats.format_name, DATE_FORMAT(orders.created_at, "%Y-%m") as month, SUM(order_items.quantity) as total')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('books', 'order_items.book_id', '=', 'books.id')
            ->join('book_formats', function($join) {
                $join->on('book_formats.book_id', '=', 'books.id');
            })
            ->where('orders.order_status_id', $successStatus)
            ->groupBy('month', 'book_formats.format_name')
            ->orderBy('month')
            ->get();

        // Lấy danh sách tháng và format
        $months = $orderItems->pluck('month')->unique()->values()->toArray();
        $formats = $orderItems->pluck('format_name')->unique()->values()->toArray();
        $this->labels = $months;

        // Chuẩn bị dữ liệu cho chart
        $colors = [
            '#3cb44b', '#0082c8', '#e6194b', '#ffe119', '#f58231',
            '#911eb4', '#46f0f0', '#f032e6', '#d2f53c', '#fabebe',
            '#008080', '#e6beff', '#aa6e28', '#fffac8', '#800000'
        ];
        $datasets = [];
        foreach ($formats as $i => $format) {
            $data = [];
            foreach ($months as $month) {
                $item = $orderItems->first(function($row) use ($format, $month) {
                    return $row->format_name === $format && $row->month === $month;
                });
                $data[] = $item ? (int)$item->total : 0;
            }
            $datasets[] = [
                'label' => $format,
                'data' => $data,
                'borderColor' => $colors[$i % count($colors)],
                'backgroundColor' => $colors[$i % count($colors)],
                'fill' => false,
                'tension' => 0.2,
                'pointRadius' => 4,
                'pointHoverRadius' => 6
            ];
        }
        $this->datasets = $datasets;

        $datasets = [];
        foreach ($formats as $i => $format) {
            // Lấy số lượng bán theo tháng cho từng format
            $data = [];
            foreach ($months as $month) {
                $count = DB::table('order_items')
                    ->join('books', 'order_items.book_id', '=', 'books.id')
                    ->join('book_formats', function($join) {
                        $join->on('book_formats.book_id', '=', 'books.id');
                    })
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.order_status_id', $successStatus)
                    ->whereRaw('DATE_FORMAT(orders.created_at, "%Y-%m") = ?', [$month])
                    ->where('book_formats.format_name', $format)
                    ->sum('order_items.quantity');
                $data[] = (int) $count;
            }
            // Thêm màu sắc và style cho từng format
            $colors = [
                '#3cb44b', '#0082c8', '#e6194b', '#ffe119', '#f58231',
                '#911eb4', '#46f0f0', '#f032e6', '#d2f53c', '#fabebe',
                '#008080', '#e6beff', '#aa6e28', '#fffac8', '#800000'
            ];
            $datasets[] = [
                'label' => $format,
                'data' => $data,
                'borderColor' => $colors[$i % count($colors)],
                'backgroundColor' => $colors[$i % count($colors)],
                'fill' => false,
                'tension' => 0.2,
                'pointRadius' => 4,
                'pointHoverRadius' => 6
            ];
        }
        $this->datasets = $datasets;
    }

    public function render()
    {
        return view('livewire.book-type-line-chart');
    }
}
