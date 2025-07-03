<?php

namespace App\Livewire;

use App\Models\Brand;
use Livewire\Component;

class BookBrandChart extends Component
{
    public $brandStats = [];

    public function mount()
    {
        $rawStats = Brand::withCount('books')->get();

        $topN = 6; // Hiển thị Top 6 thương hiệu
        $sorted = $rawStats->sortByDesc('books_count');

        $topBrands = $sorted->take($topN)->map(function ($brand) {
            return [
                'name' => $brand->name,
                'books_count' => $brand->books_count
            ];
        });

        $otherTotal = $sorted->skip($topN)->sum('books_count');

        $finalStats = $topBrands->values();

        if ($otherTotal > 0) {
            $finalStats->push([
                'name' => 'Khác',
                'books_count' => $otherTotal
            ]);
        }

        $this->brandStats = $finalStats;
    }

    public function render()
    {
        return view('livewire.book-brand-chart');
    }
}
