<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Author::query();

            // Sắp xếp theo mới nhất đến cũ nhất
            $query->orderBy('created_at', 'desc');

            if (!empty($request['search_name'])) {
                $query->where('name', 'like', '%' . $request['search_name'] . '%');
            }

            $authors = $query->withCount('books')->paginate(10);
            $trashCount = Author::onlyTrashed()->count();

            return view('admin.categories.authors', [
                'authors' => $authors,
                'searchName' => $request['search_name'] ?? '',
                'trashCount' => $trashCount
            ]);
        } catch (\Throwable $e) {
            report($e);
            toastr()->error('Lỗi khi truy vấn tác giả. Vui lòng thử lại sau.');
            return back();
        }
    }

    public function trash(Request $request)
    {
        try {
            $query = Author::onlyTrashed();
            
            if (!empty($request['search_name'])) {
                $query->where('name', 'like', '%' . $request['search_name'] . '%');
            }

            $deletedAuthors = $query->withCount('books')->paginate(10);
            // Giữ lại tham số tìm kiếm khi phân trang
            $deletedAuthors->appends(['search_name' => $request['search_name']]);

            return view('admin.categories.authors-trash', [
                'deletedAuthors' => $deletedAuthors,
                'searchName' => $request['search_name'] ?? ''
            ]);
        } catch (\Throwable $e) {
            report($e);
            toastr()->error('Lỗi khi truy vấn tác giả đã xóa. Vui lòng thử lại sau.');
            return back();
        }
    }

    public function destroy(Author $author)
    {
        try {
            // Kiểm tra xem tác giả có sách nào không
            if ($author->books()->count() > 0) {
                toastr()->error('Không thể xóa tác giả đang có sách trong hệ thống.');
                return back();
            }
            
            $author->delete();
            toastr()->success('Tác giả đã được xóa tạm thời thành công.');
            return back();
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Không thể xóa tác giả. Vui lòng thử lại sau.');
        }
    }

    public function restore($id)
    {
        try {
            Author::withTrashed()->findOrFail($id)->restore();
            toastr()->success('Tác giả đã được khôi phục thành công.');
            return back();
        } catch (\Throwable $e) {
            report($e);
            toastr()->error('Không thể khôi phục tác giả. Vui lòng thử lại sau.');
            return back();
        }
    }    
    public function forceDelete($id)
    {
        try {
            $author = Author::withTrashed()->findOrFail($id);
            
            // Kiểm tra xem tác giả có sách nào không
            if ($author->books()->count() > 0) {
                return back()->with('error', 'Không thể xóa vĩnh viễn tác giả đang có sách trong hệ thống. Vui lòng gán sách cho tác giả khác hoặc xóa mềm.');
            }

            $author->forceDelete();
            return back()->with('success', 'Tác giả đã được xóa vĩnh viễn.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Không thể xóa vĩnh viễn tác giả. Vui lòng thử lại sau.');
        }
    }

    public function create()
    {
        return view('admin.categories.authors-create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:authors,name',
                'biography' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'name.required' => 'Vui lòng nhập tên tác giả',
                'name.unique' => 'Tên tác giả đã tồn tại trong hệ thống',
                'name.max' => 'Tên tác giả không được vượt quá 255 ký tự',
                'biography.string' => 'Tiểu sử phải là chuỗi ký tự',
                'image.image' => 'File phải là hình ảnh',
                'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
                'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB'
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $data = $validator->validated();

            // Xử lý upload ảnh nếu có
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('authors', $filename, 'public');
                $data['image'] = '/storage/authors/' . $filename;
            }

            Author::create($data);
            toastr()->success('Thêm tác giả mới thành công');
            return redirect()->route('admin.categories.authors.index');

        } catch (\Throwable $e) {
            report($e);
            toastr()->error('Không thể thêm tác giả. Vui lòng thử lại sau.');
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $author = Author::findOrFail($id);
        return view('admin.categories.authors-edit', compact('author'));
    }

    public function update(Request $request, $id)
    {
        $author = Author::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:authors,name,' . $author->id,
            'biography' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'name.required' => 'Vui lòng nhập tên tác giả',
            'name.unique' => 'Tên tác giả đã tồn tại trong hệ thống',
            'name.max' => 'Tên tác giả không được vượt quá 255 ký tự',
            'biography.string' => 'Tiểu sử phải là chuỗi ký tự',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Xử lý upload ảnh nếu có
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('authors', $filename, 'public');
            $data['image'] = '/storage/authors/' . $filename;
        }

        $author->update($data);
        toastr()->success('Cập nhật tác giả thành công');
        return redirect()->route('admin.categories.authors.index');
    }
}
