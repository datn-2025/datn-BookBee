<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookSummary;
use App\Models\Collection;
use App\Models\ComboSummary;
use App\Services\AISummaryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AISummaryController extends Controller
{
    protected $aiSummaryService;

    public function __construct(AISummaryService $aiSummaryService)
    {
        $this->aiSummaryService = $aiSummaryService;
    }

    /**
     * Tạo tóm tắt AI cho một cuốn sách
     */
    public function generateSummary(Request $request, $bookId): JsonResponse
    {
        try {
            $book = Book::with(['authors', 'category'])->findOrFail($bookId);

            // Kiểm tra xem đã có summary chưa
            if ($book->hasSummary() && $book->summary->isCompleted()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sách đã có tóm tắt AI',
                    'data' => $book->summary
                ]);
            }

            // Tạo summary mới
            $summary = $this->aiSummaryService->generateSummaryWithFallback($book);

            return response()->json([
                'success' => true,
                'message' => 'Tạo tóm tắt AI thành công',
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo tóm tắt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy tóm tắt AI của một cuốn sách
     */
    public function getSummary($bookId): JsonResponse
    {
        try {
            $book = Book::with('summary')->findOrFail($bookId);

            if (!$book->hasSummary()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sách chưa có tóm tắt AI'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $book->summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sách'
            ], 404);
        }
    }

    /**
     * Xóa tóm tắt AI và tạo lại
     */
    public function regenerateSummary(Request $request, $bookId): JsonResponse
    {
        try {
            $book = Book::with(['authors', 'category', 'summary'])->findOrFail($bookId);

            // Xóa summary cũ nếu có
            if ($book->hasSummary()) {
                $book->summary->delete();
            }

            // Tạo summary mới
            $summary = $this->aiSummaryService->generateSummaryWithFallback($book);

            return response()->json([
                'success' => true,
                'message' => 'Tạo lại tóm tắt AI thành công',
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo lại tóm tắt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra trạng thái tóm tắt AI
     */
    public function checkStatus($bookId): JsonResponse
    {
        try {
            $book = Book::with('summary')->findOrFail($bookId);

            if (!$book->hasSummary()) {
                return response()->json([
                    'success' => true,
                    'status' => 'none',
                    'message' => 'Chưa có tóm tắt'
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $book->summary->status,
                'message' => $this->getStatusMessage($book->summary->status),
                'data' => $book->summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sách'
            ], 404);
        }
    }

    /**
     * Chat với AI về nội dung sách
     */
    public function chatWithAI(Request $request, $bookId): JsonResponse
    {
        try {
            // Validation với custom messages
            $request->validate([
                'message' => [
                    'required',
                    'string',
                    'min:3',
                    'max:300',
                    function ($attribute, $value, $fail) {
                        // Kiểm tra UTF-8 hợp lệ
                        if (!mb_check_encoding($value, 'UTF-8')) {
                            $fail('Tin nhắn chứa ký tự không hợp lệ.');
                        }
                        
                        // Kiểm tra không chỉ chứa whitespace
                        if (trim($value) === '') {
                            $fail('Tin nhắn không được để trống.');
                        }
                    }
                ]
            ], [
                'message.required' => 'Vui lòng nhập tin nhắn.',
                'message.string' => 'Tin nhắn phải là chuỗi ký tự.',
                'message.min' => 'Tin nhắn phải có ít nhất 3 ký tự.',
                'message.max' => 'Tin nhắn không được vượt quá 300 ký tự.'
            ]);

            // Kiểm tra sách tồn tại
            $book = Book::with(['authors', 'category', 'summary'])->find($bookId);
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy sách.'
                ], 404);
            }

            $userMessage = trim($request->input('message'));

            // Làm sạch UTF-8 encoding cho user message
            $userMessage = mb_convert_encoding($userMessage, 'UTF-8', 'UTF-8');

            // Kiểm tra rate limiting đơn giản (có thể cải thiện với Redis)
            $sessionKey = 'chat_' . $bookId . '_' . (session()->getId() ?: 'guest_' . $request->ip());
            $lastChatTime = session()->get($sessionKey . '_last_time', 0);
            $chatCount = session()->get($sessionKey . '_count', 0);
            
            $now = time();
            
            // Reset counter mỗi phút
            if ($now - session()->get($sessionKey . '_reset_time', 0) > 60) {
                $chatCount = 0;
                session()->put($sessionKey . '_reset_time', $now);
            }
            
            // Giới hạn 10 tin nhắn mỗi phút
            if ($chatCount >= 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã gửi quá nhiều tin nhắn. Vui lòng đợi một chút rồi thử lại.'
                ], 429);
            }
            
            // Giới hạn không được gửi tin nhắn liên tiếp trong vòng 2 giây
            if ($now - $lastChatTime < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đợi một chút trước khi gửi tin nhắn tiếp theo.'
                ], 429);
            }

            // Kiểm tra xem câu hỏi có liên quan đến sách không
            if (!$this->isBookRelatedQuestion($userMessage)) {
                // Lưu thông tin để tracking
                session()->put($sessionKey . '_last_time', $now);
                session()->put($sessionKey . '_count', $chatCount + 1);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Tôi chỉ có thể trả lời các câu hỏi liên quan đến cuốn sách "' . $book->title . '". Vui lòng hỏi về nội dung, tác giả, nhân vật, cốt truyện, hoặc thông tin khác của sách này.'
                ]);
            }

            // Gọi AI service với try-catch riêng
            try {
                $aiResponse = $this->aiSummaryService->chatAboutBook($book, $userMessage);
            } catch (\Exception $aiError) {
                Log::error('AI Service Error', [
                    'book_id' => $bookId,
                    'user_message' => $userMessage,
                    'ai_error' => $aiError->getMessage()
                ]);

                // Fallback response nếu AI service lỗi
                $aiResponse = "Xin lỗi, hiện tại tôi không thể trả lời câu hỏi của bạn về cuốn sách '{$book->title}'. Hệ thống AI đang gặp vấn đề. Vui lòng thử lại sau ít phút.";
            }

            // Cập nhật session tracking
            session()->put($sessionKey . '_last_time', $now);
            session()->put($sessionKey . '_count', $chatCount + 1);

            return response()->json([
                'success' => true,
                'response' => $aiResponse,
                'book_title' => $book->title,
                'remaining_messages' => max(0, 10 - ($chatCount + 1))
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Xử lý validation errors
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Log chi tiết lỗi để debug
            Log::error('Chat with AI Controller Error', [
                'book_id' => $bookId,
                'user_message' => $request->input('message'),
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'session_id' => session()->getId()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Kiểm tra câu hỏi có liên quan đến sách không
     */
    private function isBookRelatedQuestion($message): bool
    {
        $message = strtolower(trim($message));
        
        // Từ chối ngay nếu tin nhắn trống hoặc quá ngắn
        if (strlen($message) < 3) {
            return false;
        }

        // Danh sách các câu hỏi/chủ đề bị cấm
        $forbiddenTopics = [
            'thời tiết', 'chính trị', 'tôn giáo', 'làm bài tập', 'viết code',
            'lập trình', 'hacking', 'virus', 'sex', 'bạo lực', 'ma túy',
            'cờ bạc', 'đánh bạc', 'kiếm tiền', 'mua bán', 'quảng cáo',
            'link', 'website', 'facebook', 'zalo', 'telegram', 'tiktok',
            'số điện thoại', 'địa chỉ', 'email', 'password', 'mật khẩu',
            'covid', 'vaccine', 'thuốc', 'bệnh', 'y tế', 'chữa bệnh'
        ];

        // Kiểm tra các chủ đề bị cấm
        foreach ($forbiddenTopics as $forbidden) {
            if (strpos($message, $forbidden) !== false) {
                return false;
            }
        }

        // Từ khóa liên quan đến sách (được mở rộng)
        $bookKeywords = [
            // Từ khóa chung về sách
            'sách', 'cuốn sách', 'quyển sách', 'tác phẩm', 'book',
            
            // Tác giả
            'tác giả', 'người viết', 'author', 'nhà văn', 'văn sĩ',
            
            // Nội dung sách
            'nội dung', 'cốt truyện', 'câu chuyện', 'story', 'plot',
            'nhân vật', 'character', 'vai diễn', 'người trong truyện',
            'chương', 'chapter', 'phần', 'đoạn', 'trang',
            
            // Thể loại và phân loại
            'thể loại', 'genre', 'loại sách', 'phân loại',
            'tiểu thuyết', 'truyện', 'novel', 'fiction',
            'phi hư cấu', 'non-fiction', 'tự truyện', 'biography',
            
            // Đánh giá và cảm nhận
            'tóm tắt', 'summary', 'đánh giá', 'review', 'nhận xét',
            'cảm nhận', 'ý kiến', 'opinion', 'bình luận',
            'thích', 'hay', 'tốt', 'xuất sắc', 'tuyệt vời',
            
            // Chủ đề và ý nghĩa
            'chủ đề', 'theme', 'ý nghĩa', 'meaning', 'thông điệp',
            'bài học', 'lesson', 'kinh nghiệm', 'góc nhìn',
            'triết lý', 'philosophy', 'quan điểm', 'tư tưởng',
            
            // Thông tin xuất bản
            'xuất bản', 'publish', 'nhà xuất bản', 'publisher',
            'năm xuất bản', 'phiên bản', 'edition', 'in ấn',
            
            // Độc giả
            'độc giả', 'reader', 'người đọc', 'đọc sách',
            'phù hợp', 'suitable', 'độ tuổi', 'age group',
            'khuyên đọc', 'recommend', 'gợi ý',
            
            // Kết thúc và phát triển
            'kết thúc', 'ending', 'kết', 'end', 'final',
            'phát triển', 'development', 'diễn biến', 'tiến triển',
            
            // So sánh
            'so sánh', 'compare', 'khác', 'giống', 'tương tự',
            'other books', 'sách khác', 'tác phẩm khác'
        ];

        // Đếm số từ khóa liên quan đến sách
        $bookKeywordCount = 0;
        foreach ($bookKeywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                $bookKeywordCount++;
            }
        }

        // Cần ít nhất 1 từ khóa liên quan đến sách
        if ($bookKeywordCount === 0) {
            return false;
        }

        // Kiểm tra độ dài tin nhắn hợp lý (không quá ngắn, không quá dài)
        if (strlen($message) > 300) {
            return false;
        }

        // Từ chối các câu hỏi có vẻ như spam hoặc không nghiêm túc
        $spamPatterns = [
            '/(.)\1{4,}/', // Lặp lại ký tự nhiều lần
            '/[A-Z]{10,}/', // Quá nhiều chữ hoa liên tiếp
            '/\?{3,}/', // Quá nhiều dấu hỏi
            '/!{3,}/', // Quá nhiều dấu cảm
        ];

        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return false;
            }
        }

        return true;
    }

    private function getStatusMessage($status)
    {
        return match($status) {
            'pending' => 'Đang chờ xử lý',
            'processing' => 'Đang xử lý',
            'completed' => 'Hoàn thành',
            'failed' => 'Thất bại',
            default => 'Không xác định'
        };
    }

    // COMBO AI SUMMARY METHODS

    /**
     * Tạo tóm tắt AI cho combo sách
     */
    public function generateComboSummary(Request $request, $comboId): JsonResponse
    {
        try {
            $combo = Collection::with(['books.authors', 'books.category'])->findOrFail($comboId);

            // Kiểm tra xem đã có summary chưa
            if ($combo->hasSummary() && $combo->summary->isCompleted()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Combo đã có tóm tắt AI',
                    'data' => $combo->summary
                ]);
            }

            // Tạo summary mới
            $summary = $this->aiSummaryService->generateComboSummaryWithFallback($combo);

            return response()->json([
                'success' => true,
                'message' => 'Tạo tóm tắt AI cho combo thành công',
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo tóm tắt combo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy tóm tắt AI của combo
     */
    public function getComboSummary($comboId): JsonResponse
    {
        try {
            $combo = Collection::with('summary')->findOrFail($comboId);

            if (!$combo->hasSummary()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Combo chưa có tóm tắt AI'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $combo->summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy combo'
            ], 404);
        }
    }

    /**
     * Xóa tóm tắt AI và tạo lại cho combo
     */
    public function regenerateComboSummary(Request $request, $comboId): JsonResponse
    {
        try {
            $combo = Collection::with(['books.authors', 'books.category', 'summary'])->findOrFail($comboId);

            // Xóa summary cũ nếu có
            if ($combo->hasSummary()) {
                $combo->summary->delete();
            }

            // Tạo summary mới
            $summary = $this->aiSummaryService->generateComboSummaryWithFallback($combo);

            return response()->json([
                'success' => true,
                'message' => 'Tạo lại tóm tắt AI cho combo thành công',
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo lại tóm tắt combo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra trạng thái tóm tắt AI combo
     */
    public function checkComboStatus($comboId): JsonResponse
    {
        try {
            $combo = Collection::with('summary')->findOrFail($comboId);

            if (!$combo->hasSummary()) {
                return response()->json([
                    'success' => true,
                    'status' => 'none',
                    'message' => 'Chưa có tóm tắt'
                ]);
            }

            return response()->json([
                'success' => true,
                'status' => $combo->summary->status,
                'message' => $this->getStatusMessage($combo->summary->status),
                'data' => $combo->summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy combo'
            ], 404);
        }
    }

    /**
     * Chat với AI về combo sách
     */
    public function chatWithComboAI(Request $request, $comboId): JsonResponse
    {
        $request->validate([
            'message' => [
                'required',
                'string',
                'min:3',
                'max:300',
                function ($attribute, $value, $fail) {
                    // Kiểm tra UTF-8 hợp lệ
                    if (!mb_check_encoding($value, 'UTF-8')) {
                        $fail('Tin nhắn chứa ký tự không hợp lệ.');
                    }
                    
                    // Kiểm tra không chỉ chứa whitespace
                    if (trim($value) === '') {
                        $fail('Tin nhắn không được để trống.');
                    }
                }
            ]
        ]);

        try {
            $combo = Collection::with(['books.authors'])->findOrFail($comboId);
            $userMessage = trim($request->input('message'));

            // Rate limiting - 10 tin nhắn mỗi phút
            $sessionKey = 'combo_chat_' . $comboId;
            $now = now();
            $lastTime = session($sessionKey . '_last_time');
            $chatCount = session($sessionKey . '_count', 0);

            if ($lastTime && $now->diffInMinutes($lastTime) < 1) {
                if ($chatCount >= 10) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã gửi quá nhiều tin nhắn. Vui lòng đợi 1 phút trước khi gửi tiếp.'
                    ], 429);
                }
            } else {
                // Reset counter sau 1 phút
                $chatCount = 0;
            }

            // Kiểm tra câu hỏi có liên quan đến combo không
            if (!$this->isComboRelatedQuestion($userMessage)) {
                // Lưu thông tin để tracking
                session()->put($sessionKey . '_last_time', $now);
                session()->put($sessionKey . '_count', $chatCount + 1);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Tôi chỉ có thể trả lời các câu hỏi liên quan đến combo "' . $combo->name . '". Vui lòng hỏi về nội dung, các sách trong combo, giá trị của combo, hoặc thông tin khác liên quan đến combo này.'
                ]);
            }

            // Gọi AI service
            $aiResponse = $this->aiSummaryService->chatAboutCombo($combo, $userMessage);

            // Cập nhật session tracking
            session()->put($sessionKey . '_last_time', $now);
            session()->put($sessionKey . '_count', $chatCount + 1);

            return response()->json([
                'success' => true,
                'response' => $aiResponse,
                'combo_name' => $combo->name,
                'remaining_messages' => max(0, 10 - ($chatCount + 1))
            ]);

        } catch (\Exception $e) {
            // Log chi tiết lỗi để debug
            Log::error('Chat with Combo AI Controller Error', [
                'combo_id' => $comboId,
                'user_message' => $request->input('message'),
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi chat với AI về combo. Vui lòng thử lại sau.'
            ], 500);
        }
    }

    /**
     * Kiểm tra câu hỏi có liên quan đến combo không
     */
    private function isComboRelatedQuestion($message): bool
    {
        $comboKeywords = [
            'combo', 'sách', 'book', 'đọc', 'tác giả', 'author', 'nội dung', 
            'content', 'giá', 'price', 'mua', 'buy', 'review', 'đánh giá',
            'tóm tắt', 'summary', 'chủ đề', 'theme', 'thể loại', 'genre',
            'nhân vật', 'character', 'cốt truyện', 'plot', 'bộ', 'set',
            'collection', 'gói', 'package', 'tiết kiệm', 'save', 'ưu đãi',
            'khuyến mãi', 'discount', 'giá trị', 'value', 'lợi ích', 'benefit'
        ];

        $message = mb_strtolower($message);
        
        foreach ($comboKeywords as $keyword) {
            if (mb_strpos($message, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
