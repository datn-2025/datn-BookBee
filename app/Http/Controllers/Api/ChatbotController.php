<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    /**
     * Xử lý tin nhắn từ chatbot với Gemini API
     */
    public function processMessage(Request $request)
    {
        try {
            // Xử lý JSON manually nếu cần
            $input = $request->all();
            if (empty($input) && $request->getContent()) {
                $rawContent = $request->getContent();
                // Xử lý encoding UTF-8
                $rawContent = mb_convert_encoding($rawContent, 'UTF-8', 'auto');
                $input = json_decode($rawContent, true);
                
                // Nếu vẫn không parse được, thử với utf8_encode
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $input = json_decode(utf8_encode($rawContent), true);
                }
            }
            
            $userPrompt = $input['message'] ?? $request->input('message', '');
            
            // Kiểm tra quick replies trước để tránh gọi API Gemini không cần thiết
            $quickResponse = $this->handleQuickReplies($userPrompt);
            if ($quickResponse) {
                return response()->json([
                    'success' => true,
                    'data' => $quickResponse
                ]);
            }
            
            // Nếu không có GEMINI_API_KEY, trả về response mặc định
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'type' => 'text',
                        'content' => 'Tôi hiểu bạn đang tìm kiếm thông tin. Để tôi giúp bạn tốt hơn, bạn có thể sử dụng các từ khóa như "sách bán chạy", "sách mới", hoặc tên danh mục cụ thể.',
                        'quick_replies' => [
                            'Sách bán chạy',
                            'Sách mới', 
                            'Sách giảm giá',
                            'Xem danh mục'
                        ]
                    ]
                ]);
            }
            
            // Lấy dữ liệu từ database
            $books = Book::with(['authors', 'formats', 'reviews'])->latest()->take(20)->get()->toArray();
            $categories = Category::get()->toArray();
            
            // Tạo prompt cho Gemini
            $prompt = $this->buildPrompt($userPrompt, $books, $categories);
            
            // Gọi Gemini API
            $geminiResponse = $this->callGeminiAPI($prompt);
            
            // Parse response từ Gemini
            $response = $this->parseGeminiResponse($geminiResponse, $userPrompt);
            
            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'response' => [
                    'type' => 'text',
                    'content' => 'Xin lỗi, tôi gặp sự cố kỹ thuật. Vui lòng thử lại sau.'
                ]
            ]);
        }
    }

    /**
     * Tạo prompt cho Gemini API
     */
    private function buildPrompt($userPrompt, $books, $categories)
    {
        return <<<EOT
Bạn là trợ lý AI tư vấn sách thông minh của BookBee.vn.

Người dùng hỏi: "{$userPrompt}"

Dưới đây là dữ liệu thực tế từ cửa hàng sách:

Danh mục sách:
{$this->formatArray($categories)}

Danh sách sách (20 cuốn mới nhất):
{$this->formatArray($books)}

Hướng dẫn trả lời:
1. Phân tích câu hỏi của người dùng một cách thông minh
2. Tìm kiếm và lọc sách phù hợp từ dữ liệu trên
3. Trả lời một cách thân thiện, chính xác và hữu ích
4. Nếu tìm thấy sách phù hợp, hãy đề xuất 3-5 cuốn tốt nhất
5. Bao gồm thông tin: tên sách, tác giả, giá, đánh giá (nếu có)
6. Nếu không tìm thấy, hãy gợi ý các lựa chọn khác

Hãy trả lời theo yêu cầu người dùng một cách chính xác, thân thiện và ngắn gọn.
EOT;
    }

    /**
     * Format mảng dữ liệu thành chuỗi JSON đẹp
     */
    private function formatArray($array)
    {
        return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Gọi Gemini API
     */
    private function callGeminiAPI($prompt)
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            
            if (!$apiKey) {
                throw new \Exception('GEMINI_API_KEY not configured');
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $prompt]]
                ]]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API failed', ['response' => $response->body()]);
                throw new \Exception('Gemini API request failed');
            }

            $data = $response->json();
            
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi chưa hiểu rõ câu hỏi.';
            
        } catch (\Exception $e) {
            Log::error('Gemini API error: ' . $e->getMessage());
            return 'Xin lỗi, tôi gặp sự cố kỹ thuật. Vui lòng thử lại sau.';
        }
    }

    /**
     * Parse và format response từ Gemini
     */
    private function parseGeminiResponse($geminiText, $userPrompt)
    {
        // Kiểm tra xem có phải thông báo lỗi từ API không
        if (strpos($geminiText, 'Xin lỗi, tôi gặp sự cố kỹ thuật') !== false) {
            return [
                'type' => 'text',
                'content' => $geminiText
            ];
        }
        
        // Kiểm tra xem có phải câu chào hỏi không
        if (preg_match('/(xin chào|hello|hi|chào)/i', $userPrompt)) {
            return [
                'type' => 'greeting',
                'content' => $geminiText,
                'quick_replies' => [
                    'Sách bán chạy',
                    'Sách mới',
                    'Sách giảm giá',
                    'Xem danh mục'
                ]
            ];
        }

        // Kiểm tra xem có yêu cầu xem danh mục không
        if (preg_match('/(danh mục|categories|menu)/i', $userPrompt)) {
            $categories = Category::take(8)->get();
            
            $categoryButtons = $categories->pluck('name')->toArray();
            
            return [
                'type' => 'categories',
                'content' => $geminiText,
                'categories' => $categoryButtons
            ];
        }

        // Thử tìm sách được đề xuất trong response
        $suggestedBooks = $this->extractBookSuggestions($geminiText, $userPrompt);
        
        if (!empty($suggestedBooks)) {
            return [
                'type' => 'product_list',
                'content' => $geminiText,
                'products' => $suggestedBooks
            ];
        }

        // Trả về text thường
        return [
            'type' => 'text',
            'content' => $geminiText,
            'quick_replies' => [
                'Sách bán chạy',
                'Sách mới', 
                'Xem danh mục'
            ]
        ];
    }

    /**
     * Trích xuất gợi ý sách từ response của Gemini
     */
    private function extractBookSuggestions($geminiText, $userPrompt)
    {
        $books = [];
        
        // Tìm sách dựa trên các pattern trong user prompt
        if (preg_match('/(dưới|under|below)\s*(\d+)\s*(k|nghìn)/i', $userPrompt, $matches)) {
            $amount = intval($matches[2]) * 1000;
            $books = Book::whereHas('formats', function($q) use ($amount) {
                $q->where('price', '<=', $amount);
            })->with(['authors', 'formats', 'reviews'])->take(5)->get();
        }
        elseif (preg_match('/(bán chạy|best seller|popular)/i', $userPrompt)) {
            $books = Book::select('books.*')
                        ->join('order_items', 'books.id', '=', 'order_items.book_id')
                        ->groupBy('books.id')
                        ->orderByRaw('SUM(order_items.quantity) DESC')
                        ->with(['authors', 'formats', 'reviews'])
                        ->take(5)
                        ->get();
        }
        elseif (preg_match('/(mới|new|latest)/i', $userPrompt)) {
            $books = Book::orderBy('publication_date', 'desc')
                        ->with(['authors', 'formats', 'reviews'])
                        ->take(5)
                        ->get();
        }
        elseif (preg_match('/(giảm giá|sale|discount)/i', $userPrompt)) {
            $books = Book::whereHas('formats', function($query) {
                        $query->whereNotNull('discount')
                              ->where('discount', '>', 0);
                    })
                    ->with(['authors', 'formats', 'reviews'])
                    ->take(5)
                    ->get();
        }
        else {
            // Tìm kiếm chung
            $books = Book::where('title', 'like', '%' . $userPrompt . '%')
                        ->orWhere('description', 'like', '%' . $userPrompt . '%')
                        ->with(['authors', 'formats', 'reviews'])
                        ->take(5)
                        ->get();
        }
        
        $products = [];
        foreach ($books as $book) {
            $products[] = $this->formatBookCard($book);
        }
        
        return $products;
    }



    /**
     * Format thông tin sách thành card
     */
    private function formatBookCard($book)
    {
        $formats = $book->formats ?? collect();
        $price = $formats->first()->price ?? 0;
        $discountPrice = $formats->first()->discount_price ?? null;
        
        $avgRating = $book->reviews ? $book->reviews->avg('rating') : 0;
        $reviewCount = $book->reviews ? $book->reviews->count() : 0;
        
        return [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->authors->pluck('name')->join(', ') ?? 'Chưa rõ tác giả',
            'price' => $price,
            'discount_price' => $discountPrice,
            'rating' => round($avgRating, 1),
            'review_count' => $reviewCount,
            'image' => $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/no-image.jpg'),
            'url' => route('books.show', $book->slug ?? $book->id)
        ];
    }



    /**
     * Xử lý quick replies trực tiếp từ database
     */
    private function handleQuickReplies($userPrompt)
    {
        $prompt = mb_strtolower(trim($userPrompt), 'UTF-8');
        
        // Chào hỏi
        if (preg_match('/(xin chào|chào|hello|hi|hey|chào bạn)/i', $prompt)) {
            return [
                'type' => 'greeting',
                'content' => '👋 Xin chào! Tôi là trợ lý BookBee. Tôi có thể giúp bạn tìm sách, tư vấn hoặc trả lời câu hỏi về sản phẩm. Bạn cần hỗ trợ gì?',
                'quick_replies' => [
                    'Sách bán chạy',
                    'Sách mới',
                    'Sách giảm giá',
                    'Xem danh mục'
                ]
            ];
        }
        
        // Sách bán chạy
        if (preg_match('/(sách bán chạy|bán chạy nhất|bestseller|best seller)/i', $prompt)) {
            $books = Book::select('books.*')
                        ->join('order_items', 'books.id', '=', 'order_items.book_id')
                        ->groupBy('books.id')
                        ->orderByRaw('SUM(order_items.quantity) DESC')
                        ->with(['authors', 'formats', 'reviews'])
                        ->take(6)
                        ->get();
            
            $products = [];
            foreach ($books as $book) {
                $products[] = $this->formatBookCard($book);
            }
            
            return [
                'type' => 'product_list',
                'content' => '📚 Đây là những cuốn sách bán chạy nhất tại BookBee.vn:',
                'products' => $products
            ];
        }
        
        // Sách mới
        if (preg_match('/(sách mới|mới nhất|new|latest)/i', $prompt)) {
            $books = Book::orderBy('publication_date', 'desc')
                        ->with(['authors', 'formats', 'reviews'])
                        ->take(6)
                        ->get();
            
            $products = [];
            foreach ($books as $book) {
                $products[] = $this->formatBookCard($book);
            }
            
            return [
                'type' => 'product_list',
                'content' => '🆕 Những cuốn sách mới nhất tại BookBee.vn:',
                'products' => $products
            ];
        }
        
        // Sách giảm giá
        if (preg_match('/(sách giảm giá|giảm giá|sale|discount|khuyến mãi)/i', $prompt)) {
            $books = Book::whereHas('formats', function($query) {
                        $query->whereNotNull('discount')
                              ->where('discount', '>', 0);
                    })
                    ->with(['authors', 'formats', 'reviews'])
                    ->take(6)
                    ->get();
            
            $products = [];
            foreach ($books as $book) {
                $products[] = $this->formatBookCard($book);
            }
            
            return [
                'type' => 'product_list',
                'content' => '🔥 Những cuốn sách đang giảm giá hot tại BookBee.vn:',
                'products' => $products
            ];
        }
        
        // Xem danh mục
        if (str_contains($prompt, 'danh mục') || str_contains($prompt, 'danh m?c') || str_contains($prompt, 'categories') || str_contains($prompt, 'menu') || str_contains($prompt, 'xem danh mục')) {
            $categories = Category::take(8)->get();
            
            $categoryButtons = $categories->pluck('name')->toArray();
            
            return [
                'type' => 'categories',
                'content' => '📂 Các danh mục sách tại BookBee.vn:',
                'categories' => $categoryButtons
            ];
        }
        
        // Sách theo danh mục cụ thể (dò động từ DB để khớp cả quick-action như "Tiểu thuyết")
        $allCategories = Category::select(['id', 'name'])->get();
        foreach ($allCategories as $cat) {
            $catName = trim($cat->name);
            $catLower = mb_strtolower($catName, 'UTF-8');
            // Khớp nếu prompt chứa tên danh mục hoặc có tiền tố "sách <danh mục>"
            if (
                Str::contains($prompt, $catLower) ||
                Str::contains($prompt, 'sách ' . $catLower)
            ) {
                $books = Book::where('category_id', $cat->id)
                            ->with(['authors', 'formats', 'reviews'])
                            ->take(6)
                            ->get();

                $products = [];
                foreach ($books as $book) {
                    $products[] = $this->formatBookCard($book);
                }

                return [
                    'type' => 'product_list',
                    'content' => "📖 Sách {$catName} tại BookBee.vn:",
                    'products' => $products
                ];
            }
        }
        
        // Tìm kiếm tổng quát
        if (preg_match('/(tìm|search|find|kiếm)/i', $prompt) || strlen($prompt) > 3) {
            // Tìm kiếm trong tiêu đề và mô tả sách
            $books = Book::where(function($query) use ($prompt) {
                        $query->where('title', 'like', '%' . $prompt . '%')
                              ->orWhere('description', 'like', '%' . $prompt . '%');
                    })
                    ->orWhereHas('authors', function($query) use ($prompt) {
                        $query->where('name', 'like', '%' . $prompt . '%');
                    })
                    ->with(['authors', 'formats', 'reviews'])
                    ->take(6)
                    ->get();
            
            if ($books->count() > 0) {
                $products = [];
                foreach ($books as $book) {
                    $products[] = $this->formatBookCard($book);
                }
                
                return [
                    'type' => 'product_list',
                    'content' => "🔍 Tôi tìm thấy {$books->count()} sách phù hợp với '{$userPrompt}':",
                    'products' => $products
                ];
            } else {
                return [
                    'type' => 'text',
                    'content' => "😔 Xin lỗi, tôi không tìm thấy sách nào phù hợp với '{$userPrompt}'. Bạn có thể thử tìm kiếm với từ khóa khác hoặc xem các danh mục sách của chúng tôi.",
                    'quick_replies' => [
                        'Xem danh mục',
                        'Sách bán chạy',
                        'Sách mới',
                        'Sách giảm giá'
                    ]
                ];
            }
        }
        
        return null; // Không phải quick reply, tiếp tục với Gemini API
    }
    
    /**
     * Lấy danh sách danh mục
     */
    public function getCategories()
    {
        $categories = Category::whereNull('parent_id')
                            ->get();
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Lấy sách theo danh mục
     */
    public function getBooksByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        
        $books = Book::where('category_id', $categoryId)
                    ->take(10)
                    ->get();
        
        $products = [];
        foreach ($books as $book) {
            $products[] = $this->formatBookCard($book);
        }
        
        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}