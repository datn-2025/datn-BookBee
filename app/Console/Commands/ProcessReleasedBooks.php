<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Preorder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\BookReleaseNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ProcessReleasedBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:process-released 
                          {--dry-run : Show what would be processed without making changes}
                          {--book= : Process specific book by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process books that have been released today - convert preorders to regular orders and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting to process released books...');
        
        $isDryRun = $this->option('dry-run');
        $specificBookId = $this->option('book');
        
        // Get books that are released today or overdue
        $query = Book::query()
            ->where('pre_order', true)
            ->where('release_date', '<=', now()->toDateString())
            ->whereHas('preorders', function($q) {
                $q->whereIn('status', ['pending', 'confirmed']);
            })
            ->with(['preorders' => function($q) {
                $q->whereIn('status', ['pending', 'confirmed'])
                  ->with(['user', 'bookFormat']);
            }]);
            
        if ($specificBookId) {
            $query->where('id', $specificBookId);
        }
        
        $releasedBooks = $query->get();
        
        if ($releasedBooks->isEmpty()) {
            $this->info('📚 No books to process today.');
            return;
        }
        
        $this->info("📖 Found {$releasedBooks->count()} books to process:");
        
        foreach ($releasedBooks as $book) {
            $this->processBook($book, $isDryRun);
        }
        
        $this->info('✅ Processing completed!');
    }
    
    private function processBook(Book $book, bool $isDryRun = false)
    {
        $this->line("📚 Processing: {$book->title}");
        $this->line("   Release Date: {$book->release_date->format('Y-m-d')}");
        
        $preorders = $book->preorders()
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['user', 'bookFormat'])
            ->get();
            
        $this->line("   Found {$preorders->count()} preorders to process");
        
        if ($isDryRun) {
            $this->warn("   [DRY RUN] Would process {$preorders->count()} preorders");
            return;
        }
        
        DB::transaction(function () use ($book, $preorders) {
            $processedCount = 0;
            $failedCount = 0;
            
            foreach ($preorders as $preorder) {
                try {
                    $this->processPreorder($preorder);
                    $processedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Failed to process preorder {$preorder->id}: " . $e->getMessage());
                    $this->error("   ❌ Failed to process preorder {$preorder->id}: " . $e->getMessage());
                }
            }
            
            // Disable preorder for this book
            $book->update(['pre_order' => false]);
            
            // Send release notification email
            if ($processedCount > 0) {
                try {
                    $this->sendReleaseNotifications($book, $preorders);
                    $this->info("   📧 Release notifications sent");
                } catch (\Exception $e) {
                    Log::error("Failed to send release notifications for book {$book->id}: " . $e->getMessage());
                    $this->warn("   ⚠️  Failed to send release notifications");
                }
            }
            
            $this->info("   ✅ Processed: {$processedCount} success, {$failedCount} failed");
        });
    }
    
    private function processPreorder(Preorder $preorder)
    {
        // Create order from preorder
        $order = Order::create([
            'user_id' => $preorder->user_id,
            'order_code' => $this->generateOrderCode(),
            'status' => 'Chờ Thanh Toán',
            'payment_status_id' => 1, // Chờ thanh toán
            'order_status_id' => 1, // Chờ xử lý
            'total_amount' => $preorder->total_amount,
            'recipient_name' => $preorder->customer_name,
            'recipient_email' => $preorder->email,
            'recipient_phone' => $preorder->phone,
            'delivery_method' => $preorder->isEbook() ? 'ebook' : 'delivery',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Add address if physical book
        if (!$preorder->isEbook()) {
            // Create address record or use existing one
            $address = $preorder->user->addresses()->create([
                'recipient_name' => $preorder->customer_name,
                'phone' => $preorder->phone,
                'address_detail' => $preorder->address,
                'city' => $preorder->province_name,
                'district' => $preorder->district_name,
                'ward' => $preorder->ward_name,
                'province_id' => $preorder->province_code,
                'district_id' => $preorder->district_code,
                'ward_code' => $preorder->ward_code,
                'is_default' => false,
            ]);
            
            $order->update(['address_id' => $address->id]);
        }
        
        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'book_id' => $preorder->book_id,
            'book_format_id' => $preorder->book_format_id,
            'quantity' => $preorder->quantity,
            'price' => $preorder->unit_price,
            'total' => $preorder->total_amount,
        ]);
        
        // Update preorder status
        $preorder->update([
            'status' => 'processing',
            'notes' => 'Đã chuyển thành đơn hàng #' . $order->order_code
        ]);
        
        Log::info("Converted preorder {$preorder->id} to order {$order->id}");
    }
    
    private function generateOrderCode(): string
    {
        do {
            $code = 'ORD' . now()->format('ymd') . rand(1000, 9999);
        } while (Order::where('order_code', $code)->exists());
        
        return $code;
    }
    
    private function sendReleaseNotifications(Book $book, $preorders)
    {
        // Group preorders by user to send one email per user
        $preordersByUser = $preorders->groupBy('user_id');
        
        foreach ($preordersByUser as $userId => $userPreorders) {
            $user = $userPreorders->first()->user;
            
            try {
                Mail::to($user->email)->send(
                    new BookReleaseNotification($book, $userPreorders)
                );
            } catch (\Exception $e) {
                Log::error("Failed to send release notification to user {$userId}: " . $e->getMessage());
            }
        }
    }
}
