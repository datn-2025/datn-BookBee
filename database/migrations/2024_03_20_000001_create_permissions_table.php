<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100); // Tên quyền
            $table->string('slug', 100)->unique(); // Định danh quyền
            $table->string('description')->nullable(); // Mô tả quyền
            $table->string('module', 50); // Module áp dụng (users, books, orders,...)
            $table->timestamps();
            $table->index(['slug', 'module']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
