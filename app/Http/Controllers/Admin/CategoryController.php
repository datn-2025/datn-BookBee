<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function brand(Request $request)
    {
        $query = Brand::query();
        // Sắp xếp theo mới nhất đến cũ nhất
        $query->orderBy('created_at', 'desc');
        if (!empty($request->search_name)) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }
        $brands = $query->withCount('books')->paginate(10);
        return view('admin.categories.brand', [
            'brands' => $brands
        ]);
    }

    public function BrandCreate()
    {
        return view('admin.categories.brand-create');
    }


    public function BrandStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu',
            'name.unique' => 'Tên thương hiệu đã tồn tại',
            'name.max' => 'Tên thương hiệu không được vượt quá 255 ký tự',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['description'] = strip_tags($data['description']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('brands', $filename, 'public');
            $data['image'] = '/storage/brands/' . $filename;
        }


        Brand::create($data);

        toastr()->success('Thêm thương hiệu mới thành công');
        return redirect()->route('admin.categories.brands.brand');
    }

    public function BrandDestroy($id)
    {
        $brand = Brand::withCount('books')->findOrFail($id);
        if ($brand->books_count > 0) {
            toastr()->error('Không thể xóa thương hiệu vì vẫn còn sách thuộc thương hiệu này!');
            return back();
        }
        $brand->delete();
        toastr()->success('Đã chuyển thương hiệu vào thùng rác!');
        return redirect()->route('admin.categories.brands.brand');
    }

    public function BrandTrash(Request $request)
    {
        $query = Brand::onlyTrashed();
        if (!empty($request->search_name)) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }
        $brands = $query->withCount('books')->paginate(10);
        return view('admin.categories.brand-trash', [
            'brands' => $brands
        ]);
    }

    public function BrandRestore($id)
    {
        $brand = Brand::onlyTrashed()->findOrFail($id);
        $brand->restore();
        toastr()->success('Khôi phục thương hiệu thành công!');
        return redirect()->route('admin.categories.brands.trash');
    }

    public function BrandForceDelete($id)
    {
        $brand = Brand::onlyTrashed()->findOrFail($id);
        if ($brand->books()->count() > 0) {
            toastr()->error('Không thể xóa vĩnh viễn thương hiệu vì vẫn còn sách thuộc thương hiệu này!');
            return back();
        }
        // Xóa file ảnh nếu có
        if ($brand->image && file_exists(public_path($brand->image))) {
            @unlink(public_path($brand->image));
        }
        $brand->forceDelete();
        toastr()->success('Đã xóa vĩnh viễn thương hiệu!');
        return redirect()->route('admin.categories.brands.trash');
    }

    public function BrandEdit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.categories.brand-edit', compact('brand'));
    }

    public function BrandUpdate(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'name.required' => 'Vui lòng nhập tên thương hiệu',
            'name.unique' => 'Tên thương hiệu đã tồn tại',
            'name.max' => 'Tên thương hiệu không được vượt quá 255 ký tự',
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 2MB'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['description'] = strip_tags($data['description']);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($brand->image && file_exists(public_path($brand->image))) {
                @unlink(public_path($brand->image));
            }
            $image = $request->file('image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('brands', $filename, 'public');
            $data['image'] = '/storage/brands/' . $filename;
        }

        $brand->update($data);
        toastr()->success('Cập nhật thương hiệu thành công!');
        return redirect()->route('admin.categories.brands.brand');
    }
}
