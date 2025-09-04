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
            if (!Schema::hasColumn('preorders', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('delivered_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('preorders', function (Blueprint $table) {
            if (Schema::hasColumn('preorders', 'converted_at')) {
                $table->dropColumn('converted_at');
            }
        });
    }
};
