<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE refund_requests MODIFY reason TEXT");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE refund_requests 
            MODIFY reason ENUM('wrong_item', 'quality_issue', 'shipping_delay') 
            CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
};
