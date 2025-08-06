# 📘 Tài liệu: Xây Chatbot Tư Vấn Sách Với Gemini API (Không Cần Cấu Hình Câu Hỏi)

## 🎯 Mục Tiêu

Xây dựng chatbot trong Laravel có khả năng:
- Hiểu các câu hỏi tự nhiên như "sách dưới 200k", "sách mới", "sách lập trình"...
- Không cần cấu hình câu hỏi mẫu hoặc intent thủ công.
- Gửi dữ liệu thật từ database vào Gemini API, để AI tự phân tích và phản hồi.

---

## 🔄 Luồng Xử Lý Tổng Quan

1. Người dùng nhập câu hỏi tự nhiên (VD: "sách dưới 200k")
2. Laravel nhận câu hỏi và lấy dữ liệu (sách + danh mục) từ DB
3. Ghép dữ liệu vào prompt và gửi lên Gemini API
4. Gemini phân tích prompt + dữ liệu → trả lời phù hợp
5. Laravel nhận kết quả và trả về cho frontend

---

## ⚙️ Laravel Controller (Ví dụ)

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Book;
use App\Models\Category;

class ChatController extends Controller
{
    public function handle(Request $request)
    {
        $userPrompt = $request->input('message');
        $books = Book::latest()->take(20)->get()->toArray();
        $categories = Category::all()->toArray();

        $prompt = $this->buildPrompt($userPrompt, $books, $categories);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('GEMINI_API_KEY'),
        ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent', [
            'contents' => [[
                'role' => 'user',
                'parts' => [['text' => $prompt]]
            ]]
        ]);

        $text = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi chưa hiểu rõ câu hỏi.';

        return response()->json(['reply' => $text]);
    }

    private function buildPrompt($userPrompt, $books, $categories)
    {
        return <<<EOT
Bạn là trợ lý AI tư vấn sách.

Người dùng hỏi: "{$userPrompt}"

Dưới đây là danh sách sách và danh mục:

Danh mục:
{$this->formatArray($categories)}

Sách:
{$this->formatArray($books)}

Hãy trả lời theo yêu cầu người dùng một cách chính xác, thân thiện và ngắn gọn.
EOT;
    }

    private function formatArray($array)
    {
        return json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
