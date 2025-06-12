<?php

namespace App\Livewire;

use App\Models\Author;
use Livewire\Component;

class BookAuthorChart extends Component
{
    public $authorStats = [];

    public function mount()
    {
        $this->authorStats = Author::withCount('books')->get();
    }
    public function render()
    {
        return view('livewire.book-author-chart');
    }
}
