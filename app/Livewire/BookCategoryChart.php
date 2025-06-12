<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class BookCategoryChart extends Component
{
      public $categoryStats = [];

    public function mount()
    {
        $this->categoryStats = Category::withCount('books')->get();
    }

    public function render()
    {
        return view('livewire.book-category-chart');
    }
}
