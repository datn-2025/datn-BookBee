<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Author;
use Illuminate\Support\Str;

class AuthorBookSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::all();
        $authors = Author::all();

        if ($books->isEmpty() || $authors->isEmpty()) {
            $this->command->error('Vui lòng chạy BookSeeder và AuthorSeeder trước!');
            return;
        }

        foreach ($books as $book) {
            // Random 1-3 tác giả cho mỗi sách
            $selectedAuthors = $authors->random(rand(1, 3));
            foreach ($selectedAuthors as $author) {
                \DB::table('author_books')->insert([
                    'id' => (string) Str::uuid(),
                    'book_id' => $book->id,
                    'author_id' => $author->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
