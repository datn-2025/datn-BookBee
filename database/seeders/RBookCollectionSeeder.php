<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookCollection;
use App\Models\Collection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RBookCollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Book::all();
        $collections = Collection::all();

        if ($books->isEmpty() || $collections->isEmpty()) {
            $this->command->error('Vui lòng seed trước bảng Books và Collections!');
            return;
        }

        // Mỗi collection sẽ chứa ngẫu nhiên 3 sách
        foreach ($collections as $collection) {
            $selectedBooks = $books->random(min(3, $books->count()));

            foreach ($selectedBooks as $index => $book) {
                BookCollection::create([
                    'id' => (string) Str::uuid(),
                    'book_id' => $book->id,
                    'collection_id' => $collection->id,
                    'order_column' => $index + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    
}
