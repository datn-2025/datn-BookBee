<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookFormat;
use App\Models\Book;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class BookFormatSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');
        $books = Book::all();

        foreach ($books as $book) {
            // Tạo format bìa mềm (100% sách đều có)
            BookFormat::create([
                'id' => (string) Str::uuid(),
                'book_id' => $book->id,
                'format_name' => 'Bìa mềm',
                'price' => $faker->numberBetween(50000, 150000),
                'discount' => $faker->optional(0.3)->numberBetween(30000, 100000),
                'stock' => $faker->numberBetween(10, 100),
                'file_url' => null,
                'sample_file_url' => null,
                'allow_sample_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 70% sách có bản bìa cứng
            if ($faker->boolean(70)) {
                BookFormat::create([
                    'id' => (string) Str::uuid(),
                    'book_id' => $book->id,
                    'format_name' => 'Bìa cứng',
                    'price' => $faker->numberBetween(150000, 300000),
                    'discount' => $faker->optional(0.2)->numberBetween(100000, 250000),
                    'stock' => $faker->numberBetween(5, 50),
                    'file_url' => null,
                    'sample_file_url' => null,
                    'allow_sample_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 50% sách có bản ebook
            if ($faker->boolean(50)) {
                BookFormat::create([
                    'id' => (string) Str::uuid(),
                    'book_id' => $book->id,
                    'format_name' => 'Ebook',
                    'price' => $faker->numberBetween(30000, 100000),
                    'discount' => $faker->optional(0.4)->numberBetween(20000, 80000),
                    'stock' => 999,
                    'file_url' => 'ebooks/sample-' . Str::random(10) . '.pdf',
                    'sample_file_url' => 'ebooks/sample-' . Str::random(10) . '.pdf',
                    'allow_sample_read' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 30% sách có bản audio book
            if ($faker->boolean(30)) {
                BookFormat::create([
                    'id' => (string) Str::uuid(),
                    'book_id' => $book->id,
                    'format_name' => 'Audio Book',
                    'price' => $faker->numberBetween(80000, 200000),
                    'discount' => $faker->optional(0.3)->numberBetween(50000, 150000),
                    'stock' => 999,
                    'file_url' => 'audiobooks/sample-' . Str::random(10) . '.mp3',
                    'sample_file_url' => 'audiobooks/sample-' . Str::random(10) . '.mp3',
                    'allow_sample_read' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
