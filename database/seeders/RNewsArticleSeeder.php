<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RNewsArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Top 10 cuốn sách giúp bạn thay đổi tư duy',
                'thumbnail' => 'news/top-10-sach-thay-doi-tu-duy.jpg',
                'summary' => 'Khám phá những cuốn sách được đánh giá cao về khả năng giúp bạn thay đổi tư duy và phát triển bản thân.',
                'content' => 'Nội dung chi tiết của bài viết về các cuốn sách hay giúp thay đổi tư duy, phân tích vì sao chúng hiệu quả...',
                'category' => 'Books',
                'is_featured' => true,
            ],
            [
                'title' => 'Mẹo bảo quản sách luôn như mới',
                'thumbnail' => 'news/meo-bao-quan-sach.jpg',
                'summary' => 'Những mẹo đơn giản giúp sách của bạn luôn bền đẹp theo thời gian.',
                'content' => 'Chi tiết hướng dẫn cách bảo quản sách, tránh ẩm mốc, cong vênh, giữ màu sắc bìa và giấy luôn đẹp...',
                'category' => 'Tips',
                'is_featured' => false,
            ],
            [
                'title' => 'Xu hướng đọc sách năm 2025',
                'thumbnail' => 'news/xu-huong-doc-sach-2025.jpg',
                'summary' => 'Tổng hợp những xu hướng đọc sách nổi bật trong năm 2025, từ thể loại đến hình thức đọc.',
                'content' => 'Phân tích thị trường sách năm 2025, sự phát triển của sách điện tử, sách audio và xu hướng chọn thể loại của độc giả...',
                'category' => 'Books',
                'is_featured' => true,
            ],
            [
                'title' => 'Những tác giả nổi bật trong làng văn học Việt Nam',
                'thumbnail' => 'news/tac-gia-noi-bat-van-hoc-vn.jpg',
                'summary' => 'Khám phá những tác giả có ảnh hưởng lớn đến văn học Việt Nam hiện đại.',
                'content' => 'Bài viết điểm qua các tác giả tiêu biểu, tác phẩm nổi bật và ảnh hưởng của họ đến nền văn học nước nhà...',
                'category' => 'Authors',
                'is_featured' => false,
            ],
            [
                'title' => 'Cách chọn sách phù hợp với từng độ tuổi',
                'thumbnail' => 'news/chon-sach-theo-do-tuoi.jpg',
                'summary' => 'Hướng dẫn cách chọn sách phù hợp với từng lứa tuổi, từ trẻ em đến người lớn.',
                'content' => 'Chi tiết về các thể loại sách phù hợp với từng độ tuổi, lợi ích của việc đọc sách theo độ tuổi và gợi ý sách hay...',
                'category' => 'Tips',
                'is_featured' => true,
            ],
            [
                'title' => 'Sách hay nên đọc trong mùa hè này',
                'thumbnail' => 'news/sach-hay-mua-he.jpg',
                'summary' => 'Gợi ý những cuốn sách thú vị để bạn đọc trong mùa hè này.',
                'content' => 'Danh sách các cuốn sách phù hợp với khôngg khí mùa hè, từ tiểu thuyết giải trí đến sách phi hư cấu, giúp bạn thư giãn và giải trí.',
                'category' => 'Books',
                'is_featured' => false,
            ],
            [
                'title' => 'Tác động của sách đến sự phát triển tư duy trẻ em',
                'thumbnail' => 'news/tac-dong-sach-den-tu-duy-tre-em.jpg',
                'summary' => 'Phân tích vai trò của sách trong việc phát triển tư duy và kỹ năng của trẻ em.',
                'content' => 'Bài viết nêu rõ tầm quan trọng của việc đọc sách đối với trẻ em, các thể loại sách phù hợp và lợi ích của việc đọc sách từ nhỏ...',
                'category' => 'Children',
                'is_featured' => true,
            ],          
            [
                'title' => 'Những cuốn sách kinh điển không thể bỏ qua',
                'thumbnail' => 'news/sach-kinh-dien-khong-the-bo-qua.jpg',
                'summary' => 'Tổng hợp những cuốn sách kinh điển đã để lại dấu ấn sâu sắc trong nền văn học thế giới.',
                'content' => 'Danh sách các tác phẩm kinh điển, phân tích nội dung và tầm ảnh hưởng của chúng đến văn hóa và xã hội...',
                'category' => 'Books',
                'is_featured' => false,
            ],
            [
                'title' => 'Cách tạo thói quen đọc sách hàng ngày',
                'thumbnail' => 'news/tao-thoi-quen-doc-sach.jpg', 
                'summary' => 'Hướng dẫn cách xây dựng thói quen đọc sách mỗi ngày để nâng cao kiến thức và giải trí.',
                'content' => 'Bài viết chia sẻ các mẹo để tạo thói quen đọc sách, từ việc chọn thời gian đọc, tạo không gian thoải mái đến việc lựa chọn sách phù hợp...',
                'category' => 'Tips',
                'is_featured' => true,
            ],
            [
                'title' => 'Sách điện tử và sách truyền thống: Nên chọn loại nào?',
                'thumbnail' => 'news/sach-dien-tu-va-truyen-thong.jpg',
                'summary' => 'So sánh ưu nhược điểm của sách điện tử và sách truyền thống để giúp bạn lựa chọn phù hợp.',
                'content' => 'Phân tích các ưu điểm của sách điện tử như tính tiện lợi, khả năng lưu trữ lớn, so với sách truyền thống với cảm giác cầm nắm, mùi giấy và trải nghiệm đọc truyền thống...',
                'category' => 'Books',
                'is_featured' => false,
            ],
            [
                'title' => 'Những cuốn sách hay về kỹ năng sống',
                'thumbnail' => 'news/sach-ve-ky-nang-song.jpg',
                'summary' => 'Gợi ý những cuốn sách giúp bạn phát triển kỹ năng sống và cải thiện bản thân.',
                'content' => 'Danh sách các cuốn sách nổi bật về kỹ năng sống, từ quản lý thời gian, giao tiếp, đến phát triển bản thân và tư duy tích cực...',
                'category' => 'Self-Help',
                'is_featured' => true,
            ],
            [
                'title' => 'Tác động của sách đến sức khỏe tâm lý',
                'thumbnail' => 'news/tac-dong-sach-den-suc-khoe-tam-ly.jpg',
                'summary' => 'Khám phá mối liên hệ giữa việc đọc sách và sức khỏe tâm lý của con người.',
                'content' => 'Bài viết phân tích các nghiên cứu cho thấy việc đọc sách có thể giúp giảm căng thẳng, cải thiện tâm trạng và tăng cường khả năng tập trung, từ đó nâng cao sức khỏe tâm lý...',
                'category' => 'Health',
                'is_featured' => false,
            ],
        ];

        foreach ($articles as $article) {
            NewsArticle::create([
                'id' => (string) Str::uuid(),
                'title' => $article['title'],
                'thumbnail' => $article['thumbnail'],
                'summary' => $article['summary'],
                'content' => $article['content'],
                'category' => $article['category'],
                'is_featured' => $article['is_featured'],
            ]);
        }
    }
}
