<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Book;

class InventoryStatusReport extends Component
{
    public $books;

    public function mount()
{
    // Lấy 5 sách có tổng tồn kho ít nhất từ các book formats
    $this->books = Book::with('formats')
        ->select('id', 'title', 'cover_image', 'status')
        ->withSum('formats as total_stock', 'stock')
        ->orderBy('total_stock') // Sắp xếp tăng dần
        ->limit(6)
        ->get();
}

    public function render()
    {
        return view('livewire.inventory-status-report');
    }
}
