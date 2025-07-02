<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;

class NewsArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsArticle::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        // Filter by featured status
        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }
        $articles = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $categories = NewsArticle::distinct()->pluck('category');

        return view('admin.news.index', compact('articles', 'categories'));
    }

    public function create()
    {
        $categories = NewsArticle::distinct()->pluck('category');
        return view('admin.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:news_articles,title',
            'category' => 'required|string|max:50',
            'summary' => 'required|string|max:200',
            'content' => 'required|string',
            'thumbnail' => 'required|image|max:2048',
            'is_featured' => 'boolean'
        ], [
            'title.required' => 'Tiêu đề bài viết là bắt buộc.',
            'title.unique' => 'Tiêu đề bài viết đã tồn tại.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'category.required' => 'Danh mục là bắt buộc.',
            'summary.required' => 'Tóm tắt bài viết là bắt buộc.',
            'summary.max' => 'Tóm tắt không được vượt quá 200 ký tự.',
            'content.required' => 'Nội dung bài viết là bắt buộc.',
            'thumbnail.required' => 'Ảnh đại diện là bắt buộc.',
            'thumbnail.image' => 'Tệp tải lên phải là ảnh hợp lệ (jpeg, png, bmp, gif, svg hoặc webp).',
            'thumbnail.max' => 'Ảnh không được vượt quá 2MB.'
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            NewsArticle::create($validated);

            Toastr::success('Tin tức đã được tạo thành công!');
            return redirect()->route('admin.news.index');
        } catch (\Exception $e) {
            if (isset($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            Toastr::error('Có lỗi xảy ra khi tạo tin tức!');
            return back()->withInput();
        }
    }

    public function show(NewsArticle $article)
    {
        return view('admin.news.show', compact('article'));
    }

    public function edit(NewsArticle $article)
    {
        $categories = NewsArticle::distinct()->pluck('category');
        return view('admin.news.edit', compact('article', 'categories'));
    }

    public function update(Request $request, NewsArticle $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|unique:news_articles,title,' . $article->id,
            'category' => 'required|string|max:50',
            'summary' => 'required|string|max:200',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
            'is_featured' => 'boolean'
        ], [
            'title.required' => 'Tiêu đề bài viết là bắt buộc.',
            'title.unique' => 'Tiêu đề bài viết đã tồn tại.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'category.required' => 'Danh mục là bắt buộc.',
            'summary.required' => 'Tóm tắt bài viết là bắt buộc.',
            'summary.max' => 'Tóm tắt không được vượt quá 200 ký tự.',
            'content.required' => 'Nội dung bài viết là bắt buộc.',
            'thumbnail.image' => 'Tệp tải lên phải là ảnh hợp lệ (jpeg, png, bmp, gif, svg hoặc webp).',
            'thumbnail.max' => 'Ảnh không được vượt quá 2MB.'
        ]);

        try {
            $hasFile = $request->hasFile('thumbnail');

            // Nếu có ảnh mới thì lưu lại
            if ($hasFile) {
                // Xóa ảnh cũ nếu có
                if ($article->thumbnail) {
                    Storage::disk('public')->delete($article->thumbnail);
                }

                $thumbnailPath = $request->file('thumbnail')->store('articles', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            }

            // Kiểm tra xem có sự thay đổi hay không
            $article->fill($validated);
            $isUpdated = $article->isDirty(); // Kiểm tra xem có thay đổi không

            if ($isUpdated) {
                // Nếu có thay đổi, lưu lại
                $article->save();

                Toastr::success('Tin tức đã được cập nhật thành công!');
            } else {
                // Nếu không có thay đổi
                Toastr::info('Không có thay đổi nào được thực hiện.');
            }

            return redirect()->route('admin.news.index');
        } catch (\Exception $e) {
            // Nếu có lỗi xảy ra, xóa ảnh đã tải lên (nếu có)
            if (isset($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }

            Toastr::error('Có lỗi xảy ra khi cập nhật tin tức!');
            return back()->withInput();
        }
    }

    public function destroy(NewsArticle $article)
    {
        try {
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }

            $article->delete();

            Toastr::success('Tin tức đã được xóa thành công!');
        } catch (\Exception $e) {
            Toastr::error('Có lỗi xảy ra khi xóa tin tức!');
        }

        return redirect()->route('admin.news.index');
    }
}
