<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use Carbon\Carbon;

class UpdateBookStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cập nhật trạng thái sách dựa trên ngày xuất bản';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting book status update based on release dates...');

        // Get all books that have either release_date or publication_date
        $books = Book::whereNotNull('release_date')
                    ->orWhereNotNull('publication_date')
                    ->get();

        $updatedCount = 0;
        $today = now()->startOfDay();

        foreach ($books as $book) {
            $oldStatus = $book->status;
            
            // Determine status based on release_date (priority) or publication_date (fallback)
            $targetDate = $book->release_date ?: $book->publication_date;
            
            if ($targetDate) {
                $targetDate = $targetDate->startOfDay();
                
                if ($targetDate->gt($today)) {
                    $newStatus = 'Sắp ra mắt';
                } else {
                    $newStatus = 'Còn hàng';
                }

                if ($oldStatus !== $newStatus) {
                    $book->status = $newStatus;
                    $book->save();
                    $updatedCount++;
                    
                    $this->line("Updated book '{$book->title}': {$oldStatus} → {$newStatus}");
                }
            }
        }

        $this->info("Book status update completed. Updated {$updatedCount} books.");

        return 0;
    }
}
