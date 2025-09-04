<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'variant_id')) {
                $table->uuid('variant_id')->nullable()->after('book_format_id');
            }
            if (!Schema::hasColumn('order_items', 'variant_label')) {
                $table->string('variant_label')->nullable()->after('variant_id');
            }
            if (!Schema::hasColumn('order_items', 'variant_extra_price')) {
                $table->decimal('variant_extra_price', 12, 2)->nullable()->after('variant_label');
            }
            if (!Schema::hasColumn('order_items', 'variant_sku')) {
                $table->string('variant_sku')->nullable()->after('variant_extra_price');
            }

            $table->index(['book_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'variant_sku')) {
                $table->dropColumn('variant_sku');
            }
            if (Schema::hasColumn('order_items', 'variant_extra_price')) {
                $table->dropColumn('variant_extra_price');
            }
            if (Schema::hasColumn('order_items', 'variant_label')) {
                $table->dropColumn('variant_label');
            }
            if (Schema::hasColumn('order_items', 'variant_id')) {
                $table->dropIndex(['book_id', 'variant_id']);
                $table->dropColumn('variant_id');
            }
        });
    }
};
