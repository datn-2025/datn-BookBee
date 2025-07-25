<?php

namespace App\Livewire;

use App\Models\Author;
use Livewire\Component;

class BookAuthorChart extends Component
{
    public $authorStats = [];

    public function mount()
    {
        $rawStats = Author::withCount('books')->get();

        $topN = 6; // Hiển thị Top 6 tác giả
        $sorted = $rawStats->sortByDesc('books_count');

        $topAuthors = $sorted->take($topN)->map(function ($author) {
            return [
                'name' => $author->name,
                'books_count' => $author->books_count
            ];
        });

        $otherTotal = $sorted->skip($topN)->sum('books_count');

        $finalStats = $topAuthors->values();

        if ($otherTotal > 0) {
            $finalStats->push([
                'name' => 'Khác',
                'books_count' => $otherTotal
            ]);
        }

        $this->authorStats = $finalStats;
    }

    public function render()
    {
        return view('livewire.book-author-chart');
    }
}
