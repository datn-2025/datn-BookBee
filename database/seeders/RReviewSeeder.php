<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RReviewSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();
        $orders = Order::all();

        if ($users->isEmpty() || $books->isEmpty() || $orders->isEmpty()) {
            $this->command->error('Vui lòng seed đầy đủ users, books, orders trước khi seed reviews!');
            return;
        }

        $sampleComments = [
            'Sách rất hay, nội dung bổ ích.',
            'Chất lượng giấy in tốt, đóng gói cẩn thận.',
            'Sách có giá trị, nội dung sâu sắc.',
            'Hơi thất vọng, sách bị móp nhẹ.',
            'Tác giả viết rất cuốn hút, không thể dừng lại.',
            'Nội dung dễ hiểu, phù hợp với người mới bắt đầu.',
            'Sách này rất hay, tôi đã đọc đi đọc lại nhiều lần.',
            'Sách này rất thú vị, tôi đã học được nhiều điều mới.',
            'Giao hàng nhanh, đóng gói chắc chắn.',
            'Tác phẩm kinh điển, nên đọc.',
            'Sách này không như mong đợi, hơi khó hiểu.',
            'Rất hài lòng với chất lượng sản phẩm.',
            'Sách này rất phù hợp với sở thích của tôi.',
            'Tôi đã mua nhiều sách của tác giả này, chưa bao giờ thất vọng.',
            'Sách này rất đáng giá, tôi sẽ giới thiệu cho bạn bè.',
            'Tôi rất thích cách tác giả trình bày vấn đề.',
            'Sách này có nhiều lỗi chính tả, cần chỉnh sửa lại.',
            'Tôi không thích nội dung của sách này, hơi nhàm chán.',
            'Sách này rất phù hợp với những ai yêu thích thể loại phiêu lưu.',
            'Tôi đã mua sách này làm quà tặng, người nhận rất thích.',
            'Sách này rất hữu ích cho việc học tập và nghiên cứu.',
            'Tôi đã tìm kiếm sách này từ lâu, cuối cùng cũng mua được.',
            'Sách này rất phù hợp với những ai yêu thích thể loại khoa học viễn tưởng.',
            'Tôi rất ấn tượng với cách tác giả xây dựng nhân vật.',
            'Sách này rất hay, nhưng giá hơi cao.',
            'Tôi đã đọc sách này trong một ngày, không thể dừng lại.',
            'Sách này rất phù hợp với những ai yêu thích thể loại tâm lý học.',
            'Tôi rất thích cách tác giả sử dụng ngôn ngữ trong sách này.',
            'Sách này rất hay, nhưng nội dung hơi dài dòng.',
            'Tôi đã mua sách này để tặng bạn, họ rất thích.',
            'Sách này rất phù hợp với những ai yêu thích thể loại lịch sử.',
            'Tôi rất thích cách tác giả kết thúc câu chuyện.',
            'Sách này rất hay, nhưng có một số phần hơi khó hiểu.',
            'Tôi đã mua sách này để nghiên cứu, rất hài lòng với chất lượng.',
            'Sách này rất phù hợp với những ai yêu thích thể loại triết học.',
            'Tôi rất thích cách tác giả mô tả cảnh vật trong sách này.',
            'Tôi đã mua sách này để làm tài liệu tham khảo, rất hữu ích.',
        ];

        $reviewCount = 0;

        foreach ($users as $user) {
            foreach ($books->random(2) as $book) { // Mỗi user đánh giá 2 cuốn ngẫu nhiên
                if (Review::where('user_id', $user->id)->where('book_id', $book->id)->exists()) {
                    continue;
                }

                Review::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'order_id' => $orders->random()->id,
                    'rating' => rand(3, 5),
                    'comment' => $sampleComments[array_rand($sampleComments)],
                    'status' => 'approved',
                    'admin_response' => null,
                ]);

                $reviewCount++;
            }
        }

        $this->command->info("Đã tạo $reviewCount review mẫu thực tế.");
    }
}
