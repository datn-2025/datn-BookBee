<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookSummary;
use App\Models\Collection;
use App\Models\ComboSummary;
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
        $authorName = $book->authors->count() > 0 ? $book->authors->pluck('name')->join(', ') : 'Tác giả không xác định';
        $prompt .= "**Tác giả:** {$authorName}\n";
        
        $categoryName = $book->category ? $book->category->name : 'Thể loại không xác định';
        $prompt .= "**Thể loại:** {$categoryName}\n";
        
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

            // Làm sạch UTF-8 encoding trước khi gửi request
            $cleanPrompt = mb_convert_encoding($prompt, 'UTF-8', 'UTF-8');
            
            // Gọi API Gemini
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json; charset=utf-8'
                ])
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $cleanPrompt
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
                
                // Làm sạch response và đảm bảo UTF-8 hợp lệ
                $cleanContent = mb_convert_encoding(trim($content), 'UTF-8', 'UTF-8');
                return $cleanContent;
            } else {
                throw new Exception('API Error: ' . $response->body());
            }

        } catch (Exception $e) {
            Log::error('AI Chat Failed', [
                'book_id' => $book->id,
                'book_title' => $book->title,
                'user_message' => $userMessage,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'api_key_exists' => !empty($this->apiKey),
                'api_url' => $this->apiUrl
            ]);

            // Fallback response
            return $this->generateFallbackChatResponse($book, $userMessage);
        }
    }

    private function buildBookContext(Book $book)
    {
        $context = "Thông tin sách:\n";
        $context .= "- Tiêu đề: " . mb_convert_encoding($book->title, 'UTF-8', 'UTF-8') . "\n";
        $authorName = $book->authors->count() > 0 ? $book->authors->pluck('name')->join(', ') : 'Tác giả không xác định';
        $context .= "- Tác giả: " . mb_convert_encoding($authorName, 'UTF-8', 'UTF-8') . "\n";
        
        $categoryName = $book->category ? $book->category->name : 'Thể loại không xác định';
        $context .= "- Thể loại: " . mb_convert_encoding($categoryName, 'UTF-8', 'UTF-8') . "\n";
        
        if ($book->description) {
            $cleanDescription = mb_convert_encoding(substr($book->description, 0, 300), 'UTF-8', 'UTF-8');
            $context .= "- Mô tả: " . $cleanDescription . "\n";
        }
        
        if ($book->page_count) {
            $context .= "- Số trang: {$book->page_count}\n";
        }

        // Thêm thông tin từ tóm tắt nếu có
        if ($book->summary && $book->summary->summary) {
            $cleanSummary = mb_convert_encoding(substr($book->summary->summary, 0, 200), 'UTF-8', 'UTF-8');
            $context .= "- Tóm tắt: " . $cleanSummary . "\n";
        }

        return $context;
    }

    private function buildChatPrompt(Book $book, string $bookContext, string $userMessage)
    {
        // Làm sạch UTF-8 cho tất cả dữ liệu đầu vào
        $cleanBookTitle = mb_convert_encoding($book->title, 'UTF-8', 'UTF-8');
        $cleanAuthorName = mb_convert_encoding($book->authors->count() > 0 ? $book->authors->pluck('name')->join(', ') : 'Tác giả không xác định', 'UTF-8', 'UTF-8');
        $cleanUserMessage = mb_convert_encoding($userMessage, 'UTF-8', 'UTF-8');
        
        $prompt = "Bạn là một trợ lý AI chuyên về phân tích sách và văn học. ";
        $prompt .= "QUAN TRỌNG - QUY TẮC CHAT:\n";
        $prompt .= "1. Bạn CHỈ được trả lời về cuốn sách '{$cleanBookTitle}' của tác giả {$cleanAuthorName}\n";
        $prompt .= "2. TUYỆT ĐỐI KHÔNG trả lời về: chính trị, tôn giáo, thời tiết, y tế, lập trình, tài chính, hay bất kỳ chủ đề nào khác\n";
        $prompt .= "3. Nếu câu hỏi không liên quan đến sách này, hãy từ chối lịch sự và yêu cầu hỏi về sách\n";
        $prompt .= "4. Nếu câu hỏi mơ hồ, hãy yêu cầu làm rõ trong bối cảnh của cuốn sách\n";
        $prompt .= "5. Chỉ thảo luận về: nội dung, nhân vật, cốt truyện, chủ đề, ý nghĩa, phong cách viết, đánh giá\n\n";
        
        $prompt .= $bookContext . "\n";
        
        $prompt .= "Câu hỏi của người dùng: \"{$cleanUserMessage}\"\n\n";
        
        $prompt .= "KIỂM TRA: Câu hỏi này có thực sự về cuốn sách '{$cleanBookTitle}' không?\n";
        $prompt .= "- Nếu KHÔNG: Từ chối lịch sự và yêu cầu hỏi về sách\n";
        $prompt .= "- Nếu CÓ: Trả lời chi tiết và hữu ích về sách\n\n";
        
        $prompt .= "Trả lời bằng tiếng Việt, ngắn gọn (tối đa 200 từ), thân thiện và chuyên nghiệp.";

        return $prompt;
    }

    private function generateFallbackChatResponse(Book $book, string $userMessage)
    {
        // Làm sạch UTF-8 cho dữ liệu sách
        $cleanBookTitle = mb_convert_encoding($book->title, 'UTF-8', 'UTF-8');
        $cleanAuthorName = mb_convert_encoding($book->authors->count() > 0 ? $book->authors->pluck('name')->join(', ') : 'Tác giả không xác định', 'UTF-8', 'UTF-8');
        $cleanCategoryName = mb_convert_encoding($book->category ? $book->category->name : 'Thể loại không xác định', 'UTF-8', 'UTF-8');
        
        // Kiểm tra xem câu hỏi có liên quan đến sách không ngay trong fallback
        $lowerMessage = strtolower($userMessage);
        $bookTitle = strtolower($cleanBookTitle);
        $authorName = strtolower($cleanAuthorName);
        
        // Phản hồi cụ thể hơn dựa trên nội dung câu hỏi
        if (strpos($lowerMessage, 'tóm tắt') !== false || strpos($lowerMessage, 'nội dung') !== false) {
            return "Cuốn sách '{$cleanBookTitle}' của tác giả {$cleanAuthorName} thuộc thể loại {$cleanCategoryName}. " .
                   "Tôi đang gặp sự cố kỹ thuật nên không thể cung cấp tóm tắt chi tiết lúc này. Vui lòng thử lại sau.";
        }
        
        if (strpos($lowerMessage, 'tác giả') !== false || strpos($lowerMessage, $authorName) !== false) {
            return "Tác giả của cuốn sách này là {$cleanAuthorName}. " .
                   "Tôi đang gặp vấn đề kỹ thuật nên không thể cung cấp thêm thông tin về tác giả lúc này.";
        }
        
        if (strpos($lowerMessage, 'thể loại') !== false || strpos($lowerMessage, 'genre') !== false) {
            return "'{$cleanBookTitle}' thuộc thể loại {$cleanCategoryName}. " .
                   "Tôi đang gặp sự cố nên không thể phân tích sâu hơn về thể loại này.";
        }
        
        // Phản hồi chung khi API lỗi
        $responses = [
            "Tôi hiểu bạn muốn biết về cuốn sách '{$cleanBookTitle}'. Tuy nhiên, tôi đang gặp sự cố kỹ thuật. Vui lòng thử lại sau.",
            "Xin lỗi, tôi đang không thể phân tích chi tiết về '{$cleanBookTitle}' lúc này. Hệ thống đang được bảo trì.",
            "Tôi muốn giúp bạn tìm hiểu về '{$cleanBookTitle}', nhưng đang gặp vấn đề kỹ thuật. Vui lòng thử lại trong ít phút."
        ];

        return $responses[array_rand($responses)];
    }

    public function generateSummaryWithFallback(Book $book)
    {
        try {
            return $this->generateSummary($book);
        } catch (Exception $e) {
            // Fallback với demo data nếu API không khả dụng
            Log::warning('Using fallback summar y for book: ' . $book->id);
            
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
        $authorName = $book->authors->count() > 0 ? $book->authors->pluck('name')->join(', ') : 'tác giả không xác định';
        $categoryName = $book->category ? $book->category->name : 'thể loại không xác định';
        $pageCount = $book->page_count ?: 'số trang không xác định';
        
        return "Cuốn sách \"{$book->title}\" của {$authorName} là một tác phẩm thuộc thể loại {$categoryName}. " .
               "Với {$pageCount} trang, cuốn sách mang đến cho người đọc những kiến thức và trải nghiệm độc đáo trong lĩnh vực này.";
    }

    private function generateDefaultDetailedSummary(Book $book)
    {
        $authorName = $book->authors->count() > 0 ? $book->authors->pluck('name')->join(', ') : 'tác giả không xác định';
        $categoryName = $book->category ? $book->category->name : 'thể loại không xác định';
        $pageCount = $book->page_count ?: 'số trang không xác định';
        
        return "Cuốn sách \"{$book->title}\" của tác giả {$authorName} là một tác phẩm xuất sắc trong thể loại {$categoryName}. " .
               "Qua {$pageCount} trang, tác giả đã khéo léo xây dựng nội dung phong phú và hấp dẫn. " .
               "Cuốn sách không chỉ cung cấp thông tin và kiến thức chuyên sâu mà còn mang đến những góc nhìn mới mẻ và sâu sắc. " .
               "Với phong cách viết cuốn hút và cách trình bày logic, tác phẩm phù hợp cho nhiều đối tượng độc giả khác nhau. " .
               "Đây là một cuốn sách đáng đọc cho những ai quan tâm đến lĩnh vực này.";
    }

    private function generateDefaultKeyPoints(Book $book)
    {
        $categoryName = $book->category ? $book->category->name : 'lĩnh vực này';
        
        return [
            "Nội dung phong phú và đa dạng",
            "Phong cách viết hấp dẫn và dễ hiểu",
            "Cung cấp kiến thức chuyên sâu về {$categoryName}",
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
        $categoryName = $book->category ? $book->category->name : null;
        return $categoryBasedThemes[$categoryName] ?? ['Kiến thức', 'Phát triển', 'Học hỏi'];
    }

    // Combo Summary Methods

    public function generateComboSummary(Collection $combo)
    {
        try {
            // Tạo hoặc cập nhật status thành processing
            $summary = ComboSummary::updateOrCreate(
                ['collection_id' => $combo->id],
                ['status' => 'processing', 'error_message' => null]
            );

            // Tạo prompt từ thông tin combo
            $prompt = $this->buildComboPrompt($combo);

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
                $result = $response->json();
                
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'];
                    $parsedData = $this->parseComboAIResponse($aiResponse);
                    
                    $summary->update([
                        'summary' => $parsedData['summary'],
                        'detailed_summary' => $parsedData['detailed_summary'],
                        'key_points' => $parsedData['key_points'],
                        'themes' => $parsedData['themes'],
                        'benefits' => $parsedData['benefits'] ?? [],
                        'status' => 'completed',
                        'ai_model' => 'gemini-1.5-flash',
                        'error_message' => null
                    ]);

                    return $summary;
                }
            }

            throw new Exception('API response không hợp lệ');

        } catch (Exception $e) {
            Log::error('Error generating combo summary:', [
                'combo_id' => $combo->id,
                'error' => $e->getMessage()
            ]);

            $summary->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    public function generateComboSummaryWithFallback(Collection $combo)
    {
        try {
            return $this->generateComboSummary($combo);
        } catch (Exception $e) {
            Log::warning('Using fallback summary for combo: ' . $combo->id);
            
            return $this->generateFallbackComboSummary($combo);
        }
    }

    private function buildComboPrompt(Collection $combo)
    {
        $comboName = mb_convert_encoding($combo->name, 'UTF-8', 'UTF-8');
        $comboDescription = mb_convert_encoding($combo->description ?? '', 'UTF-8', 'UTF-8');
        
        // Lấy thông tin các sách trong combo
        $booksInfo = $combo->books->map(function($book) {
            $authorName = $book->authors->count() > 0 ? $book->authors->pluck('name')->join(', ') : 'Tác giả không xác định';
            return "- {$book->title} (Tác giả: {$authorName})";
        })->join("\n");

        $prompt = "Bạn là một chuyên gia phân tích văn học và sách. Hãy tạo một tóm tắt chi tiết về combo sách sau:\n\n";
        $prompt .= "TÊN COMBO: {$comboName}\n";
        
        if ($comboDescription) {
            $prompt .= "MÔ TẢ: {$comboDescription}\n";
        }
        
        $prompt .= "CÁC SÁCH TRONG COMBO:\n{$booksInfo}\n\n";
        
        $prompt .= "Hãy trả về kết quả theo format JSON sau (QUAN TRỌNG: chỉ trả về JSON, không có text thêm):\n";
        $prompt .= "{\n";
        $prompt .= '  "summary": "Tóm tắt ngắn gọn về combo sách này (100-150 từ)",';
        $prompt .= '  "detailed_summary": "Tóm tắt chi tiết về giá trị và nội dung của combo (200-300 từ)",';
        $prompt .= '  "key_points": ["điểm chính 1", "điểm chính 2", "điểm chính 3", "điểm chính 4"],';
        $prompt .= '  "themes": ["chủ đề 1", "chủ đề 2", "chủ đề 3"],';
        $prompt .= '  "benefits": ["lợi ích 1", "lợi ích 2", "lợi ích 3", "lợi ích 4"]';
        $prompt .= "\n}\n\n";
        $prompt .= "Tất cả nội dung phải bằng tiếng Việt, chính xác và hữu ích cho độc giả.";

        return $prompt;
    }

    private function parseComboAIResponse($response)
    {
        try {
            // Làm sạch response
            $cleanResponse = trim($response);
            $cleanResponse = preg_replace('/^```json\s*/', '', $cleanResponse);
            $cleanResponse = preg_replace('/\s*```$/', '', $cleanResponse);
            $cleanResponse = mb_convert_encoding($cleanResponse, 'UTF-8', 'UTF-8');
            
            $data = json_decode($cleanResponse, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                return [
                    'summary' => $data['summary'] ?? 'Combo sách đa dạng với nhiều giá trị.',
                    'detailed_summary' => $data['detailed_summary'] ?? 'Combo này bao gồm nhiều cuốn sách với nội dung phong phú.',
                    'key_points' => $data['key_points'] ?? ['Đa dạng nội dung', 'Giá trị cao', 'Phù hợp nhiều độc giả'],
                    'themes' => $data['themes'] ?? ['Kiến thức', 'Phát triển'],
                    'benefits' => $data['benefits'] ?? ['Tiết kiệm chi phí', 'Đa dạng nội dung', 'Giá trị cao']
                ];
            }
        } catch (Exception $e) {
            Log::error('Error parsing combo AI response:', ['error' => $e->getMessage()]);
        }
        
        // Fallback nếu parse thất bại
        return [
            'summary' => 'Combo sách đa dạng với nhiều giá trị cho độc giả.',
            'detailed_summary' => 'Combo này tập hợp những cuốn sách chất lượng, mang lại trải nghiệm đọc phong phú.',
            'key_points' => ['Tập hợp sách chất lượng', 'Giá trị đọc cao', 'Đa dạng chủ đề'],
            'themes' => ['Kiến thức', 'Phát triển'],
            'benefits' => ['Tiết kiệm chi phí', 'Đa dạng nội dung', 'Trải nghiệm phong phú']
        ];
    }

    private function generateFallbackComboSummary(Collection $combo)
    {
        $summary = ComboSummary::updateOrCreate(
            ['collection_id' => $combo->id],
            [
                'summary' => $this->generateDefaultComboSummary($combo),
                'detailed_summary' => $this->generateDefaultComboDetailedSummary($combo),
                'key_points' => $this->generateDefaultComboKeyPoints($combo),
                'themes' => $this->generateDefaultComboThemes($combo),
                'benefits' => $this->generateDefaultComboBenefits($combo),
                'status' => 'completed',
                'ai_model' => 'fallback'
            ]
        );

        return $summary;
    }

    private function generateDefaultComboSummary(Collection $combo)
    {
        $bookCount = $combo->books->count();
        return "Combo \"{$combo->name}\" bao gồm {$bookCount} cuốn sách được tuyển chọn kỹ lưỡng, mang đến cho độc giả một bộ sưu tập đa dạng và có giá trị. Đây là sự kết hợp hoàn hảo giữa chất lượng nội dung và mức giá hợp lý, giúp độc giả tiết kiệm chi phí đồng thời có được những cuốn sách hay.";
    }

    private function generateDefaultComboDetailedSummary(Collection $combo)
    {
        $bookCount = $combo->books->count();
        $authors = $combo->books->flatMap(function($book) {
            return $book->authors->pluck('name');
        })->unique()->take(3)->join(', ');
        
        return "Combo \"{$combo->name}\" là một bộ sưu tập gồm {$bookCount} cuốn sách từ các tác giả uy tín như {$authors}. Bộ combo này được thiết kế để mang lại trải nghiệm đọc toàn diện, với những nội dung bổ ích và phong phú. Mỗi cuốn sách trong combo đều có giá trị riêng, khi kết hợp lại tạo thành một bộ sưu tập hoàn chỉnh giúp độc giả mở rộng kiến thức và phát triển bản thân.";
    }

    private function generateDefaultComboKeyPoints(Collection $combo)
    {
        return [
            "Bộ sưu tập {$combo->books->count()} cuốn sách chất lượng",
            "Giá ưu đãi so với mua lẻ từng cuốn",
            "Nội dung đa dạng và bổ sung lẫn nhau",
            "Phù hợp cho nhiều đối tượng độc giả khác nhau"
        ];
    }

    private function generateDefaultComboThemes(Collection $combo)
    {
        // Lấy category từ các sách trong combo
        $categories = $combo->books->pluck('category.name')->filter()->unique()->take(3);
        
        if ($categories->count() > 0) {
            return $categories->toArray();
        }
        
        return ['Kiến thức tổng hợp', 'Phát triển bản thân', 'Đọc hiểu'];
    }

    private function generateDefaultComboBenefits(Collection $combo)
    {
        return [
            "Tiết kiệm chi phí đáng kể so với mua lẻ",
            "Được đọc nhiều cuốn sách hay cùng lúc",
            "Nội dung đa dạng, bổ sung kiến thức toàn diện",
            "Trải nghiệm đọc phong phú và thú vị"
        ];
    }

    public function chatAboutCombo(Collection $combo, string $userMessage)
    {
        try {
            $prompt = $this->buildComboChatPrompt($combo, $userMessage);

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
                    'temperature' => 0.8,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    return trim($result['candidates'][0]['content']['parts'][0]['text']);
                }
            }

            throw new Exception('API response không hợp lệ');

        } catch (Exception $e) {
            Log::error('Error in combo chat:', [
                'combo_id' => $combo->id,
                'user_message' => $userMessage,
                'error' => $e->getMessage()
            ]);

            return 'Xin lỗi, tôi đang gặp sự cố kỹ thuật. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.';
        }
    }

    private function buildComboChatPrompt(Collection $combo, string $userMessage)
    {
        $cleanComboName = mb_convert_encoding($combo->name, 'UTF-8', 'UTF-8');
        $cleanUserMessage = mb_convert_encoding($userMessage, 'UTF-8', 'UTF-8');
        
        // Lấy thông tin chi tiết về các sách trong combo
        $booksContext = $combo->books->map(function($book) {
            $authorName = $book->authors->count() > 0 ? $book->authors->pluck('name')->join(', ') : 'Tác giả không xác định';
            $description = $book->description ? mb_substr(strip_tags($book->description), 0, 200) . '...' : '';
            return "- {$book->title} (Tác giả: {$authorName}): {$description}";
        })->join("\n");
        
        $prompt = "Bạn là một trợ lý AI chuyên về phân tích combo sách và tư vấn đọc sách. ";
        $prompt .= "QUAN TRỌNG - QUY TẮC CHAT:\n";
        $prompt .= "1. CHỈ trả lời về combo sách \"{$cleanComboName}\" và các sách trong combo này\n";
        $prompt .= "2. KHÔNG trả lời về các chủ đề khác (chính trị, tôn giáo, y tế, pháp luật, v.v.)\n";
        $prompt .= "3. Nếu câu hỏi không liên quan đến combo này, lịch sự từ chối và hướng dẫn hỏi về combo\n";
        $prompt .= "4. Trả lời bằng tiếng Việt, nhiệt tình và hữu ích\n";
        $prompt .= "5. Độ dài trả lời: 50-150 từ\n\n";
        
        $prompt .= "THÔNG TIN COMBO:\n";
        $prompt .= "Tên combo: {$cleanComboName}\n";
        if ($combo->description) {
            $prompt .= "Mô tả: " . mb_convert_encoding($combo->description, 'UTF-8', 'UTF-8') . "\n";
        }
        $prompt .= "Giá combo: " . number_format($combo->combo_price, 0, ',', '.') . "₫\n\n";
        
        $prompt .= "CÁC SÁCH TRONG COMBO:\n{$booksContext}\n\n";
        
        $prompt .= "CÂU HỎI CỦA NGƯỜI DÙNG: {$cleanUserMessage}\n\n";
        $prompt .= "HÃY TRẢ LỜI:";

        return $prompt;
    }
}
