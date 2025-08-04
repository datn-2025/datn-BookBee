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
        // Cập nhật enum status để thêm giá trị 'approved' và 'pending'
        DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('visible', 'hidden', 'approved', 'pending') DEFAULT 'approved'");
        
        // Cập nhật các record hiện tại có status 'visible' thành 'approved'
        DB::table('reviews')->where('status', 'visible')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cập nhật các record có status 'approved' hoặc 'pending' về 'visible'
        DB::table('reviews')->whereIn('status', ['approved', 'pending'])->update(['status' => 'visible']);
        
        // Rollback về enum cũ
        DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('visible', 'hidden') DEFAULT 'visible'");
    }
};