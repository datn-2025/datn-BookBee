<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Category::query()->withCount('books');

            if (!empty($request['search_name_category'])) {
                $query->where('name', 'like', '%' . $request['search_name_category'] . '%');
            }

            $categories = $query->orderBy('created_at', 'desc')->paginate(10);
            $trashCount = Category::onlyTrashed()->count();

            return view('admin.categories.index', [
                'categories' => $categories,
                'searchName' => $request['search_name_category'] ?? '',
                'trashCount' => $trashCount
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi khi lấy danh sách danh mục: ' . $e->getMessage());
            Toastr::error('Không thể truy vấn danh mục. Vui lòng thử lại sau.');
            return back();
        }
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
                'not_regex:/<.*?>/i'
            ],
            'description' => 'nullable|string|max:800',
            'image'       => 'nullable|image|mimetypes:image/jpeg,image/png,image/jpg,image/gif|max:2048',
        ], [
            'name.required'     => 'Tên danh mục không được để trống.',
            'name.max'          => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique'       => 'Tên danh mục đã tồn tại.',
            'name.not_regex'    => 'Tên danh mục không được chứa thẻ HTML.',
            'description.max'   => 'Mô tả không được vượt quá 800 ký tự.',
            'image.image'       => 'File tải lên phải là hình ảnh.',
            'image.mimetypes'   => 'Ảnh phải có định dạng jpeg, png, jpg, gif.',
            'image.max'         => 'Ảnh không được vượt quá 2MB.',
        ]);

        try {
            $slug = Str::slug($validated['name']);
            $slug = Category::where('slug', $slug)->exists()
                ? $slug . '-' . Str::random(6) : $slug;

            $categoryData = [
                'name'          => $validated['name'],
                'slug'          => $slug,
                'description'   => $validated['description'] ?? null,
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = uniqid('cat_', true) . '.' . $file->extension();
                $path = $file->storeAs('images/admin/categories', $filename, 'public');
                $categoryData['image'] = $path;
            }

            Category::create($categoryData);
            Toastr::success('Thêm mới danh mục thành công!');
            return redirect()->route('admin.categories.index');
        } catch (\Throwable $e) {
            Log::error('Lỗi khi thêm danh mục: ' . $e->getMessage());
            Toastr::error('Đã xảy ra lỗi khi thêm danh mục. Vui lòng thử lại sau.');
            return back()->withInput();
        }
    }

    public function edit($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
                'not_regex:/<.*?>/i'
            ],
            'description'   => 'nullable|string|max:800',
            'image'         => 'nullable|image|mimetypes:image/jpeg,image/png,image/jpg,image/gif|max:2048',
            'remove_image'  => 'nullable|boolean'
        ], [
            'name.required'     => 'Tên danh mục không được để trống.',
            'name.max'          => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique'       => 'Tên danh mục đã tồn tại.',
            'name.not_regex'    => 'Tên danh mục không được chứa thẻ HTML.',
            'description.max'   => 'Mô tả không được vượt quá 800 ký tự.',
            'image.image'       => 'File tải lên phải là hình ảnh.',
            'image.mimetypes'   => 'Ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'image.max'         => 'Ảnh không được vượt quá 2MB.',
        ]);

        try {
            $categoryData = [
                'name'          => $validated['name'],
                'description'   => $validated['description'] ?? null,
            ];

            // Chỉ đổi slug nếu tên thay đổi
            if ($validated['name'] !== $category->name) {
                $baseSlug = Str::slug($validated['name']);
                $slugUnique = Category::where('slug', $baseSlug)
                    ->whereKeyNot($category->id)->exists()
                    ? $baseSlug . '-' . Str::random(6) : $baseSlug;

                $categoryData['slug'] = $slugUnique;
            }

            if (($request->hasFile('image') || $request->boolean('remove_image')) && $category->image) {
                Storage::disk('public')->delete($category->image);
                $categoryData['image'] = null;
            }

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = uniqid('cat_', true) . '.' . $file->extension();
                $categoryData['image'] = $file->storeAs('images/admin/categories', $filename, 'public');
            }

            // Kiểm tra thay đổi
            $original = $category->only(['name', 'description', 'image']);
            $incoming = array_merge($original, Arr::only($categoryData, ['name', 'description', 'image']));

            if ($original === $incoming) {
                Toastr::info('Không có thay đổi nào cho danh mục sách.');
                return redirect()->route('admin.categories.index');
            }

            $category->update($categoryData);

            Toastr::success('Cập nhật danh mục thành công!');
            return redirect()->route('admin.categories.index');
        } catch (\Throwable $e) {
            Log::error('Lỗi khi cập nhật danh mục: ' . $e->getMessage());
            Toastr::error('Đã xảy ra lỗi khi cập nhật danh mục. Vui lòng thử lại sau.');
            return back()->withInput();
        }
    }

    public function trash(Request $request)
    {
        try {
            $query = Category::onlyTrashed()->withCount('books');

            if (!empty($request['search_name_category'])) {
                $query->where('name', 'like', '%' . $request['search_name_category'] . '%');
            }

            $deletedCategories = $query->orderBy('deleted_at', 'desc')->paginate(10);

            return view('admin.categories.categories-trash', [
                'deletedCategories' => $deletedCategories,
                'searchName' => $request['search_name_category'] ?? ''
            ]);
        } catch (\Throwable $e) {
            Log::error('Lỗi trong hàm trash: ' . $e->getMessage());
            Toastr::error('Đã xảy ra lỗi khi truy vấn danh mục. Vui lòng thử lại sau.');
            return back();
        }
    }

    public function destroy($slug)
    {
        try {
            $category = Category::where('slug', $slug)->firstOrFail();
            $category->delete();
            Toastr::success('Đã tạm thời xoá danh mục thành công.');
        } catch (\Throwable $e) {
            Log::error('Lỗi khi xoá danh mục: ' . $e->getMessage());
            Toastr::error('Không thể xoá danh mục. Vui lòng thử lại sau.');
        }

        return back();
    }

    public function restore($slug)
    {
        try {
            $category = Category::onlyTrashed()->where('slug', $slug)->firstOrFail();
            $category->restore();
            Toastr::success('Danh mục đã được khôi phục thành công.');
        } catch (\Throwable $e) {
            Log::error('Lỗi khi khôi phục danh mục: ' . $e->getMessage());
            Toastr::error('Không thể khôi phục danh mục. Vui lòng thử lại sau.');
        }

        return back();
    }

    public function forceDelete($slug)
    {
        try {
            $category = Category::withTrashed()->where('slug', $slug)->firstOrFail();

            if ($category->books()->count() > 0) {
                Toastr::warning('Không thể xoá vĩnh viễn danh mục đang có sách. Vui lòng gán sách cho danh mục khác hoặc xoá mềm.');
                return back();
            }

            $category->forceDelete();
            Toastr::success('Danh mục đã được xoá vĩnh viễn.');
        } catch (\Throwable $e) {
            Log::error('Lỗi khi xoá vĩnh viễn danh mục: ' . $e->getMessage());
            Toastr::error('Không thể xoá vĩnh viễn danh mục. Vui lòng thử lại sau.');
        }

        return back();
    }
}
