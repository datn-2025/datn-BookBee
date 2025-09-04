<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            if (!Schema::hasColumn('preorders', 'variant_id')) {
                $table->uuid('variant_id')->nullable()->after('book_format_id');
            }
            if (!Schema::hasColumn('preorders', 'selected_variant_value_ids')) {
                $table->json('selected_variant_value_ids')->nullable()->after('selected_attributes');
            }
            if (!Schema::hasColumn('preorders', 'variant_label')) {
                $table->string('variant_label')->nullable()->after('selected_variant_value_ids');
            }
            if (!Schema::hasColumn('preorders', 'variant_extra_price')) {
                $table->decimal('variant_extra_price', 12, 2)->nullable()->after('variant_label');
            }
            if (!Schema::hasColumn('preorders', 'variant_sku')) {
                $table->string('variant_sku')->nullable()->after('variant_extra_price');
            }

            // Index for faster lookup
            $table->index(['book_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            if (Schema::hasColumn('preorders', 'variant_sku')) {
                $table->dropColumn('variant_sku');
            }
            if (Schema::hasColumn('preorders', 'variant_extra_price')) {
                $table->dropColumn('variant_extra_price');
            }
            if (Schema::hasColumn('preorders', 'variant_label')) {
                $table->dropColumn('variant_label');
            }
            if (Schema::hasColumn('preorders', 'selected_variant_value_ids')) {
                $table->dropColumn('selected_variant_value_ids');
            }
            if (Schema::hasColumn('preorders', 'variant_id')) {
                $table->dropColumn('variant_id');
            }
        });
    }
};
