<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Brand;
use App\Models\Author;
use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\BookAttributeValue;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Admin\GiftController;
use App\Models\BookGift;
use Illuminate\Support\Facades\Validator;

class AdminBookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query()
            ->with([
                'category:id,name',
                'author:id,name',
                'brand:id,name',
                'formats:id,book_id,format_name,price,discount,stock',
                'images:id,book_id,image_url'
            ])
            ->withSum(['formats as total_stock' => function ($query) {
                $query->whereIn('format_name', ['Bìa mềm', 'Bìa cứng']);
            }], 'stock');

        // Tìm kiếm theo tiêu đề sách hoặc mã ISBN
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('isbn', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Lọc theo thương hiệu (brand)
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Lọc theo tác giả
        if ($request->filled('author')) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('authors.id', $request->author);
            });
        }

        // Lọc theo khoảng trang
        if ($request->filled('min_pages')) {
            $query->where('page_count', '>=', $request->min_pages);
        }
        if ($request->filled('max_pages')) {
            $query->where('page_count', '<=', $request->max_pages);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo khoảng giá của format
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('formats', function ($q) use ($request) {
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', $request->max_price);
                }
            });
        }

        // Sắp xếp theo số trang hoặc giá
        if ($request->filled('sort')) {
            match ($request->sort) {
                'pages_asc' => $query->orderBy('page_count', 'asc'),
                'pages_desc' => $query->orderBy('page_count', 'desc'),
                'price_asc' => $query->withMin('formats as min_price', 'price')
                    ->orderBy('min_price', 'asc'),
                'price_desc' => $query->withMax('formats as max_price', 'price')
                    ->orderBy('max_price', 'desc'),
                default => $query->orderBy('id', 'desc')
            };
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $books = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $categories = Category::all();
        $brands = Brand::all();
        $authors = Author::all();
        return view('admin.books.index', compact('books', 'categories', 'brands', 'authors'));
    }

    public function create()
    {
        $categories = Category::whereNull('deleted_at')->get();
        $brands = Brand::whereNull('deleted_at')->get();
        $authors = Author::whereNull('deleted_at')->get();
        $attributes = Attribute::with('values')->get();
        $allBooks = Book::all();
        return view('admin.books.create', compact('categories', 'brands', 'authors', 'attributes', 'allBooks'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // Kiểm tra slug trùng lặp trước khi validate
        $title = $request->input('title');
        if ($title) {
            $slug = Str::slug($title);
            $slugExists = Book::where('slug', $slug)->exists();
            if ($slugExists) {
                // Trả về lỗi validate cho trường title thay vì toastr
                return back()->withInput()->withErrors(['title' => 'Tiêu đề sách đã tồn tại. Vui lòng chọn tiêu đề khác.']);
            }
        }

        // Validation với thông báo tùy chỉnh
        $validator = Validator::make($request->all(),[
            'title' => 'required|string|max:255',
            // Slug sẽ được check unique thủ công phía dưới
            'description' => 'nullable|string',
            'isbn' => 'required|string|max:20', // Bắt buộc nhập ISBN
            'page_count' => 'required|integer', // Bắt buộc nhập số trang
            'attribute_values' => 'nullable|array',
            'attribute_values.*.id' => 'required|distinct|exists:attribute_values,id',
            'attribute_values.*.extra_price' => 'nullable|numeric|min:0',
            'has_physical' => 'boolean',
            'formats.physical.price' => 'required_if:has_physical,1|numeric|min:0',
            'formats.physical.discount' => 'nullable|numeric|min:0|max:100',
            'formats.physical.stock' => 'required_if:has_physical,1|integer|min:0',
            'has_ebook' => 'boolean',
            'formats.ebook.price' => 'required_if:has_ebook,1|numeric|min:0',
            'formats.ebook.discount' => 'nullable|numeric|min:0|max:100',
            'formats.ebook.file' => 'required_if:has_ebook,1|mimes:pdf,epub|max:50000',
            'formats.ebook.sample_file' => 'nullable|mimes:pdf,epub|max:10000',
            'formats.ebook.allow_sample_read' => 'boolean',
            'status' => 'required|string|max:50',
            'category_id' => 'required|uuid|exists:categories,id',
            'author_ids' => 'required|array|min:1', // Bắt buộc nhập tác giả
            'author_ids.*' => 'required|uuid|exists:authors,id',
            'brand_id' => 'required|uuid|exists:brands,id',
            'publication_date' => 'required|date', // Bắt buộc nhập ngày xuất bản
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề sách',
            'title.unique' => 'Tiêu đề sách đã tồn tại.',
            'isbn.required' => 'Vui lòng nhập mã ISBN',
            'page_count.required' => 'Vui lòng nhập số trang',
            'page_count.integer' => 'Số trang phải là số nguyên',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.uuid' => 'Danh mục không hợp lệ',
            'author_ids.required' => 'Vui lòng chọn ít nhất một tác giả',
            'author_ids.min' => 'Vui lòng chọn ít nhất một tác giả',
            'author_ids.*.uuid' => 'Tác giả không hợp lệ',
            'brand_id.required' => 'Vui lòng chọn thương hiệu',
            'brand_id.uuid' => 'Thương hiệu không hợp lệ',
            'status.required' => 'Vui lòng chọn trạng thái',
            'cover_image.required' => 'Vui lòng chọn ảnh bìa cho sách',
            'cover_image.image' => 'File ảnh bìa không hợp lệ',
            'cover_image.max' => 'Kích thước ảnh bìa không được vượt quá 2MB',
            'formats.physical.price.required_if' => 'Vui lòng nhập giá bán cho sách vật lý',
            'formats.physical.price.numeric' => 'Giá bán sách vật lý phải là số',
            'formats.physical.stock.required_if' => 'Vui lòng nhập số lượng cho sách vật lý',
            'formats.physical.stock.integer' => 'Số lượng sách vật lý phải là số nguyên',
            'formats.ebook.price.required_if' => 'Vui lòng nhập giá bán cho ebook',
            'formats.ebook.price.numeric' => 'Giá bán ebook phải là số',
            'formats.ebook.file.required_if' => 'Vui lòng chọn file ebook',
            'formats.ebook.file.mimes' => 'File ebook phải có định dạng PDF hoặc EPUB',
            'formats.ebook.file.max' => 'Kích thước file ebook không được vượt quá 50MB',
            'attribute_values.*.id.distinct' => 'Không được chọn trùng thuộc tính cho sách',
            'publication_date.required' => 'Vui lòng nhập ngày xuất bản',
            'publication_date.date' => 'Ngày xuất bản không hợp lệ',
        ]);
        // dd($request->all());
        if($validator->fails()) {
            // Trả về lỗi validate về form, không toastr
            return back()->withInput()->withErrors($validator);
        }
        // Kiểm tra xem ít nhất một định dạng sách được chọn
        if (!$request->boolean('has_physical') && !$request->boolean('has_ebook')) {
            return back()->withInput()->withErrors([
                'format' => 'Vui lòng chọn ít nhất một định dạng sách (Sách vật lý hoặc Ebook)'
            ]);
        }

        $data = $request->only([
            'title',
            'description',
            'brand_id',
            'category_id',
            'status',
            'isbn',
            'publication_date',
            'page_count'
        ]);

        // dd($request->hasFile('cover_image'));

        $slug = Str::slug($data['title']);
        $data['slug'] = $slug;



        $book = Book::create($data);
        // Xử lý ảnh chính
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('books', 'public');
            $book->cover_image = $coverImagePath;
            $book->save();
        }

        // Xử lý ảnh phụ
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('books/thumbnail', 'public');
                // Lưu ảnh vào bảng book_images
                $book->images()->create([
                    'image_url' => $path
                ]);
            }
        }

        // Lưu định dạng sách vật lý
        if ($request->boolean('has_physical')) {
            $book->formats()->create([
                'format_name' => 'Sách Vật Lý',
                'price' => $request->input('formats.physical.price'),
                'discount' => $request->input('formats.physical.discount'),
                'stock' => $request->input('formats.physical.stock'),
            ]);
        }

        // Lưu định dạng ebook
        if ($request->boolean('has_ebook')) {
            $ebookFormat = [
                'format_name' => 'Ebook',
                'price' => $request->input('formats.ebook.price'),
                'discount' => $request->input('formats.ebook.discount'),
                'allow_sample_read' => $request->boolean('formats.ebook.allow_sample_read'),
            ];

            // Upload file ebook chính
            if ($request->hasFile('formats.ebook.file')) {
                $ebookFile = $request->file('formats.ebook.file');
                $ebookFilename = time() . '_' . Str::slug(pathinfo($ebookFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ebookFile->getClientOriginalExtension();
                $ebookPath = $ebookFile->storeAs('ebooks', $ebookFilename, 'public');
                $ebookFormat['file_url'] = $ebookPath;
            }

            // Upload file xem thử
            if ($request->hasFile('formats.ebook.sample_file')) {
                $sampleFile = $request->file('formats.ebook.sample_file');
                $sampleFilename = time() . '_sample_' . Str::slug(pathinfo($sampleFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $sampleFile->getClientOriginalExtension();
                $samplePath = $sampleFile->storeAs('ebook-samples', $sampleFilename, 'public');
                $ebookFormat['sample_file_url'] = $samplePath;
            }

            $book->formats()->create($ebookFormat);
        }

        // Lưu thuộc tính và giá thêm
        if ($request->filled('attribute_values')) {
            foreach ($request->attribute_values as $valueId => $data) {
                // Sử dụng create thay vì attach để đảm bảo UUID được tạo tự động
                BookAttributeValue::create([
                    'book_id' => $book->id,
                    'attribute_value_id' => $data['id'],
                    'extra_price' => $data['extra_price'] ?? 0
                ]);
            }
        }

        // Lưu quà tặng nếu có (theo form hiện tại chỉ nhập 1 quà tặng)
        if ($request->filled('gift_name')) {
            $giftData = [
                'book_id' => $book->id,
                'gift_name' => $request->input('gift_name'),
                'gift_description' => $request->input('gift_description'),
                'quantity' => $request->input('quantity', 0),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ];
            if ($request->hasFile('gift_image')) {
                $giftData['gift_image'] = $request->file('gift_image')->store('gifts', 'public');
            }
            \App\Models\BookGift::create($giftData);
        }

        // Gán tác giả cho sách
//        $book->author()->sync($request->input('author_ids', []));
        if ($request->has('author_ids')) {
            foreach ($request->input('author_ids') as $authorId) {
                \DB::table('author_books')->insert([
                    'id' => Str::uuid(), // Tạo UUID mới
                    'book_id' => $book->id,
                    'author_id' => $authorId,
                ]);
            }
        }
        Toastr::success('Thêm sách thành công!');
        return redirect()->route('admin.books.index');
    }

    public function show($id, $slug)
    {
        $book = Book::with([
            'category:id,name',
            'author:id,name',
            'brand:id,name',
            'formats:id,book_id,format_name,price,discount,stock,file_url,sample_file_url,allow_sample_read',
            'images:id,book_id,image_url',
            'attributeValues.attribute',
            'reviews.user:id,name,email'
        ])->findOrFail($id);

        // Calculate average rating
        $averageRating = $book->reviews->avg('rating');
        $reviewCount = $book->reviews->count();

        // Group attributes by attribute name
        $attributes = [];
        foreach ($book->attributeValues as $attributeValue) {
            $attributeName = $attributeValue->attribute->name;
            if (!isset($attributes[$attributeName])) {
                $attributes[$attributeName] = [];
            }
            $attributes[$attributeName][] = [
                'value' => $attributeValue->value,
                'extra_price' => $attributeValue->pivot->extra_price ?? 0
            ];
        }

        return view('admin.books.show', compact('book', 'attributes', 'averageRating', 'reviewCount'));
    }

    public function edit($id, $slug)
    {
        $book = Book::with([
            'formats',
            'images',
            'attributeValues',
            'author' // sửa lại đúng quan hệ many-to-many
        ])->findOrFail($id);

        $categories = Category::all();
        $brands = Brand::all();
        $authors = Author::all();
        $attributes = Attribute::with('values')->get();

        // Lấy định dạng sách vật lý nếu có
        $physicalFormat = $book->formats->where('format_name', 'Sách Vật Lý')->first();

        // Lấy định dạng ebook nếu có
        $ebookFormat = $book->formats->where('format_name', 'Ebook')->first();

        // Chuẩn bị dữ liệu thuộc tính đã chọn
        $selectedAttributeValues = [];
        foreach ($book->attributeValues as $attributeValue) {
            $selectedAttributeValues[$attributeValue->id] = [
                'id' => $attributeValue->id,
                'extra_price' => $attributeValue->pivot->extra_price ?? 0
            ];
        }

        return view('admin.books.edit', compact(
            'book',
            'categories',
            'brands',
            'authors',
            'attributes',
            'physicalFormat',
            'ebookFormat',
            'selectedAttributeValues'
        ));
    }

    public function update(Request $request, $id, $slug)
    {
        $book = Book::findOrFail($id);

        // Validation rules giống store, chỉ khác ảnh bìa/file ebook là nullable
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isbn' => 'required|string|max:20', // Bắt buộc nhập ISBN
            'page_count' => 'required|integer', // Bắt buộc nhập số trang
            'attribute_values' => 'nullable|array',
            'attribute_values.*.id' => 'required|distinct|exists:attribute_values,id',
            'attribute_values.*.extra_price' => 'nullable|numeric|min:0',
            'has_physical' => 'boolean',
            'formats.physical.price' => 'required_if:has_physical,1|numeric|min:0',
            'formats.physical.discount' => 'nullable|numeric|min:0|max:100',
            'formats.physical.stock' => 'required_if:has_physical,1|integer|min:0',
            'has_ebook' => 'boolean',
            'formats.ebook.price' => 'required_if:has_ebook,1|numeric|min:0',
            'formats.ebook.discount' => 'nullable|numeric|min:0|max:100',
            'formats.ebook.file' => 'nullable|mimes:pdf,epub|max:50000', // khác store: required_if -> nullable
            'formats.ebook.sample_file' => 'nullable|mimes:pdf,epub|max:10000',
            'formats.ebook.allow_sample_read' => 'boolean',
            'status' => 'required|string|max:50',
            'category_id' => 'required|uuid|exists:categories,id',
            'author_ids' => 'required|array|min:1', // Bắt buộc nhập tác giả
            'author_ids.*' => 'required|uuid|exists:authors,id',
            'brand_id' => 'required|uuid|exists:brands,id',
            'publication_date' => 'required|date', // Bắt buộc nhập ngày xuất bản
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // khác store: required -> nullable
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề sách',
            'title.unique' => 'Tiêu đề sách đã tồn tại.',
            'isbn.required' => 'Vui lòng nhập mã ISBN',
            'page_count.required' => 'Vui lòng nhập số trang',
            'page_count.integer' => 'Số trang phải là số nguyên',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.uuid' => 'Danh mục không hợp lệ',
            'author_ids.required' => 'Vui lòng chọn ít nhất một tác giả',
            'author_ids.min' => 'Vui lòng chọn ít nhất một tác giả',
            'author_ids.*.uuid' => 'Tác giả không hợp lệ',
            'brand_id.required' => 'Vui lòng chọn thương hiệu',
            'brand_id.uuid' => 'Thương hiệu không hợp lệ',
            'status.required' => 'Vui lòng chọn trạng thái',
            'cover_image.image' => 'File ảnh bìa không hợp lệ',
            'cover_image.max' => 'Kích thước ảnh bìa không được vượt quá 2MB',
            'formats.physical.price.required_if' => 'Vui lòng nhập giá bán cho sách vật lý',
            'formats.physical.price.numeric' => 'Giá bán sách vật lý phải là số',
            'formats.physical.stock.required_if' => 'Vui lòng nhập số lượng cho sách vật lý',
            'formats.physical.stock.integer' => 'Số lượng sách vật lý phải là số nguyên',
            'formats.ebook.price.required_if' => 'Vui lòng nhập giá bán cho ebook',
            'formats.ebook.price.numeric' => 'Giá bán ebook phải là số',
            'formats.ebook.file.mimes' => 'File ebook phải có định dạng PDF hoặc EPUB',
            'formats.ebook.file.max' => 'Kích thước file ebook không được vượt quá 50MB',
            'attribute_values.*.id.distinct' => 'Không được chọn trùng thuộc tính cho sách',
            'publication_date.required' => 'Vui lòng nhập ngày xuất bản',
            'publication_date.date' => 'Ngày xuất bản không hợp lệ',
        ]);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }
        // Kiểm tra xem ít nhất một định dạng sách được chọn
        if (!$request->boolean('has_physical') && !$request->boolean('has_ebook')) {
            return back()->withInput()->withErrors([
                'format' => 'Vui lòng chọn ít nhất một định dạng sách (Sách vật lý hoặc Ebook)'
            ]);
        }

        $data = $request->only([
            'title',
            'description',
            'brand_id',
            'category_id',
            'status',
            'isbn',
            'publication_date',
            'page_count'
        ]);

        // Kiểm tra slug trùng lặp trước khi validate
        $title = $request->input('title');
        if ($title) {
            $slug = Str::slug($title);
            $data['slug'] = $slug;
            $slugExists = Book::where('slug', $slug)->where('id', '!=', $id)->exists();
            if ($slugExists) {
                // Trả về lỗi validate cho trường title thay vì toastr
                return back()->withInput()->withErrors(['title' => 'Tiêu đề sách đã tồn tại. Vui lòng chọn tiêu đề khác.']);
            }
        }

        // Xử lý ảnh chính nếu có cập nhật
        if ($request->hasFile('cover_image')) {
            // Xóa ảnh cũ nếu có
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $coverImage = $request->file('cover_image');
            $coverImagePath = $coverImage->store('books', 'public');
            $data['cover_image'] = $coverImagePath;
        }

        // Cập nhật thông tin sách
        $book->update($data);

        // Xử lý ảnh phụ nếu có cập nhật
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('books/thumbnail', 'public');
                $book->images()->create([
                    'image_url' => $path
                ]);
            }
        }

        // Xử lý xóa ảnh nếu có yêu cầu
        if ($request->filled('delete_images')) {
            $imageIds = $request->input('delete_images');
            $imagesToDelete = $book->images()->whereIn('id', $imageIds)->get();

            foreach ($imagesToDelete as $image) {
                if (Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
                $image->delete();
            }
        }

        // Cập nhật định dạng sách vật lý
        if ($request->boolean('has_physical')) {
            $physicalFormat = $book->formats()->where('format_name', 'Sách Vật Lý')->first();

            $physicalData = [
                'format_name' => 'Sách Vật Lý',
                'price' => $request->input('formats.physical.price'),
                'discount' => $request->input('formats.physical.discount'),
                'stock' => $request->input('formats.physical.stock'),
            ];

            if ($physicalFormat) {
                $physicalFormat->update($physicalData);
            } else {
                $book->formats()->create($physicalData);
            }
        } else {
            // Xóa định dạng sách vật lý nếu không còn sử dụng
            $book->formats()->where('format_name', 'Sách Vật Lý')->delete();
        }

        // Cập nhật định dạng ebook
        if ($request->boolean('has_ebook')) {
            $ebookFormat = $book->formats()->where('format_name', 'Ebook')->first();

            $ebookData = [
                'format_name' => 'Ebook',
                'price' => $request->input('formats.ebook.price'),
                'discount' => $request->input('formats.ebook.discount'),
                'allow_sample_read' => $request->boolean('formats.ebook.allow_sample_read'),
            ];

            // Upload file ebook chính nếu có cập nhật
            if ($request->hasFile('formats.ebook.file')) {
                // Xóa file cũ nếu có
                if ($ebookFormat && $ebookFormat->file_url && Storage::disk('public')->exists($ebookFormat->file_url)) {
                    Storage::disk('public')->delete($ebookFormat->file_url);
                }

                $ebookFile = $request->file('formats.ebook.file');
                $ebookFilename = time() . '_' . Str::slug(pathinfo($ebookFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ebookFile->getClientOriginalExtension();
                $ebookPath = $ebookFile->storeAs('ebooks', $ebookFilename, 'public');
                $ebookData['file_url'] = $ebookPath;
            }

            // Upload file xem thử nếu có cập nhật
            if ($request->hasFile('formats.ebook.sample_file')) {
                // Xóa file cũ nếu có
                if ($ebookFormat && $ebookFormat->sample_file_url && Storage::disk('public')->exists($ebookFormat->sample_file_url)) {
                    Storage::disk('public')->delete($ebookFormat->sample_file_url);
                }

                $sampleFile = $request->file('formats.ebook.sample_file');
                $sampleFilename = time() . '_sample_' . Str::slug(pathinfo($sampleFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $sampleFile->getClientOriginalExtension();
                $samplePath = $sampleFile->storeAs('ebook-samples', $sampleFilename, 'public');
                $ebookData['sample_file_url'] = $samplePath;
            }

            if ($ebookFormat) {
                $ebookFormat->update($ebookData);
            } else {
                $book->formats()->create($ebookData);
            }
        } else {
            // Xóa định dạng ebook nếu không còn sử dụng
            $book->formats()->where('format_name', 'Ebook')->delete();
        }

        // Cập nhật thuộc tính và giá thêm
        // Xóa tất cả các liên kết thuộc tính hiện tại
        $book->attributeValues()->detach();

        // Thêm lại các thuộc tính mới
        if ($request->filled('attribute_values')) {
            foreach ($request->attribute_values as $valueId => $data) {
                BookAttributeValue::create([
                    'book_id' => $book->id,
                    'attribute_value_id' => $data['id'],
                    'extra_price' => $data['extra_price'] ?? 0
                ]);
            }
        }

        // Cập nhật quà tặng
        if ($request->has('gifts')) {
            // Xóa quà tặng cũ
            $book->gifts()->delete();
            foreach ($request->gifts as $gift) {
                $giftData = [
                    'book_id' => $book->id,
                    'gift_name' => $gift['gift_name'] ?? null,
                    'gift_description' => $gift['gift_description'] ?? null,
                    'gift_image' => null,
                    'quantity' => $gift['quantity'] ?? null,
                    'start_date' => $gift['start_date'] ?? null,
                    'end_date' => $gift['end_date'] ?? null,
                ];
                if (isset($gift['gift_image']) && is_file($gift['gift_image'])) {
                    $giftData['gift_image'] = $gift['gift_image']->store('gifts', 'public');
                }
                BookGift::create($giftData);
            }
        }

        // Cập nhật danh sách tác giả
        $book->authors()->sync($request->input('author_ids', []));

        Toastr::success('Cập nhật sách thành công!');
        return redirect()->route('admin.books.index');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        Toastr::success('Sách đã được chuyển vào thùng rác!', 'Thành công');
        return redirect()->route('admin.books.index');
    }

    public function trash(Request $request)
    {
        $query = Book::onlyTrashed()
            ->with([
                'category:id,name',
                'author:id,name',
                'brand:id,name',
                'formats:id,book_id,format_name,price,discount,stock',
                'images:id,book_id,image_url'
            ]);

        // Tìm kiếm theo tiêu đề sách hoặc mã ISBN
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('isbn', 'like', '%' . $request->search . '%');
            });
        }

        $trashedBooks = $query->orderBy('deleted_at', 'desc')->paginate(10)->withQueryString();
        return view('admin.books.trash', compact('trashedBooks'));
    }

    public function restore($id)
    {
        $book = Book::onlyTrashed()->findOrFail($id);
        $book->restore();

        Toastr::success('Sách đã được khôi phục thành công!', 'Thành công');
        return redirect()->route('admin.books.trash');
    }

    public function forceDelete($id)
    {
        $book = Book::onlyTrashed()->findOrFail($id);

        // Kiểm tra nếu sách có đơn hàng thì không cho xóa cứng
        if ($book->orderItems()->exists()) {
            Toastr::error('Không thể xóa vĩnh viễn sách này vì đã có đơn hàng liên quan!', 'Lỗi');
            return redirect()->route('admin.books.trash');
        }

        // Xóa các hình ảnh liên quan
        foreach ($book->images as $image) {
            if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }
            $image->delete();
        }

        // Xóa ảnh bìa
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        // Xóa các định dạng sách
        $book->formats()->delete();

        // Xóa các liên kết với thuộc tính
        $book->attributeValues()->detach();

        // Xóa vĩnh viễn sách
        $book->forceDelete();

        Toastr::success('Sách đã được xóa vĩnh viễn!', 'Thành công');
        return redirect()->route('admin.books.trash');
    }
}
