<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RInvoiceItemSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = Invoice::all();
        
        if ($invoices->isEmpty()) {
            $this->command->error('Vui lòng seed đầy đủ invoices trước khi seed invoice_items!');
            return;
        }

        foreach ($invoices as $invoice) {
            // Lấy ngẫu nhiên từ 1-3 sách từ database
            $randomBooks = Book::inRandomOrder()
                ->limit(rand(1, 3))
                ->get();

            if ($randomBooks->isEmpty()) {
                $this->command->error('Không tìm thấy sách nào để tạo invoice items!');
                continue;
            }

            foreach ($randomBooks as $book) {
                // Kiểm tra nếu sách này đã có trong invoice tránh trùng (theo unique constraint)
                if (InvoiceItem::where('invoice_id', $invoice->id)
                    ->where('book_id', $book->id)
                    ->exists()) {
                    continue;
                }

                InvoiceItem::create([
                    'id' => (string) Str::uuid(),
                    'invoice_id' => $invoice->id,
                    'book_id' => $book->id,
                    'quantity' => rand(1, 3),
                    'price' => $book->price ?? rand(50000, 200000), // Lấy giá từ book, nếu null thì giá mẫu
                ]);
            }
        }
    }
}
