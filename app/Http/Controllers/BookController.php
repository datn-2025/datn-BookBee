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
            ->whereNull('books.deleted_at') // Loại trừ sách đã xóa mềm
            ->leftJoin('author_books', 'books.id', '=', 'author_books.book_id')
            ->leftJoin('authors', 'author_books.author_id', '=', 'authors.id')
            ->leftJoin('brands', 'books.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('book_formats', 'books.id', '=', 'book_formats.book_id')
            ->leftJoin('reviews', 'books.id', '=', 'reviews.book_id')
            ->leftJoin('book_collections', 'books.id', '=', 'book_collections.book_id')
            ->leftJoin('collections', function($join) {
                $join->on('book_collections.collection_id', '=', 'collections.id')
                     ->where('collections.combo_price', '>', 0)
                     ->whereNull('collections.deleted_at')
                     ->where(function($query) {
                         $query->whereNull('collections.start_date')
                               ->orWhere('collections.start_date', '<=', now());
                     })
                     ->where(function($query) {
                         $query->whereNull('collections.end_date')
                               ->orWhere('collections.end_date', '>=', now());
                     });
            })
            ->leftJoin('book_gifts', 'books.id', '=', 'book_gifts.book_id')
            ->select(
                'books.id',
                'books.title',
                'books.slug',
                'books.cover_image',
                'books.status',
                DB::raw('GROUP_CONCAT(DISTINCT authors.name SEPARATOR ", ") as author_name'),
                'brands.name as brand_name',
                'categories.name as category_name',
                DB::raw('MIN(book_formats.price) as min_price'),
                DB::raw('MAX(book_formats.price) as max_price'),
                DB::raw('SUM(CASE WHEN book_formats.format_name NOT LIKE "%ebook%" AND book_formats.stock IS NOT NULL THEN book_formats.stock ELSE 0 END) as physical_stock'),
                DB::raw('MAX(CASE WHEN book_formats.format_name LIKE "%ebook%" THEN 1 ELSE 0 END) as has_ebook'),
                DB::raw('AVG(reviews.rating) as avg_rating'),
                DB::raw('COUNT(DISTINCT collections.id) as has_combo'),
                DB::raw('COUNT(DISTINCT book_gifts.id) as has_gift'),
                DB::raw('GROUP_CONCAT(DISTINCT collections.name SEPARATOR ", ") as combo_names'),
                DB::raw('GROUP_CONCAT(DISTINCT book_gifts.gift_name SEPARATOR ", ") as gift_names')
            )
            ->when($category, fn($query) => $query->where('books.category_id', $category->id))
            ->groupBy('books.id', 'books.title', 'books.slug', 'books.cover_image', 'books.status', 'brands.name', 'categories.name');

        if (!empty($authorIds)) {
            $booksQuery->whereExists(function($query) use ($authorIds) {
                $query->select(DB::raw(1))
                      ->from('author_books as ab')
                      ->whereRaw('ab.book_id = books.id')
                      ->whereIn('ab.author_id', $authorIds);
            });
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
            $booksQuery->where(function($mainQuery) use ($search) {
                $mainQuery->where(function ($q) use ($search) {
                    $q->where('books.title', 'like', "%$search%")
                        ->orWhere('books.description', 'like', "%$search%")
                        ->orWhere('books.isbn', 'like', "%$search%")
                        ->orWhere('brands.name', 'like', "%$search%")
                        ->orWhere('categories.name', 'like', "%$search%");
                })
                ->orWhereExists(function($query) use ($search) {
                    $query->select(DB::raw(1))
                          ->from('author_books as ab')
                          ->join('authors as a', 'ab.author_id', '=', 'a.id')
                          ->whereRaw('ab.book_id = books.id')
                          ->where('a.name', 'like', "%$search%");
                });
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
            ->join('books', function($join) {
                $join->on('author_books.book_id', '=', 'books.id')
                     ->whereNull('books.deleted_at'); // Loại trừ sách đã xóa mềm
            })
            ->when($category, fn($query) => $query->where('books.category_id', $category->id))
            ->select('authors.id', 'authors.name')
            ->distinct()
            ->get();

        $brands = DB::table('brands')
            ->join('books', function($join) {
                $join->on('brands.id', '=', 'books.brand_id')
                     ->whereNull('books.deleted_at'); // Loại trừ sách đã xóa mềm
            })
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

    public function search(Request $request)
    {
        $searchTerm = $request->input('search', '');
        $authorFilter = $request->input('author');
        $brandFilter = $request->input('brand');
        $categoryFilter = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sort = $request->input('sort', 'newest');

        // Kiểm tra có sách nào trong database không
        $totalBooks = DB::table('books')->count();
        if ($totalBooks == 0) {
            // Không có sách nào, trả về kết quả rỗng
            $books = collect([]);
            $combos = collect([]);
            $categories = collect([]);
            $authors = collect([]);
            $brands = collect([]);
        } else {
            // Tạo query tìm kiếm sách
            $booksQuery = DB::table('books')
                ->whereNull('books.deleted_at') // Loại trừ sách đã xóa mềm
                ->leftJoin('author_books', 'books.id', '=', 'author_books.book_id')
                ->leftJoin('authors', 'author_books.author_id', '=', 'authors.id')
                ->leftJoin('brands', 'books.brand_id', '=', 'brands.id')
                ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
                ->leftJoin('book_formats', 'books.id', '=', 'book_formats.book_id')
                ->leftJoin('reviews', 'books.id', '=', 'reviews.book_id')
                ->leftJoin('book_collections', 'books.id', '=', 'book_collections.book_id')
                ->leftJoin('collections', function($join) {
                    $join->on('book_collections.collection_id', '=', 'collections.id')
                         ->where('collections.combo_price', '>', 0)
                         ->whereNull('collections.deleted_at')
                         ->where(function($query) {
                             $query->whereNull('collections.start_date')
                                   ->orWhere('collections.start_date', '<=', now());
                         })
                         ->where(function($query) {
                             $query->whereNull('collections.end_date')
                                   ->orWhere('collections.end_date', '>=', now());
                         });
                })
                ->leftJoin('book_gifts', 'books.id', '=', 'book_gifts.book_id')
                ->select(
                    'books.id',
                    'books.title',
                    'books.slug',
                    'books.cover_image',
                    'books.description',
                    'books.status',
                    DB::raw('GROUP_CONCAT(DISTINCT authors.name SEPARATOR ", ") as author_name'),
                    'brands.name as brand_name',
                    'categories.name as category_name',
                    DB::raw('MIN(book_formats.price) as min_price'),
                    DB::raw('MAX(book_formats.price) as max_price'),
                    DB::raw('MIN(CASE WHEN book_formats.discount > 0 THEN book_formats.price - book_formats.discount ELSE book_formats.price END) as min_sale_price'),
                    DB::raw('MAX(CASE WHEN book_formats.discount > 0 THEN book_formats.price - book_formats.discount ELSE book_formats.price END) as max_sale_price'),
                    DB::raw('MIN(book_formats.discount) as min_discount'),
                    DB::raw('MAX(book_formats.discount) as max_discount'),
                    DB::raw('SUM(CASE WHEN book_formats.format_name NOT LIKE "%ebook%" AND book_formats.stock IS NOT NULL THEN book_formats.stock ELSE 0 END) as physical_stock'),
                    DB::raw('MAX(CASE WHEN book_formats.format_name LIKE "%ebook%" THEN 1 ELSE 0 END) as has_ebook'),
                    DB::raw('AVG(reviews.rating) as avg_rating'),
                    DB::raw('COUNT(DISTINCT collections.id) as has_combo'),
                    DB::raw('COUNT(DISTINCT book_gifts.id) as has_gift'),
                    DB::raw('GROUP_CONCAT(DISTINCT collections.name SEPARATOR ", ") as combo_names'),
                    DB::raw('GROUP_CONCAT(DISTINCT book_gifts.gift_name SEPARATOR ", ") as gift_names')
                )
                ->groupBy(
                    'books.id', 
                    'books.title', 
                    'books.slug', 
                    'books.cover_image', 
                    'books.description',
                    'books.status',
                    'brands.name',
                    'categories.name'
                );

            // Nếu có từ khóa tìm kiếm
            if (!empty($searchTerm)) {
                $booksQuery->where(function($mainQuery) use ($searchTerm) {
                    $mainQuery->where(function($query) use ($searchTerm) {
                        $query->where('books.title', 'like', "%$searchTerm%")
                              ->orWhere('books.description', 'like', "%$searchTerm%")
                              ->orWhere('books.isbn', 'like', "%$searchTerm%")
                              ->orWhere('brands.name', 'like', "%$searchTerm%")
                              ->orWhere('categories.name', 'like', "%$searchTerm%");
                    })
                    ->orWhereExists(function($query) use ($searchTerm) {
                        $query->select(DB::raw(1))
                              ->from('author_books as ab')
                              ->join('authors as a', 'ab.author_id', '=', 'a.id')
                              ->whereRaw('ab.book_id = books.id')
                              ->where('a.name', 'like', "%$searchTerm%");
                    });
                });
            }

            // Áp dụng filters
            if ($categoryFilter) {
                $booksQuery->where('books.category_id', $categoryFilter);
            }

            if ($brandFilter) {
                $booksQuery->where('brands.id', $brandFilter);
            }

            // Filter theo author - cần thực hiện sau GROUP BY
            if ($authorFilter) {
                $booksQuery->whereExists(function($query) use ($authorFilter) {
                    $query->select(DB::raw(1))
                          ->from('author_books as ab')
                          ->whereRaw('ab.book_id = books.id')
                          ->where('ab.author_id', $authorFilter);
                });
            }

            // Filter theo giá
            if ($minPrice) {
                $booksQuery->having(DB::raw('MIN(book_formats.price)'), '>=', $minPrice);
            }

            if ($maxPrice) {
                $booksQuery->having(DB::raw('MAX(book_formats.price)'), '<=', $maxPrice);
            }

            // Sắp xếp
            switch ($sort) {
                case 'price_asc':
                    $booksQuery->orderByRaw('MIN(book_formats.price) ASC');
                    break;
                case 'price_desc':
                    $booksQuery->orderByRaw('MAX(book_formats.price) DESC');
                    break;
                case 'name_asc':
                    $booksQuery->orderBy('books.title', 'ASC');
                    break;
                case 'name_desc':
                    $booksQuery->orderBy('books.title', 'DESC');
                    break;
                case 'oldest':
                    $booksQuery->orderBy('books.created_at', 'ASC');
                    break;
                case 'newest':
                default:
                    $booksQuery->orderBy('books.created_at', 'DESC');
                    break;
            }

            // Lấy tất cả sách phù hợp với điều kiện tìm kiếm
            // Giới hạn 1000 sách để tránh vấn đề hiệu suất
            $books = $booksQuery->limit(1000)->get();

            // Tìm kiếm combos/collections
            $combosQuery = DB::table('collections')
                ->where('status', 'active')
                ->where('combo_price', '>', 0)
                ->whereNull('deleted_at')
                ->where(function($query) {
                    $query->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                })
                ->select(
                    'id',
                    'name',
                    'slug', 
                    'description',
                    'combo_price',
                    'cover_image',
                    'created_at'
                );

            // Tìm kiếm combo theo từ khóa
            if (!empty($searchTerm)) {
                $combosQuery->where(function($query) use ($searchTerm) {
                    $query->where('name', 'like', "%$searchTerm%")
                          ->orWhere('description', 'like', "%$searchTerm%");
                });
            }

            // Filter combo theo giá
            if ($minPrice) {
                $combosQuery->where('combo_price', '>=', $minPrice);
            }
            if ($maxPrice) {
                $combosQuery->where('combo_price', '<=', $maxPrice);
            }

            // Filter combo theo category - check if combo contains books from this category
            if ($categoryFilter) {
                $combosQuery->whereExists(function($query) use ($categoryFilter) {
                    $query->select(DB::raw(1))
                          ->from('book_collections as bc')
                          ->join('books as b', 'bc.book_id', '=', 'b.id')
                          ->whereRaw('bc.collection_id = collections.id')
                          ->where('b.category_id', $categoryFilter)
                          ->whereNull('b.deleted_at');
                });
            }

            // Filter combo theo author - check if combo contains books from this author
            if ($authorFilter) {
                $combosQuery->whereExists(function($query) use ($authorFilter) {
                    $query->select(DB::raw(1))
                          ->from('book_collections as bc')
                          ->join('books as b', 'bc.book_id', '=', 'b.id')
                          ->join('author_books as ab', 'b.id', '=', 'ab.book_id')
                          ->whereRaw('bc.collection_id = collections.id')
                          ->where('ab.author_id', $authorFilter)
                          ->whereNull('b.deleted_at');
                });
            }

            // Filter combo theo brand - check if combo contains books from this brand
            if ($brandFilter) {
                $combosQuery->whereExists(function($query) use ($brandFilter) {
                    $query->select(DB::raw(1))
                          ->from('book_collections as bc')
                          ->join('books as b', 'bc.book_id', '=', 'b.id')
                          ->whereRaw('bc.collection_id = collections.id')
                          ->where('b.brand_id', $brandFilter)
                          ->whereNull('b.deleted_at');
                });
            }

            // Sắp xếp combo
            switch ($sort) {
                case 'price_asc':
                    $combosQuery->orderBy('combo_price', 'ASC');
                    break;
                case 'price_desc':
                    $combosQuery->orderBy('combo_price', 'DESC');
                    break;
                case 'name_asc':
                    $combosQuery->orderBy('name', 'ASC');
                    break;
                case 'name_desc':
                    $combosQuery->orderBy('name', 'DESC');
                    break;
                case 'oldest':
                    $combosQuery->orderBy('created_at', 'ASC');
                    break;
                case 'newest':
                default:
                    $combosQuery->orderBy('created_at', 'DESC');
                    break;
            }

            $combos = $combosQuery->limit(500)->get();
        }

        // Lấy dữ liệu cho filters
        $categories = DB::table('categories')->orderBy('name')->get();
        $authors = DB::table('authors')->orderBy('name')->get();
        $brands = DB::table('brands')->orderBy('name')->get();

        return view('books.search', [
            'books' => $books,
            'combos' => $combos,
            'categories' => $categories,
            'authors' => $authors,
            'brands' => $brands,
            'searchTerm' => $searchTerm,
            'filters' => [
                'author' => $authorFilter,
                'brand' => $brandFilter,
                'category' => $categoryFilter,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'sort' => $sort,
            ],
        ]);
    }
}
