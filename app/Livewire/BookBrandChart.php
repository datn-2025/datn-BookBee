<?php

namespace App\Livewire;

use App\Models\Brand;
use Livewire\Component;

class BookBrandChart extends Component
{
    public $brandStats = [];

    public function mount()
    {
        $this->brandStats = Brand::withCount('books')->get();
    }
    public function render()
    {
        return view('livewire.book-brand-chart');
    }
}
