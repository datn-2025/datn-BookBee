<?php

namespace App\Livewire;

use App\Models\Book;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TopSellingBooksReport extends Component
{
    public $books = [];
    public $favoriteBooks = [];

    public function mount()
    {
        // Lấy top sách bán chạy nhất
        $this->books = Book::select('id', 'title', 'cover_image')
            ->withSum('orderItems as total_sold', 'quantity')
            ->has('orderItems')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Lấy top sách được yêu thích nhất dựa trên số lượng trong wishlist
        $this->favoriteBooks = Book::select('books.id', 'books.title', 'books.cover_image')
            ->join('wishlists', 'books.id', '=', 'wishlists.book_id')
            ->selectRaw('COUNT(wishlists.id) as wishlist_count')
            ->groupBy('books.id', 'books.title', 'books.cover_image')
            ->orderByDesc('wishlist_count')
            ->limit(5)
            ->get()
            ->map(function($book) {
                $book->favorites_count = $book->wishlist_count;
                return $book;
            });
    }

    public function render()
    {
        return view('livewire.top-selling-books-report');
    }
}
