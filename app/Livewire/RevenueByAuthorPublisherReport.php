<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Author;
use App\Models\Brand;

class RevenueByAuthorPublisherReport extends Component
{
    public $authorRevenue = [];
    public $publisherRevenue = [];

    public function mount()
    {
        // Lấy tất cả tác giả với books và orderItems lồng nhau
        $authors = Author::with(['books.orderItems'])->get();

        $this->authorRevenue = $authors->map(function ($author) {
            $revenue = 0;
            foreach ($author->books as $book) {
                foreach ($book->orderItems as $item) {
                    $revenue += $item->price * $item->quantity;
                }
            }
            return [
                'name' => $author->name,
                'revenue' => $revenue
            ];
        })->sortByDesc('revenue')->take(5)->values();

        // Lấy tất cả brand với books và orderItems
        $brands = Brand::with(['books.orderItems'])->get();

        $this->publisherRevenue = $brands->map(function ($brand) {
            $revenue = 0;
            foreach ($brand->books as $book) {
                foreach ($book->orderItems as $item) {
                    $revenue += $item->price * $item->quantity;
                }
            }
            return [
                'name' => $brand->name,
                'revenue' => $revenue
            ];
        })->sortByDesc('revenue')->take(5)->values();
    }

    public function render()
    {
        return view('livewire.revenue-by-author-publisher-report');
    }
}
