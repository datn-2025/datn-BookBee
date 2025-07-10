<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Services\AISummaryService;
use Illuminate\Console\Command;

class GenerateBookSummary extends Command
{
    protected $signature = 'book:generate-summary {book_id?} {--all} {--regenerate}';
    protected $description = 'Generate AI summary for a book or all books';

    protected $aiSummaryService;

    public function __construct(AISummaryService $aiSummaryService)
    {
        parent::__construct();
        $this->aiSummaryService = $aiSummaryService;
    }

    public function handle()
    {
        if ($this->option('all')) {
            $this->generateForAllBooks();
        } elseif ($bookId = $this->argument('book_id')) {
            $this->generateForBook($bookId);
        } else {
            $this->error('Please provide book_id or use --all option');
            return 1;
        }

        return 0;
    }

    private function generateForBook($bookId)
    {
        try {
            $book = Book::with(['author', 'category', 'summary'])->findOrFail($bookId);
            
            $this->info("Generating summary for: {$book->title}");

            if ($book->hasSummary() && !$this->option('regenerate')) {
                $this->warn('Book already has summary. Use --regenerate to recreate.');
                return;
            }

            $summary = $this->aiSummaryService->generateSummaryWithFallback($book);
            
            $this->info("✅ Summary generated successfully!");
            $this->line("Status: {$summary->status}");
            $this->line("Model: {$summary->ai_model}");
            
            if ($summary->summary) {
                $this->line("Preview: " . substr($summary->summary, 0, 100) . "...");
            }

        } catch (\Exception $e) {
            $this->error("Error generating summary: " . $e->getMessage());
        }
    }

    private function generateForAllBooks()
    {
        $this->info('Generating summaries for all books...');
        
        $books = Book::with(['author', 'category', 'summary'])->get();
        $progressBar = $this->output->createProgressBar($books->count());
        
        $generated = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($books as $book) {
            try {
                if ($book->hasSummary() && !$this->option('regenerate')) {
                    $skipped++;
                } else {
                    $this->aiSummaryService->generateSummaryWithFallback($book);
                    $generated++;
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed for book {$book->title}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Summary:");
        $this->line("✅ Generated: {$generated}");
        $this->line("⏭️  Skipped: {$skipped}");
        $this->line("❌ Failed: {$failed}");
    }
}
