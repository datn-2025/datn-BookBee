<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thay đổi column status từ enum sang varchar
        Schema::table('preorders', function (Blueprint $table) {
            // Xóa index trên column status nếu có
            $table->dropIndex(['status']);
        });
        
        // Sử dụng raw SQL để thay đổi column type
        DB::statement('ALTER TABLE preorders MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT "pending"');
        
        // Thêm lại index
        Schema::table('preorders', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về enum
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        
        DB::statement('ALTER TABLE preorders MODIFY COLUMN status ENUM("pending","confirmed","processing","shipped","delivered","cancelled") NOT NULL DEFAULT "pending"');
        
        Schema::table('preorders', function (Blueprint $table) {
            $table->index('status');
        });
    }
};
