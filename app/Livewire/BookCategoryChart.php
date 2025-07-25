<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class BookCategoryChart extends Component
{
    public $categoryStats = [];

    public function mount()
    {
        $topN = 6;
        $sorted = Category::withCount('books')->get()->sortByDesc('books_count');

        $topCategories = $sorted->take($topN)->map(function ($cat) {
            return [
                'name' => $cat->name,
                'books_count' => $cat->books_count
            ];
        });

        $otherTotal = $sorted->skip($topN)->sum('books_count');

        $finalStats = $topCategories->values();

        if ($otherTotal > 0) {
            $finalStats->push([
                'name' => 'Khác',
                'books_count' => $otherTotal
            ]);
        }

        $this->categoryStats = $finalStats;
    }

    public function render()
    {
        return view('livewire.book-category-chart');
    }
}
