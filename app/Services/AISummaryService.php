<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookSummary;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class AISummaryService
{
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function generateSummary(Book $book)
    {
        try {
            // Tạo hoặc cập nhật status thành processing
            $summary = BookSummary::updateOrCreate(
                ['book_id' => $book->id],
                ['status' => 'processing', 'error_message' => null]
            );

            // Tạo prompt từ thông tin sách
            $prompt = $this->buildPrompt($book);

            // Gọi API Gemini
            $response = Http::timeout(60)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 1,
                    'topP' => 1,
                    'maxOutputTokens' => 2048,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Clean up content để chỉ lấy JSON
                $content = $this->extractJsonFromResponse($content);
                
                // Parse JSON response
                $aiResult = json_decode($content, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($aiResult)) {
                    // Cập nhật kết quả
                    $summary->update([
                        'summary' => $aiResult['summary'] ?? '',
                        'detailed_summary' => $aiResult['detailed_summary'] ?? '',
                        'key_points' => $aiResult['key_points'] ?? [],
                        'themes' => $aiResult['themes'] ?? [],
                        'status' => 'completed',
                        'ai_model' => 'gemini-1.5-flash'
                    ]);
                    
                    return $summary;
                } else {
                    throw new Exception('Invalid JSON response from AI: ' . json_last_error_msg());
                }
            } else {
                throw new Exception('API Error: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('AI Summary Generation Failed', [
                'book_id' => $book->id,
                'error' => $e->getMessage()
            ]);

            if (isset($summary)) {
                $summary->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }

            throw $e;
        }
    }

    private function buildPrompt(Book $book)
    {
        $prompt = "Bạn là một chuyên gia phân tích và tóm tắt sách. ";
        $prompt .= "Hãy tóm tắt cuốn sách sau đây và trả lời bằng JSON với format chính xác: ";
        $prompt .= '{"summary": "string", "detailed_summary": "string", "key_points": ["array"], "themes": ["array"]}';
        $prompt .= "\n\n";
        
        $prompt .= "Thông tin sách:\n";
        $prompt .= "**Tiêu đề:** {$book->title}\n";
        $prompt .= "**Tác giả:** {$book->author->name}\n";
        $prompt .= "**Thể loại:** {$book->category->name}\n";
        
        if ($book->description) {
            $prompt .= "**Mô tả:** {$book->description}\n";
        }
        
        if ($book->page_count) {
            $prompt .= "**Số trang:** {$book->page_count}\n";
        }

        $prompt .= "\nYêu cầu:\n";
        $prompt .= "1. Tạo một tóm tắt ngắn gọn (100-150 từ) cho field 'summary'\n";
        $prompt .= "2. Tạo một tóm tắt chi tiết (300-500 từ) cho field 'detailed_summary'\n";
        $prompt .= "3. Liệt kê 5-7 điểm chính của cuốn sách cho field 'key_points' (array)\n";
        $prompt .= "4. Xác định 3-5 chủ đề chính cho field 'themes' (array)\n";
        $prompt .= "\nVui lòng trả lời bằng tiếng Việt và CHỈ trả về JSON hợp lệ, không có text khác.";

        return $prompt;
    }

    /**
     * Extract JSON from Gemini response that might contain extra text
     */
    private function extractJsonFromResponse($content)
    {
        // Remove markdown code blocks if present
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*$/', '', $content);
        
        // Find JSON object by looking for opening and closing braces
        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        
        if ($start !== false && $end !== false && $end > $start) {
            return substr($content, $start, $end - $start + 1);
        }
        
        return $content;
    }

    public function chatAboutBook(Book $book, string $userMessage)
    {
        try {
            // Tạo context về sách để AI hiểu rõ hơn
            $bookContext = $this->buildBookContext($book);
            $prompt = $this->buildChatPrompt($book, $bookContext, $userMessage);

            // Gọi API Gemini
            $response = Http::timeout(30)->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 1,
                    'topP' => 1,
                    'maxOutputTokens' => 1000,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                // Làm sạch response
                return trim($content);
            } else {
                throw new Exception('API Error: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('AI Chat Failed', [
                'book_id' => $book->id,
                'user_message' => $userMessage,
                'error' => $e->getMessage()
            ]);

            // Fallback response
            return $this->generateFallbackChatResponse($book, $userMessage);
        }
    }

    private function buildBookContext(Book $book)
    {
        $context = "Thông tin sách:\n";
        $context .= "- Tiêu đề: {$book->title}\n";
        $context .= "- Tác giả: {$book->author->name}\n";
        $context .= "- Thể loại: {$book->category->name}\n";
        
        if ($book->description) {
            $context .= "- Mô tả: " . substr($book->description, 0, 300) . "\n";
        }
        
        if ($book->page_count) {
            $context .= "- Số trang: {$book->page_count}\n";
        }

        // Thêm thông tin từ tóm tắt nếu có
        if ($book->summary && $book->summary->summary) {
            $context .= "- Tóm tắt: " . substr($book->summary->summary, 0, 200) . "\n";
        }

        return $context;
    }

    private function buildChatPrompt(Book $book, string $bookContext, string $userMessage)
    {
        $prompt = "Bạn là một trợ lý AI chuyên về phân tích sách và văn học. ";
        $prompt .= "QUAN TRỌNG - QUY TẮC CHAT:\n";
        $prompt .= "1. Bạn CHỈ được trả lời về cuốn sách '{$book->title}' của tác giả {$book->author->name}\n";
        $prompt .= "2. TUYỆT ĐỐI KHÔNG trả lời về: chính trị, tôn giáo, thời tiết, y tế, lập trình, tài chính, hay bất kỳ chủ đề nào khác\n";
        $prompt .= "3. Nếu câu hỏi không liên quan đến sách này, hãy từ chối lịch sự và yêu cầu hỏi về sách\n";
        $prompt .= "4. Nếu câu hỏi mơ hồ, hãy yêu cầu làm rõ trong bối cảnh của cuốn sách\n";
        $prompt .= "5. Chỉ thảo luận về: nội dung, nhân vật, cốt truyện, chủ đề, ý nghĩa, phong cách viết, đánh giá\n\n";
        
        $prompt .= $bookContext . "\n";
        
        $prompt .= "Câu hỏi của người dùng: \"{$userMessage}\"\n\n";
        
        $prompt .= "KIỂM TRA: Câu hỏi này có thực sự về cuốn sách '{$book->title}' không?\n";
        $prompt .= "- Nếu KHÔNG: Từ chối lịch sự và yêu cầu hỏi về sách\n";
        $prompt .= "- Nếu CÓ: Trả lời chi tiết và hữu ích về sách\n\n";
        
        $prompt .= "Trả lời bằng tiếng Việt, ngắn gọn (tối đa 200 từ), thân thiện và chuyên nghiệp.";

        return $prompt;
    }

    private function generateFallbackChatResponse(Book $book, string $userMessage)
    {
        // Kiểm tra xem câu hỏi có liên quan đến sách không ngay trong fallback
        $lowerMessage = strtolower($userMessage);
        $bookTitle = strtolower($book->title);
        $authorName = strtolower($book->author->name);
        
        // Phản hồi cụ thể hơn dựa trên nội dung câu hỏi
        if (strpos($lowerMessage, 'tóm tắt') !== false || strpos($lowerMessage, 'nội dung') !== false) {
            return "Cuốn sách '{$book->title}' của tác giả {$book->author->name} thuộc thể loại {$book->category->name}. " .
                   "Tôi đang gặp sự cố kỹ thuật nên không thể cung cấp tóm tắt chi tiết lúc này. Vui lòng thử lại sau.";
        }
        
        if (strpos($lowerMessage, 'tác giả') !== false || strpos($lowerMessage, $authorName) !== false) {
            return "Tác giả của cuốn sách này là {$book->author->name}. " .
                   "Tôi đang gặp vấn đề kỹ thuật nên không thể cung cấp thêm thông tin về tác giả lúc này.";
        }
        
        if (strpos($lowerMessage, 'thể loại') !== false || strpos($lowerMessage, 'genre') !== false) {
            return "'{$book->title}' thuộc thể loại {$book->category->name}. " .
                   "Tôi đang gặp sự cố nên không thể phân tích sâu hơn về thể loại này.";
        }
        
        // Phản hồi chung khi API lỗi
        $responses = [
            "Tôi hiểu bạn muốn biết về cuốn sách '{$book->title}'. Tuy nhiên, tôi đang gặp sự cố kỹ thuật. Vui lòng thử lại sau.",
            "Xin lỗi, tôi đang không thể phân tích chi tiết về '{$book->title}' lúc này. Hệ thống đang được bảo trì.",
            "Tôi muốn giúp bạn tìm hiểu về '{$book->title}', nhưng đang gặp vấn đề kỹ thuật. Vui lòng thử lại trong ít phút."
        ];

        return $responses[array_rand($responses)];
    }

    public function generateSummaryWithFallback(Book $book)
    {
        try {
            return $this->generateSummary($book);
        } catch (Exception $e) {
            // Fallback với demo data nếu API không khả dụng
            Log::warning('Using fallback summary for book: ' . $book->id);
            
            return $this->generateFallbackSummary($book);
        }
    }

    private function generateFallbackSummary(Book $book)
    {
        $summary = BookSummary::updateOrCreate(
            ['book_id' => $book->id],
            [
                'summary' => $this->generateDefaultSummary($book),
                'detailed_summary' => $this->generateDefaultDetailedSummary($book),
                'key_points' => $this->generateDefaultKeyPoints($book),
                'themes' => $this->generateDefaultThemes($book),
                'status' => 'completed',
                'ai_model' => 'fallback'
            ]
        );

        return $summary;
    }

    private function generateDefaultSummary(Book $book)
    {
        return "Cuốn sách \"{$book->title}\" của tác giả {$book->author->name} là một tác phẩm thuộc thể loại {$book->category->name}. " .
               "Với {$book->page_count} trang, cuốn sách mang đến cho người đọc những kiến thức và trải nghiệm độc đáo trong lĩnh vực này.";
    }

    private function generateDefaultDetailedSummary(Book $book)
    {
        return "Cuốn sách \"{$book->title}\" của tác giả {$book->author->name} là một tác phẩm xuất sắc trong thể loại {$book->category->name}. " .
               "Qua {$book->page_count} trang, tác giả đã khéo léo xây dựng nội dung phong phú và hấp dẫn. " .
               "Cuốn sách không chỉ cung cấp thông tin và kiến thức chuyên sâu mà còn mang đến những góc nhìn mới mẻ và sâu sắc. " .
               "Với phong cách viết cuốn hút và cách trình bày logic, tác phẩm phù hợp cho nhiều đối tượng độc giả khác nhau. " .
               "Đây là một cuốn sách đáng đọc cho những ai quan tâm đến lĩnh vực này.";
    }

    private function generateDefaultKeyPoints(Book $book)
    {
        return [
            "Nội dung phong phú và đa dạng",
            "Phong cách viết hấp dẫn và dễ hiểu",
            "Cung cấp kiến thức chuyên sâu về {$book->category->name}",
            "Phù hợp cho nhiều đối tượng độc giả",
            "Có giá trị học thuật và thực tiễn cao"
        ];
    }

    private function generateDefaultThemes(Book $book)
    {
        $categoryBasedThemes = [
            'Văn học' => ['Tình yêu', 'Cuộc sống', 'Con người'],
            'Khoa học' => ['Nghiên cứu', 'Khám phá', 'Công nghệ'],
            'Kinh tế' => ['Kinh doanh', 'Đầu tư', 'Phát triển'],
            'Giáo dục' => ['Học tập', 'Phát triển', 'Kỹ năng'],
        ];

        return $categoryBasedThemes[$book->category->name] ?? ['Kiến thức', 'Phát triển', 'Học hỏi'];
    }
}
