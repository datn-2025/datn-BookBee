<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request, $categorySlug = null)
    {
        // Lấy tất cả danh mục dùng cho sidebar
        $categories = DB::table('categories')->get();

        // Lấy danh mục hiện tại nếu có
        $category = null;
        if ($categorySlug) {
            $category = DB::table('categories')->where('slug', $categorySlug)->first();
            if (!$category) {
                abort(404, "Category not found");
            }
        }

        // Lấy filter từ query string
        $authorIds = $request->input('authors', []);
        $brandIds = $request->input('brands', []);
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $minRating = $request->input('min_rating');
        $sort = $request->input('sort', 'newest');

        // Chuyển filter chuỗi sang mảng nếu cần
        if (!is_array($authorIds) && $authorIds !== null) {
            $authorIds = explode(',', $authorIds);
        }
        if (!is_array($brandIds) && $brandIds !== null) {
            $brandIds = explode(',', $brandIds);
        }

        // Tạo query lấy sách
        $booksQuery = DB::table('books')
            ->join('author_books', 'books.id', '=', 'author_books.book_id')
            ->join('authors', 'author_books.author_id', '=', 'authors.id')
            ->join('brands', 'books.brand_id', '=', 'brands.id')
            ->join('book_formats', 'books.id', '=', 'book_formats.book_id')
            ->leftJoin('reviews', 'books.id', '=', 'reviews.book_id')
            ->select(
                'books.id',
                'books.title',
                'books.slug',
                'books.cover_image',
                'authors.name as author_name',
                'brands.name as brand_name',
                DB::raw('MIN(book_formats.price) as min_price'),
                DB::raw('MAX(book_formats.price) as max_price'),
                DB::raw('SUM(CASE WHEN book_formats.format_name NOT LIKE "%ebook%" AND book_formats.stock IS NOT NULL THEN book_formats.stock ELSE 0 END) as physical_stock'),
                DB::raw('MAX(CASE WHEN book_formats.format_name LIKE "%ebook%" THEN 1 ELSE 0 END) as has_ebook'),
                DB::raw('AVG(reviews.rating) as avg_rating')
            )
            ->when($category, fn($query) => $query->where('books.category_id', $category->id))
            ->groupBy('books.id', 'books.title', 'books.slug', 'books.cover_image', 'authors.name', 'brands.name');

        if (!empty($authorIds)) {
            $booksQuery->whereIn('author_books.author_id', $authorIds);
        }

        if (!empty($brandIds)) {
            $booksQuery->whereIn('books.brand_id', $brandIds);
        }

        if ($minPrice !== null) {
            $booksQuery->havingRaw('MIN(book_formats.price) >= ?', [$minPrice]);
        }
        if ($maxPrice !== null) {
            $booksQuery->havingRaw('MAX(book_formats.price) <= ?', [$maxPrice]);
        }

        // Các mốc lọc giá tiền VNĐ (nghìn đồng)
        $priceRanges = [
            '1-10' => [0, 10000],
            '10-50' => [10000, 50000],
            '50-100' => [50000, 100000],
            '100+' => [100000, null],
        ];

        $selectedPriceRange = $request->input('price_range');
        if ($selectedPriceRange && isset($priceRanges[$selectedPriceRange])) {
            [$minPrice, $maxPrice] = $priceRanges[$selectedPriceRange];
            if ($minPrice !== null) {
                $booksQuery->havingRaw('MIN(book_formats.price) >= ?', [$minPrice]);
            }
            if ($maxPrice !== null) {
                $booksQuery->havingRaw('MAX(book_formats.price) <= ?', [$maxPrice]);
            }
        }

        if ($minRating !== null) {
            $booksQuery->havingRaw('AVG(reviews.rating) >= ?', [$minRating]);
        }

        if ($search = $request->input('search')) {
            $booksQuery->where(function ($q) use ($search) {
                $q->where('books.title', 'like', "%$search%")
                    ->orWhere('authors.name', 'like', "%$search%");
            });
        }

        switch ($sort) {
            case 'price_asc':
                $booksQuery->orderBy('min_price', 'asc');
                break;
            case 'price_desc':
                $booksQuery->orderBy('min_price', 'desc');
                break;
            case 'name_asc':
                $booksQuery->orderBy('books.title', 'asc');
                break;
            case 'name_desc':
                $booksQuery->orderBy('books.title', 'desc');
                break;
            case 'newest':
            default:
                $booksQuery->orderBy('books.created_at', 'desc');
                break;
        }

        $books = $booksQuery->paginate(6)->withQueryString();

        $authors = DB::table('authors')
            ->join('author_books', 'authors.id', '=', 'author_books.author_id')
            ->join('books', 'author_books.book_id', '=', 'books.id')
            ->when($category, fn($query) => $query->where('books.category_id', $category->id))
            ->select('authors.id', 'authors.name')
            ->distinct()
            ->get();

        $brands = DB::table('brands')
            ->join('books', 'brands.id', '=', 'books.brand_id')
            ->when($category, fn($query) => $query->where('books.category_id', $category->id))
            ->select('brands.id', 'brands.name')
            ->distinct()
            ->get();

        return view('books.index', [
            'categories' => $categories,
            'category' => $category,
            'books' => $books,
            'authors' => $authors,
            'brands' => $brands,
            'filters' => [
                'authors' => $authorIds,
                'brands' => $brandIds,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'min_rating' => $minRating,
                'sort' => $sort,
            ],
        ]);
    }
}
