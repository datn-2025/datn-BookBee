<?php

namespace App\Http\Controllers\Article;

use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsController extends \App\Http\Controllers\Controller
{
    // Trang danh sách tin tức
    public function index(Request $request)
    {
        // Lấy tin tức với phân trang, ưu tiên tin nổi bật trước
        $news = NewsArticle::orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(10); // Tăng lên 10 để có đủ tin hiển thị
            
        return view('news.index', compact('news'));
    }

    // Trang chi tiết bài viết
    public function show($id)
    {
        $article = NewsArticle::findOrFail($id);
        $related = NewsArticle::where('id', '!=', $id)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();
        return view('news.show', compact('article', 'related'));
    }
}
