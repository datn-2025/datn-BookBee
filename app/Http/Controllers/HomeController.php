<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Book;
use App\Models\NewsArticle;
use App\Models\Review;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jorenvh\Share\ShareFacade;

class HomeController extends Controller
{
    public function index()

    {
        $books = Book::with('category', 'formats', 'images')
            ->orderBy('publication_date', 'desc')
            ->take(8)
            ->get();

        // $categories = Category::whereHas('books')->with('books')->take(3)->get();
        $categories = Category::withCount('books')
            ->orderBy('books_count', 'desc')
            ->with(['books' => function ($query) {
                $query->with(['formats', 'images'])->latest()->take(8); // Lấy tối đa 8 sản phẩm/danh mục
            }])
            ->take(3)
            ->get();


        $featuredBooks = Book::with(['formats' => function ($q) {
            $q->orderByDesc('price');
        }, 'authors', 'images'])
            ->withMax('formats', 'price')
            ->orderBy('formats_max_price', 'desc')
            ->take(4)
            ->get();


        $latestBooks = Book::with(['authors', 'images'])
            ->orderBy('publication_date', 'desc')
            ->take(4)
            ->get();
        $bestReviewedBooks = Book::with(['authors', 'images', 'formats', 'reviews'])
            ->withMax('reviews', 'rating')
            ->orderBy('reviews_max_rating', 'desc')
            ->take(4)
            ->get();

        $saleBooks = Book::with(['formats' => function ($q) {
            $q->orderByDesc('discount');
        }, 'authors', 'images'])
            ->withMax('formats', 'discount')
            ->orderBy('formats_max_discount', 'desc')
            ->take(4)
            ->get();

        // Lấy 10 đánh giá mới nhất
        $reviews = Review::with('user', 'book')
            ->orderBy('rating', 'desc')
            ->latest()
            ->take(10)
            ->get();
        $articles = NewsArticle::latest()->take(4)->get();

        // Lấy các combo sách (collections có combo_price)
        $combos = \App\Models\Collection::with(['books.images', 'books.formats'])
            ->whereNotNull('combo_price')
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->take(4)
            ->latest()
            ->get();

        // Lấy sách sắp ra mắt (sách có trạng thái "Sắp Ra Mắt")
        $upcomingBooks = Book::with(['authors', 'images', 'formats'])
            ->where('status', 'Sắp Ra Mắt')
            ->orderBy('publication_date', 'asc')
            ->take(4)
            ->get();

        // Lấy thống kê thực tế từ database
        $statistics = $this->getStatistics();

        return view('clients.home', compact('books', 'categories', 'featuredBooks', 'latestBooks', 'bestReviewedBooks', 'saleBooks', 'reviews', 'articles', 'combos', 'upcomingBooks', 'statistics'));
    }

    public function show($slug)

    {
        $book = Book::with([
            'authors',
            'category',
            'brand',
            'formats',
            'images',
            'reviews.user',
            'attributeValues.attribute'
        ])->where('slug', $slug)->firstOrFail();

        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->with(['images', 'authors', 'formats'])
            ->take(4)
            ->get();

        // Lấy payment methods (loại bỏ thanh toán khi nhận hàng cho preorder)
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->whereNotIn('name', ['Thanh toán khi nhận hàng', 'COD', 'Trả tiền mặt khi nhận hàng'])
            ->get();

        // Tạo các nút chia sẻ link sản phẩm
        $shareButtons = ShareFacade::page(
            route('books.show', $book->slug),
            $book->title
        )
            ->facebook()
            ->twitter()
            ->linkedin()
            ->whatsapp()
            ->telegram();

        $bookGifts = $book->gifts()->get();
        return view('clients.show', compact('book', 'relatedBooks', 'shareButtons', 'bookGifts', 'paymentMethods'));
    }

    /**
     * Hiển thị chi tiết combo sách
     */
    public function showCombo($slug)
    {
        $combo = \App\Models\Collection::with(['books.authors', 'books.images', 'books.formats'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $relatedCombos = \App\Models\Collection::where('id', '!=', $combo->id)
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->take(4)
            ->get();

        return view('clients.show', compact('combo', 'relatedCombos'));
    }

    /**
     * Lấy thống kê thực tế từ database
     */
    private function getStatistics()
    {
        try {
            // Lấy số khách hàng (users có role là 'user')
            $totalCustomers = User::whereHas('role', function($q) {
                $q->where('name', 'user');
            })->count();

            // Lấy tổng số sách đã bán từ các đơn hàng thành công
            $totalBooksSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
                ->where('order_statuses.name', 'Thành công')
                ->sum('order_items.quantity');

            // Tính trung bình thời gian giao hàng (từ đặt đến thành công)
            $avgDeliveryTime = Order::join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
                ->where('order_statuses.name', 'Thành công')
                ->whereNotNull('orders.updated_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, orders.created_at, orders.updated_at)) as avg_hours')
                ->value('avg_hours');

            $avgDeliveryHours = $avgDeliveryTime && $avgDeliveryTime > 0 ? round($avgDeliveryTime) : 48;

            // Đánh giá trung bình của tất cả sách
            $avgRating = Review::avg('rating');
            $qualityPercentage = $avgRating ? round(($avgRating / 5) * 100) : 100;

            // Kiểm tra xem có dữ liệu thực tế không
            $hasRealData = $totalCustomers > 0 || $totalBooksSold > 0;

            return [
                'customers' => max($totalCustomers, 0),
                'books_sold' => max($totalBooksSold, 0),
                'delivery_hours' => max($avgDeliveryHours, 1),
                'quality_percentage' => max($qualityPercentage, 1),
                'has_real_data' => $hasRealData
            ];
        } catch (\Exception $e) {
            // Log lỗi để debug
            Log::error('Error getting statistics: ' . $e->getMessage());
            
            // Nếu có lỗi, ẩn phần thống kê
            return [
                'customers' => 0,
                'books_sold' => 0,
                'delivery_hours' => 48,
                'quality_percentage' => 100,
                'has_real_data' => false
            ];
        }
    }

    public function about()
    {
        return view('about');
    }

    public function combos(\Illuminate\Http\Request $request)
    {
        $query = \App\Models\Collection::with(['books' => function ($query) {
            $query->with(['formats', 'images', 'authors']);
        }])->where('status', 'active');

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('combo_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('combo_price', '<=', $request->max_price);
        }

        // Filter by discount percentage
        if ($request->filled('discount')) {
            $query->whereRaw('((SELECT SUM(COALESCE((SELECT MAX(price) FROM book_formats WHERE book_id = books.id), 0)) FROM books INNER JOIN book_collections ON books.id = book_collections.book_id WHERE book_collections.collection_id = collections.id) - combo_price) / (SELECT SUM(COALESCE((SELECT MAX(price) FROM book_formats WHERE book_id = books.id), 0)) FROM books INNER JOIN book_collections ON books.id = book_collections.book_id WHERE book_collections.collection_id = collections.id) * 100 >= ?', [$request->discount]);
        }

        // Filter by number of books
        if ($request->filled('book_count')) {
            $query->whereHas('books', function($q) use ($request) {
                // This is a bit complex, we'll use a simpler approach
            }, '>=', $request->book_count);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('combo_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('combo_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $combos = $query->paginate(12)->withQueryString();

        // Get filter options
        $priceRange = \App\Models\Collection::where('status', 'active')
            ->whereNotNull('combo_price')
            ->selectRaw('MIN(combo_price) as min_price, MAX(combo_price) as max_price')
            ->first();

        return view('combos.index', compact('combos', 'priceRange'));
    }
}
