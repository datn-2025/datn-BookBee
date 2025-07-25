<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Book;

class InventoryStatusReport extends Component
{
    public $lowStockBooks;
    public $outOfStockBooks;

    public function mount()
    {
        // Sách có tổng tồn kho từ 1-5
        $this->lowStockBooks = Book::with('formats')
            ->select('id', 'title', 'cover_image', 'status')
            ->withSum('formats as total_stock', 'stock')
            ->having('total_stock', '>=', 1)
            ->having('total_stock', '<=', 5)
            ->orderBy('total_stock')
            ->get();

        // Sách hết hàng (tồn kho = 0)
        $this->outOfStockBooks = Book::with('formats')
            ->select('id', 'title', 'cover_image', 'status')
            ->withSum('formats as total_stock', 'stock')
            ->having('total_stock', '=', 0)
            ->orderBy('title')
            ->get();
    }

    public function render()
    {
        return view('livewire.inventory-status-report');
    }
}
