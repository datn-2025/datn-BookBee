<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Book;
use App\Models\NewsArticle;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
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
        return view('clients.home', compact('books', 'categories', 'featuredBooks', 'latestBooks', 'bestReviewedBooks', 'saleBooks', 'reviews', 'articles'));
    }

    public function show($slug)
{
    $book = Book::with([
        'author', 'category', 'brand', 'formats', 'images', 'reviews.user', 'attributeValues.attribute', 'summary'
    ])->where('slug', $slug)->firstOrFail();

    $relatedBooks = Book::where('category_id', $book->category_id)
        ->where('id', '!=', $book->id)
        ->with(['images', 'authors', 'formats'])
        ->take(4)
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

    return view('clients.show', compact('book', 'relatedBooks', 'shareButtons'));
}

}
