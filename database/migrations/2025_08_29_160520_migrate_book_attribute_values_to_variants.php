<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Di chuyển dữ liệu từ book_attribute_values sang book_variants + pivot
        // Giả định hệ thống cũ quản lý tồn kho/sku theo MỘT giá trị thuộc tính/biến thể
        // => Mỗi bản ghi book_attribute_values sẽ trở thành 1 bản ghi book_variants
        DB::transaction(function () {
            $legacyRows = DB::table('book_attribute_values')
                ->select('id', 'book_id', 'attribute_value_id', 'extra_price', 'stock', 'sku')
                ->get();

            foreach ($legacyRows as $row) {
                $variantId = (string) Str::uuid();

                DB::table('book_variants')->insert([
                    'id' => $variantId,
                    'book_id' => $row->book_id,
                    'sku' => $row->sku,
                    'extra_price' => $row->extra_price ?? 0,
                    'stock' => $row->stock ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('book_variant_attribute_values')->insert([
                    'id' => (string) Str::uuid(),
                    'book_variant_id' => $variantId,
                    'attribute_value_id' => $row->attribute_value_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        // CẢNH BÁO: Down sẽ xóa toàn bộ dữ liệu biến thể mới (có thể làm mất dữ liệu nếu đã phát sinh thêm)
        DB::transaction(function () {
            DB::table('book_variant_attribute_values')->delete();
            DB::table('book_variants')->delete();
        });
    }
};
