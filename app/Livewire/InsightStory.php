<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InsightStory extends Component
{
    // Thời gian phân tích (ngày gần đây)
    public int $periodDays = 30;
    
    // Kết quả insight hiển thị trên UI
    public array $insights = [];

    public function mount(): void
    {
        // __Thiết lập khoảng thời gian__
        $startDate = Carbon::now()->subDays($this->periodDays);

        // __1) Phân bổ doanh thu Ebook vs Vật lý__
        // - Giả định đơn thành công là orders có order_status.name = 'Thành công'
        // - Ebook: book_formats.file_url NOT NULL
        $successOrderIds = DB::table('orders as o')
            ->join('order_statuses as os', 'o.order_status_id', '=', 'os.id')
            ->where('os.name', 'Thành công')
            ->where('o.created_at', '>=', $startDate)
            ->pluck('o.id');

        $salesAgg = DB::table('order_items as oi')
            ->leftJoin('book_formats as bf', 'oi.book_format_id', '=', 'bf.id')
            ->whereIn('oi.order_id', $successOrderIds)
            ->selectRaw('SUM(oi.total) as total_all')
            ->selectRaw('SUM(CASE WHEN bf.file_url IS NOT NULL THEN oi.total ELSE 0 END) as total_ebook')
            ->first();

        $totalAll = (float) ($salesAgg->total_all ?? 0);
        $totalEbook = (float) ($salesAgg->total_ebook ?? 0);
        $totalPhysical = max(0, $totalAll - $totalEbook);
        $ebookPct = $totalAll > 0 ? round(($totalEbook / $totalAll) * 100) : 0;

        $revenueText = $totalAll > 0
            ? "$ebookPct% doanh thu $this->periodDays ngày qua là ebook. Nên đẩy promo ebook phù hợp."
            : 'Chưa có doanh thu trong kỳ. Hãy triển khai chiến dịch thúc đẩy bán hàng.';

        // __2) Thể loại bán chạy__ (dựa trên số lượng item, tránh lệch do giá)
        $topCategory = DB::table('order_items as oi')
            ->join('books as b', 'oi.book_id', '=', 'b.id')
            ->join('categories as c', 'b.category_id', '=', 'c.id')
            ->whereIn('oi.order_id', $successOrderIds)
            ->groupBy('c.id', 'c.name')
            ->select('c.name')
            ->selectRaw('SUM(oi.quantity) as qty')
            ->orderByDesc('qty')
            ->first();

        $hotCategoryText = $topCategory
            ? "Danh mục bán chạy: {$topCategory->name} (trong $this->periodDays ngày)."
            : 'Chưa có danh mục nổi bật trong kỳ.';

        // __3) Khách hàng VIP__ (theo doanh thu đơn thành công)
        $vip = DB::table('orders as o')
            ->join('order_statuses as os', 'o.order_status_id', '=', 'os.id')
            ->join('users as u', 'o.user_id', '=', 'u.id')
            ->where('os.name', 'Thành công')
            ->where('o.created_at', '>=', $startDate)
            ->groupBy('u.id', 'u.name')
            ->select('u.name')
            ->selectRaw('COUNT(o.id) as orders_count')
            ->selectRaw('SUM(o.total_amount) as revenue')
            ->orderByDesc('revenue')
            ->first();

        $vipText = $vip
            ? sprintf(
                'Khách hàng VIP: %s với %d đơn (~%sđ). Nên gửi ưu đãi giữ chân.',
                $vip->name,
                (int) $vip->orders_count,
                number_format((float) $vip->revenue, 0, ',', '.')
            )
            : 'Chưa ghi nhận khách hàng nổi bật trong kỳ.';

        // __4) Cảnh báo tồn kho thấp__
        // - Ưu tiên cảnh báo theo biến thể (book_attribute_values.stock)
        // - Nếu không có, fallback theo stock ở book_formats (vật lý)
        $lowVariant = DB::table('book_attribute_values as bav')
            ->join('books as b', 'bav.book_id', '=', 'b.id')
            ->join('attribute_values as av', 'bav.attribute_value_id', '=', 'av.id')
            ->whereNotNull('bav.stock')
            ->where('bav.stock', '>=', 0)
            ->orderBy('bav.stock', 'asc')
            ->limit(1)
            ->select('b.title', 'av.value as variant', 'bav.stock')
            ->first();

        $lowFormat = DB::table('book_formats as bf')
            ->join('books as b', 'bf.book_id', '=', 'b.id')
            ->whereNull('bf.file_url') // chỉ cảnh báo bản in
            ->whereNotNull('bf.stock')
            ->where('bf.stock', '>=', 0)
            ->orderBy('bf.stock', 'asc')
            ->limit(1)
            ->select('b.title', DB::raw('NULL as variant'), 'bf.stock')
            ->first();

        $warningText = 'Tồn kho ổn định.';
        if ($lowVariant && (int) $lowVariant->stock <= 5) {
            $warningText = sprintf(
                'Cảnh báo: "%s" biến thể "%s" sắp hết (còn %d). Nên nhập thêm.',
                $lowVariant->title,
                $lowVariant->variant,
                (int) $lowVariant->stock
            );
        } elseif ($lowFormat && (int) $lowFormat->stock <= 5) {
            $warningText = sprintf(
                'Cảnh báo: "%s" bản in sắp hết (còn %d). Nên nhập thêm.',
                $lowFormat->title,
                (int) $lowFormat->stock
            );
        }

        // __Gán dữ liệu cho UI__
        $this->insights = [
            'revenue' => [
                'icon' => '📊',
                'title' => 'Doanh thu & loại sách',
                'text'  => $revenueText,
            ],
            'hot_category' => [
                'icon' => '📚',
                'title' => 'Thể loại hot',
                'text'  => $hotCategoryText,
            ],
            'vip' => [
                'icon' => '🏆',
                'title' => 'Khách hàng VIP',
                'text'  => $vipText,
            ],
            'warning' => [
                'icon' => '⚠️',
                'title' => 'Cảnh báo',
                'text'  => $warningText,
            ],
        ];
    }

    public function render()
    {
        return view('livewire.insight-story');
    }
}
