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
        // Kiểm tra và tạo index cho bảng books
        $this->createIndexIfNotExists('books', [
            ['columns' => ['title'], 'name' => 'books_title_index'],
            ['columns' => ['status'], 'name' => 'books_status_index'],
            ['columns' => ['category_id'], 'name' => 'books_category_index'],
            ['columns' => ['brand_id'], 'name' => 'books_brand_index'],
            ['columns' => ['created_at'], 'name' => 'books_created_at_index'],
        ]);

        // Kiểm tra và tạo index cho bảng authors
        $this->createIndexIfNotExists('authors', [
            ['columns' => ['name'], 'name' => 'authors_name_index'],
        ]);

        // Kiểm tra và tạo index cho bảng brands
        $this->createIndexIfNotExists('brands', [
            ['columns' => ['name'], 'name' => 'brands_name_index'],
        ]);

        // Kiểm tra và tạo index cho bảng categories
        $this->createIndexIfNotExists('categories', [
            ['columns' => ['name'], 'name' => 'categories_name_index'],
        ]);

        // Kiểm tra và tạo index cho bảng book_formats
        $this->createIndexIfNotExists('book_formats', [
            ['columns' => ['book_id', 'price'], 'name' => 'book_formats_book_price_index'],
        ]);

        // Kiểm tra và tạo index cho bảng reviews
        $this->createIndexIfNotExists('reviews', [
            ['columns' => ['book_id', 'rating'], 'name' => 'reviews_book_rating_index'],
        ]);

        // Tạo fulltext index cho tìm kiếm text nếu sử dụng MySQL
        if (DB::getDriverName() === 'mysql') {
            // Kiểm tra xem fulltext index đã tồn tại chưa
            $exists = DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.statistics 
                WHERE table_schema = DATABASE() 
                AND table_name = 'books' 
                AND index_name = 'books_fulltext_index'
            ");
            
            if ($exists[0]->count == 0) {
                DB::statement('CREATE FULLTEXT INDEX books_fulltext_index ON books(title, description)');
            }
        }
    }

    /**
     * Tạo index nếu chưa tồn tại
     */
    private function createIndexIfNotExists(string $table, array $indexes): void
    {
        foreach ($indexes as $indexData) {
            $indexName = $indexData['name'];
            $columns = $indexData['columns'];
            
            // Kiểm tra xem index đã tồn tại chưa
            $exists = DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.statistics 
                WHERE table_schema = DATABASE() 
                AND table_name = ? 
                AND index_name = ?
            ", [$table, $indexName]);
            
            if ($exists[0]->count == 0) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName) {
                    $tableBlueprint->index($columns, $indexName);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa index cho bảng books
        $this->dropIndexIfExists('books', [
            'books_title_index',
            'books_status_index',
            'books_category_index',
            'books_brand_index',
            'books_created_at_index'
        ]);

        // Xóa index cho bảng authors
        $this->dropIndexIfExists('authors', ['authors_name_index']);

        // Xóa index cho bảng brands
        $this->dropIndexIfExists('brands', ['brands_name_index']);

        // Xóa index cho bảng categories
        $this->dropIndexIfExists('categories', ['categories_name_index']);

        // Xóa index cho bảng book_formats
        $this->dropIndexIfExists('book_formats', ['book_formats_book_price_index']);

        // Xóa index cho bảng reviews
        $this->dropIndexIfExists('reviews', ['reviews_book_rating_index']);

        // Xóa fulltext index
        if (DB::getDriverName() === 'mysql') {
            $exists = DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.statistics 
                WHERE table_schema = DATABASE() 
                AND table_name = 'books' 
                AND index_name = 'books_fulltext_index'
            ");
            
            if ($exists[0]->count > 0) {
                DB::statement('DROP INDEX books_fulltext_index ON books');
            }
        }
    }

    /**
     * Xóa index nếu tồn tại
     */
    private function dropIndexIfExists(string $table, array $indexes): void
    {
        foreach ($indexes as $indexName) {
            // Kiểm tra xem index có tồn tại không
            $exists = DB::select("
                SELECT COUNT(*) as count 
                FROM information_schema.statistics 
                WHERE table_schema = DATABASE() 
                AND table_name = ? 
                AND index_name = ?
            ", [$table, $indexName]);
            
            if ($exists[0]->count > 0) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($indexName) {
                    $tableBlueprint->dropIndex($indexName);
                });
            }
        }
    }
};
