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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VoucherSeeder extends Seeder
{
    public function run()
    {
        // Tạo một số voucher hoạt động cho mục đích test
        Voucher::factory()->active()->create([
            'code' => 'TESTACTIVE10',
            'discount_percent' => 10,
            'min_order_value' => 100000,
            'valid_to' => now()->addMonths(6),
        ]);

        Voucher::factory()->active()->create([
            'code' => 'TESTACTIVE20',
            'discount_percent' => 20,
            'min_order_value' => 200000,
            'valid_to' => now()->addMonths(6),
        ]);
    }
  private function generateUniqueCode($prefix)
    {
        return $prefix . strtoupper(Str::random(8));
    }
}
