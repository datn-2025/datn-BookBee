<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BookFormat;
use Illuminate\Support\Facades\DB;

class EbookDrmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Thiết lập DRM settings cho các ebook format hiện có
     */
    public function run(): void
    {
        // Cập nhật tất cả ebook formats với DRM settings mặc định
        BookFormat::where('format_name', 'Ebook')
            ->update([
                'max_downloads' => 5,
                'drm_enabled' => true,
                'download_expiry_days' => 365
            ]);

        $this->command->info('✅ Đã cập nhật DRM settings cho tất cả ebook formats');
        
        // Hiển thị thống kê
        $ebookCount = BookFormat::where('format_name', 'Ebook')->count();
        $this->command->info("📊 Tổng số ebook formats được cập nhật: {$ebookCount}");
        
        // Hiển thị một số ebook format mẫu
        $sampleEbooks = BookFormat::where('format_name', 'Ebook')
            ->with('book:id,title')
            ->limit(5)
            ->get(['id', 'book_id', 'format_name', 'max_downloads', 'drm_enabled', 'download_expiry_days']);
            
        if ($sampleEbooks->count() > 0) {
            $this->command->info('\n📚 Một số ebook đã được cập nhật DRM:');
            foreach ($sampleEbooks as $ebook) {
                $bookTitle = $ebook->book ? $ebook->book->title : 'N/A';
                $this->command->info("  - {$bookTitle} (ID: {$ebook->id}) - Max Downloads: {$ebook->max_downloads}, DRM: " . ($ebook->drm_enabled ? 'Enabled' : 'Disabled') . ", Expiry: {$ebook->download_expiry_days} days");
            }
        }
    }
}
