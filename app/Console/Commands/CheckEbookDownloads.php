<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EbookDownload;
use App\Models\Order;
use App\Models\User;

class CheckEbookDownloads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-ebook-downloads {--order-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra dữ liệu ebook downloads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== KIỂM TRA EBOOK DOWNLOADS ===');
        
        // Hiển thị tất cả ebook downloads
        $downloads = EbookDownload::with(['user', 'order', 'bookFormat'])->get();
        $this->info("Tổng số lượt tải: {$downloads->count()}");
        
        if ($downloads->count() > 0) {
            $this->table(
                ['ID', 'User ID', 'Order ID', 'Book Format ID', 'Downloaded At', 'IP Address'],
                $downloads->map(function($download) {
                    return [
                        $download->id,
                        $download->user_id,
                        $download->order_id,
                        $download->book_format_id,
                        $download->downloaded_at?->format('Y-m-d H:i:s'),
                        $download->ip_address
                    ];
                })
            );
        }
        
        // Kiểm tra order cụ thể nếu có
        if ($orderId = $this->option('order-id')) {
            $this->info("\n=== KIỂM TRA ORDER #{$orderId} ===");
            
            $order = Order::find($orderId);
            if (!$order) {
                $this->error("Order #{$orderId} không tồn tại!");
                return;
            }
            
            $this->info("User ID: {$order->user_id}");
            $this->info("Order Status: {$order->orderStatus->name}");
            
            // Kiểm tra ebook items trong order
            $ebookItems = $order->orderItems->filter(function($item) {
                return $item->bookFormat && $item->bookFormat->format_name === 'Ebook';
            });
            
            $this->info("Số ebook trong order: {$ebookItems->count()}");
            
            foreach ($ebookItems as $item) {
                $this->info("\n--- Ebook: {$item->book->title} ---");
                $this->info("Book Format ID: {$item->bookFormat->id}");
                $this->info("DRM Enabled: " . ($item->bookFormat->drm_enabled ? 'Yes' : 'No'));
                $this->info("Max Downloads: {$item->bookFormat->max_downloads}");
                
                // Đếm downloads cho ebook này
                $downloadCount = EbookDownload::where('user_id', $order->user_id)
                    ->where('order_id', $order->id)
                    ->where('book_format_id', $item->bookFormat->id)
                    ->count();
                    
                $this->info("Download Count: {$downloadCount}");
                
                // Hiển thị chi tiết downloads
                $downloads = EbookDownload::where('user_id', $order->user_id)
                    ->where('order_id', $order->id)
                    ->where('book_format_id', $item->bookFormat->id)
                    ->get();
                    
                foreach ($downloads as $download) {
                    $this->info("  - Downloaded at: {$download->downloaded_at} from {$download->ip_address}");
                }
            }
        }
        
        $this->info("\n=== HOÀN THÀNH ===");
    }
}
