<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Book;

echo "=== CẬP NHẬT NGÀY PHÁT HÀNH SÁCH ===\n\n";

$book = Book::where('title', 'Sách Test Đặt Trước')->first();

if ($book) {
    echo "Tìm thấy sách: {$book->title}\n";
    echo "Ngày phát hành hiện tại: {$book->release_date}\n";
    
    // Cập nhật ngày phát hành về quá khứ
    $book->update(['release_date' => now()->subDays(1)]);
    
    $book->refresh();
    echo "Ngày phát hành mới: {$book->release_date}\n";
    echo "Đã phát hành: " . ($book->isReleased() ? 'Có' : 'Không') . "\n";
    echo "✅ Cập nhật thành công!\n";
} else {
    echo "❌ Không tìm thấy sách 'Sách Test Đặt Trước'\n";
}

echo "\n=== HOÀN THÀNH ===\n";