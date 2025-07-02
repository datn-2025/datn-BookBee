<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class TotalBooksSoldChart extends Component
{
    public $chartData;
    public $totalBooksSold;

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        $booksSoldByCategory = OrderItem::select(
                'books.category_id',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                'categories.name as category_name'
            )
            ->join('books', 'order_items.book_id', '=', 'books.id')
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->groupBy('books.category_id', 'categories.name')
            ->get();

        $this->chartData = $booksSoldByCategory->pluck('total_quantity', 'category_name')->toArray();
        $this->totalBooksSold = array_sum($this->chartData);
    }

    public function render()
    {
        return view('livewire.total-books-sold-chart');
    }
}
