<?php

namespace App\Livewire;

use App\Models\Book;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TopSellingBooksReport extends Component
{
    public $books = [];

    public function mount()
    {
       $this->books = Book::select('id', 'title', 'cover_image')
            ->withSum('orderItems as total_sold', 'quantity')
            ->has('orderItems') // <-- Chỉ lấy sách đã từng được bán
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.top-selling-books-report');
    }
}
