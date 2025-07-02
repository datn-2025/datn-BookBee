<?php

namespace Database\Seeders;

use App\Models\Voucher;
use App\Models\VoucherCondition;
use App\Models\Book;
use App\Models\Author;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RVoucherSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::first();
        $author = Author::first();
        $brand = Brand::first();
        $book = Book::first();

        if (!$category || !$author || !$brand || !$book) {
            $this->command->error('Vui lòng seed dữ liệu Category, Author, Brand, Book trước!');
            return;
        }

        // Voucher áp dụng cho tất cả sản phẩm
        $voucherAll = Voucher::create([
            'id' => (string) Str::uuid(),
            'code' => 'ALL10',
            'description' => 'Giảm 10% cho tất cả sản phẩm',
            'discount_percent' => 10,
            'max_discount' => 50000,
            'min_order_value' => 100000,
            'quantity' => 100,
            'valid_from' => now(),
            'valid_to' => now()->addMonths(3),
            'status' => 'active'
        ]);

        VoucherCondition::create([
            'voucher_id' => $voucherAll->id,
            'type' => 'all',
            'condition_id' => null
        ]);

        // Voucher áp dụng cho 1 danh mục cụ thể
        $voucherCategory = Voucher::create([
            'id' => (string) Str::uuid(),
            'code' => 'CAT15',
            'description' => 'Giảm 15% cho danh mục ' . $category->name,
            'discount_percent' => 15,
            'max_discount' => 70000,
            'min_order_value' => 150000,
            'quantity' => 50,
            'valid_from' => now(),
            'valid_to' => now()->addMonths(2),
            'status' => 'active'
        ]);

        VoucherCondition::create([
            'voucher_id' => $voucherCategory->id,
            'type' => 'category',
            'condition_id' => $category->id
        ]);

        // Voucher áp dụng cho tác giả cụ thể
        $voucherAuthor = Voucher::create([
            'id' => (string) Str::uuid(),
            'code' => 'AUTH20',
            'description' => 'Giảm 20% cho sách của tác giả ' . $author->name,
            'discount_percent' => 20,
            'max_discount' => 80000,
            'min_order_value' => 200000,
            'quantity' => 30,
            'valid_from' => now(),
            'valid_to' => now()->addMonth(),
            'status' => 'active'
        ]);

        VoucherCondition::create([
            'voucher_id' => $voucherAuthor->id,
            'type' => 'author',
            'condition_id' => $author->id
        ]);

        // Voucher áp dụng cho thương hiệu cụ thể
        $voucherBrand = Voucher::create([
            'id' => (string) Str::uuid(),
            'code' => 'BRAND5',
            'description' => 'Giảm 5% cho sách của thương hiệu ' . $brand->name,
            'discount_percent' => 5,
            'max_discount' => 30000,
            'min_order_value' => 50000,
            'quantity' => 100,
            'valid_from' => now(),
            'valid_to' => now()->addMonths(2),
            'status' => 'active'
        ]);

        VoucherCondition::create([
            'voucher_id' => $voucherBrand->id,
            'type' => 'brand',
            'condition_id' => $brand->id
        ]);

        // Voucher áp dụng cho sách cụ thể
        $voucherBook = Voucher::create([
            'id' => (string) Str::uuid(),
            'code' => 'BOOK25',
            'description' => 'Giảm 25% cho cuốn sách "' . $book->title . '"',
            'discount_percent' => 25,
            'max_discount' => 60000,
            'min_order_value' => 100000,
            'quantity' => 20,
            'valid_from' => now(),
            'valid_to' => now()->addMonth(),
            'status' => 'active'
        ]);

        VoucherCondition::create([
            'voucher_id' => $voucherBook->id,
            'type' => 'book',
            'condition_id' => $book->id
        ]);
    }
}
