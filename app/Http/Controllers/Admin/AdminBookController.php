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
                'authors:id,name',
                'brand:id,name',
                'formats:id,book_id,format_name,price,discount,stock',
                'images:id,book_id,image_url',
                'attributeValues:id,value' // Thêm attributeValues để hiển thị biến thể
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
            $query->whereHas('authors', function ($q) use ($request) {
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
        $books = Book::select('id', 'title')->get();
        return view('admin.books.create', compact('categories', 'brands', 'authors', 'attributes', 'books'));
    }

    public function store(Request $request)
    {
        // Kiểm tra slug trùng lặp trước khi validate
        $title = $request->input('title');
        if ($title) {
            $slug = Str::slug($title);
            $slugExists = Book::where('slug', $slug)->exists();
            if ($slugExists) {
                return back()->withInput()->withErrors(['title' => 'Tiêu đề sách đã tồn tại. Vui lòng chọn tiêu đề khác.']);
            }
        }

        // Validation với rules chung
        $validator = Validator::make(
            $request->all(),
            $this->getBookValidationRules(false),
            $this->getValidationMessages()
        );

        if($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }
        
        // Validation bắt buộc chọn ít nhất một định dạng sách
        if (!$request->boolean('has_physical') && !$request->boolean('has_ebook')) {
            return back()->withInput()->withErrors(['format_required' => 'Vui lòng chọn ít nhất một định dạng sách (Sách vật lý hoặc Ebook).']);
        }

        $data = $request->only([
            'title',
            'description',
            'brand_id',
            'category_id',
            'status',
            'isbn',
            'publication_date',
            'page_count',
            'release_date',
            'pre_order',
            'pre_order_price',
            'stock_preorder_limit',
            'preorder_count',
            'preorder_description'
        ]);

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
                $book->images()->create(['image_url' => $path]);
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

        // Lưu thuộc tính, giá thêm và số lượng tồn kho theo biến thể
        if ($request->filled('attribute_values')) {
            foreach ($request->attribute_values as $valueId => $data) {
                BookAttributeValue::create([
                    'book_id' => $book->id,
                    'attribute_value_id' => $data['id'],
                    'extra_price' => $data['extra_price'] ?? 0,
                    'stock' => $data['stock'] ?? 0,
                    'sku' => $this->generateVariantSku($book, $data['id'])
                ]);
            }
        }

        // Lưu quà tặng nếu có
        if ($request->filled('gift_name')) {
            // Nếu có chọn sách khác thì dùng gift_book_id, không thì dùng sách hiện tại
            $bookId = $request->filled('gift_book_id') ? $request->input('gift_book_id') : $book->id;
            
            $giftData = [
                'book_id' => $bookId,
                'gift_name' => $request->input('gift_name'),
                'gift_description' => $request->input('gift_description'),
                'quantity' => $request->input('quantity', 0),
                'start_date' => $request->input('gift_start_date'),
                'end_date' => $request->input('gift_end_date'),
            ];
            if ($request->hasFile('gift_image')) {
                $giftData['gift_image'] = $request->file('gift_image')->store('gifts', 'public');
            }
            BookGift::create($giftData);
        }

        // Gán tác giả cho sách
        $book->authors()->sync($request->input('author_ids', []));
        
        Toastr::success('Thêm sách thành công!');
        return redirect()->route('admin.books.index');
    }

    /**
     * Common validation rules for book create/update
     */
    private function getBookValidationRules($isUpdate = false)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isbn' => 'required|string|max:20',
            'page_count' => 'required|integer|min:1',
            'attribute_values' => 'nullable|array',
            'attribute_values.*.id' => 'required|exists:attribute_values,id',
            'attribute_values.*.extra_price' => 'nullable|numeric|min:0',
            'attribute_values.*.stock' => 'nullable|integer|min:0',
            'has_physical' => 'boolean',
            'formats.physical.price' => 'required_if:has_physical,true|nullable|numeric|min:0',
            'formats.physical.discount' => 'nullable|numeric|min:0',
            'formats.physical.stock' => 'required_if:has_physical,true|nullable|integer|min:0',
            'has_ebook' => 'boolean',
            'formats.ebook.price' => 'required_if:has_ebook,true|nullable|numeric|min:0',
            'formats.ebook.discount' => 'nullable|numeric|min:0',
            'formats.ebook.allow_sample_read' => 'boolean',
            'status' => 'required|string|max:50',
            'category_id' => 'required|uuid|exists:categories,id',
            'author_ids' => 'required|array|min:1',
            'author_ids.*' => 'required|uuid|exists:authors,id',
            'brand_id' => 'required|uuid|exists:brands,id',
            'publication_date' => 'required|date',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            // Preorder validation
            'pre_order' => 'boolean',
            'release_date' => 'required_if:pre_order,true|nullable|date|after:today',
            'pre_order_price' => 'nullable|numeric|min:0',
            'stock_preorder_limit' => 'required_if:pre_order,true|nullable|integer|min:1',
            'preorder_count' => 'nullable|integer|min:0',
            'preorder_description' => 'nullable|string|max:1000',
            
            // Gift validation
            'gift_book_id' => 'nullable|uuid|exists:books,id',
            'gift_name' => 'nullable|string|max:255',
            'gift_description' => 'nullable|string',
            'gift_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'quantity' => 'nullable|integer|min:0',
            'gift_start_date' => 'nullable|date',
            'gift_end_date' => 'nullable|date|after_or_equal:gift_start_date',
        ];

        // Different rules for create vs update
        if ($isUpdate) {
            $rules['cover_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['formats.ebook.file'] = 'nullable|mimes:pdf,epub|max:50000';
            $rules['formats.ebook.sample_file'] = 'nullable|mimes:pdf,epub|max:10000';
            // For update, existing attributes validation
            $rules['existing_attributes'] = 'nullable|array';
            $rules['existing_attributes.*.extra_price'] = 'nullable|numeric|min:0';
            $rules['existing_attributes.*.stock'] = 'nullable|integer|min:0';
            $rules['existing_attributes.*.keep'] = 'nullable|in:0,1';
        } else {
            $rules['cover_image'] = 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048';
            $rules['formats.ebook.file'] = 'required_if:has_ebook,true|nullable|mimes:pdf,epub|max:50000';
            $rules['formats.ebook.sample_file'] = 'nullable|mimes:pdf,epub|max:10000';
            // For create, attribute values should not be duplicated
            $rules['attribute_values.*.id'] .= '|distinct';
        }

        return $rules;
    }

    /**
     * Get validation messages
     */
    private function getValidationMessages()
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề sách',
            'title.unique' => 'Tiêu đề sách đã tồn tại.',
            'isbn.required' => 'Vui lòng nhập mã ISBN',
            'page_count.required' => 'Vui lòng nhập số trang',
            'page_count.integer' => 'Số trang phải là số nguyên',
            'page_count.min' => 'Số trang phải lớn hơn 0',
            'category_id.required' => 'Vui lòng chọn danh mục',
            'category_id.uuid' => 'Danh mục không hợp lệ',
            'author_ids.required' => 'Vui lòng chọn ít nhất một tác giả',
            'author_ids.min' => 'Vui lòng chọn ít nhất một tác giả',
            'author_ids.*.uuid' => 'Tác giả không hợp lệ',
            'brand_id.required' => 'Vui lòng chọn thương hiệu',
            'brand_id.uuid' => 'Thương hiệu không hợp lệ',
            'status.required' => 'Vui lòng chọn trạng thái',
            'cover_image.required' => 'Vui lòng chọn ảnh bìa',
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
            'formats.ebook.sample_file.mimes' => 'File đọc thử phải có định dạng PDF hoặc EPUB',
            'formats.ebook.sample_file.max' => 'Kích thước file đọc thử không được vượt quá 10MB',
            'attribute_values.*.id.distinct' => 'Không được chọn trùng thuộc tính cho sách',
            'attribute_values.*.id.exists' => 'Thuộc tính không tồn tại',
            'attribute_values.*.extra_price.numeric' => 'Giá thêm phải là số',
            'attribute_values.*.extra_price.min' => 'Giá thêm không được âm',
            'attribute_values.*.stock.integer' => 'Tồn kho phải là số nguyên',
            'attribute_values.*.stock.min' => 'Tồn kho không được âm',
            'publication_date.required' => 'Vui lòng nhập ngày xuất bản',
            'publication_date.date' => 'Ngày xuất bản không hợp lệ',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
        ];
    }

    /**
     * Tạo mã SKU cho biến thể sản phẩm
     * Theo docs biến thể: Mã cha + hậu tố phân biệt
     */
    private function generateVariantSku($book, $attributeValueId)
    {
        // Lấy thông tin attribute value để tạo hậu tố
        $attributeValue = \App\Models\AttributeValue::with('attribute')->find($attributeValueId);
        
        if (!$attributeValue) {
            return $book->isbn . '-VAR-' . substr($attributeValueId, 0, 8);
        }

        // Tạo mã cha từ ISBN hoặc ID sách
        $parentCode = $book->isbn ?: 'BOOK-' . substr($book->id, 0, 8);
        
        // Tạo hậu tố dựa trên loại thuộc tính
        $suffix = '';
        $attributeName = strtolower($attributeValue->attribute->name ?? '');
        $attributeValueName = strtolower($attributeValue->value ?? '');
        
        // Định dạng sách
        if (strpos($attributeName, 'định dạng') !== false || strpos($attributeName, 'format') !== false) {
            if (strpos($attributeValueName, 'cứng') !== false) {
                $suffix = 'BC'; // Bìa cứng
            } elseif (strpos($attributeValueName, 'mềm') !== false) {
                $suffix = 'BM'; // Bìa mềm
            } else {
                $suffix = 'DF'; // Định dạng khác
            }
        }
        // Ngôn ngữ
        elseif (strpos($attributeName, 'ngôn ngữ') !== false || strpos($attributeName, 'language') !== false) {
            if (strpos($attributeValueName, 'việt') !== false) {
                $suffix = 'VI'; // Tiếng Việt
            } elseif (strpos($attributeValueName, 'anh') !== false || strpos($attributeValueName, 'english') !== false) {
                $suffix = 'EN'; // Tiếng Anh
            } else {
                $suffix = 'LG'; // Ngôn ngữ khác
            }
        }
        // Kích thước
        elseif (strpos($attributeName, 'kích thước') !== false || strpos($attributeName, 'size') !== false) {
            $suffix = 'SZ';
        }
        // Mặc định
        else {
            $suffix = 'VAR';
        }
        
        return $parentCode . '-' . $suffix;
    }

    public function show($id, $slug)
    {
        $book = Book::with([
            'category:id,name',
            'authors:id,name',
            'brand:id,name',
            'formats:id,book_id,format_name,price,discount,stock,file_url,sample_file_url,allow_sample_read',
            'images:id,book_id,image_url',
            'attributeValues.attribute',
            'reviews' => function($query) {
                $query->with('user:id,name,email')->orderBy('created_at', 'desc');
            },
            'gifts'
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
                'extra_price' => $attributeValue->pivot->extra_price ?? 0,
                'stock' => $attributeValue->pivot->stock ?? 0,
                'sku' => $attributeValue->pivot->sku ?? null
            ];
        }

        return view('admin.books.show', compact('book', 'attributes', 'averageRating', 'reviewCount'));
    }

    public function edit($id, $slug)
    {
        // Eager load relationships đúng cách với nested relationships
        $book = Book::with([
            'formats',
            'images',
            'bookAttributeValues.attributeValue.attribute', // Sử dụng bookAttributeValues thay vì attributeValues
            'authors',
            'gifts'
        ])->findOrFail($id);

        $categories = Category::whereNull('deleted_at')->get();
        $brands = Brand::whereNull('deleted_at')->get();
        $authors = Author::whereNull('deleted_at')->get();
        $attributes = Attribute::with('values')->get();

        // Lấy định dạng sách vật lý nếu có
        $physicalFormat = $book->formats->where('format_name', 'Sách Vật Lý')->first();

        // Lấy định dạng ebook nếu có
        $ebookFormat = $book->formats->where('format_name', 'Ebook')->first();

        // Chuẩn bị dữ liệu thuộc tính đã chọn - sửa logic đúng
        $selectedAttributeValues = [];
        foreach ($book->bookAttributeValues as $bookAttributeValue) {
            // $bookAttributeValue là BookAttributeValue model
            $selectedAttributeValues[$bookAttributeValue->id] = [
                'id' => $bookAttributeValue->attribute_value_id, // ID của AttributeValue
                'book_attribute_value_id' => $bookAttributeValue->id, // ID của BookAttributeValue
                'extra_price' => $bookAttributeValue->extra_price ?? 0,
                'stock' => $bookAttributeValue->stock ?? 0,
                'sku' => $bookAttributeValue->sku ?? null,
                'attribute_value' => $bookAttributeValue->attributeValue,
                'attribute' => $bookAttributeValue->attributeValue->attribute ?? null
            ];
        }

        $books = Book::select('id', 'title')->get();
        
        // Lấy quà tặng hiện tại của sách (nếu có)
        $currentGift = $book->gifts->first();
        
        return view('admin.books.edit', compact(
            'book',
            'categories',
            'brands',
            'authors',
            'attributes',
            'physicalFormat',
            'ebookFormat',
            'selectedAttributeValues',
            'books',
            'currentGift'
        ));
    }

    public function update(Request $request, $id, $slug)
    {
        $book = Book::findOrFail($id);

        // Kiểm tra slug trùng lặp trước khi validate
        $title = $request->input('title');
        if ($title) {
            $slug = Str::slug($title);
            $slugExists = Book::where('slug', $slug)->where('id', '!=', $id)->exists();
            if ($slugExists) {
                return back()->withInput()->withErrors(['title' => 'Tiêu đề sách đã tồn tại. Vui lòng chọn tiêu đề khác.']);
            }
        }

        // Validation với rules chung
        $validator = Validator::make(
            $request->all(),
            $this->getBookValidationRules(true),
            $this->getValidationMessages()
        );

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->errors());
        }
        
        // Validation bắt buộc chọn ít nhất một định dạng sách
        if (!$request->boolean('has_physical') && !$request->boolean('has_ebook')) {
            return back()->withInput()->withErrors(['format_required' => 'Vui lòng chọn ít nhất một định dạng sách (Sách vật lý hoặc Ebook).']);
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

        $data['slug'] = Str::slug($data['title']);
        $book->update($data);

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

        // Cập nhật thuộc tính, giá thêm và số lượng tồn kho theo biến thể
        // Xử lý thuộc tính hiện có - đơn giản hóa logic
        if ($request->filled('existing_attributes')) {
            foreach ($request->existing_attributes as $bookAttributeValueId => $data) {
                $bookAttributeValue = BookAttributeValue::find($bookAttributeValueId);
                
                if ($bookAttributeValue && $bookAttributeValue->book_id == $book->id) {
                    if (isset($data['keep']) && $data['keep'] == '1') {
                        // Cập nhật thuộc tính hiện có
                        $bookAttributeValue->update([
                            'extra_price' => $data['extra_price'] ?? 0,
                            'stock' => $data['stock'] ?? 0,
                        ]);
                    } else {
                        // Xóa thuộc tính nếu không keep
                        $bookAttributeValue->delete();
                    }
                }
            }
        }

        // Thêm các thuộc tính mới - cải thiện logic
        if ($request->filled('attribute_values')) {
            foreach ($request->attribute_values as $valueId => $data) {
                // Kiểm tra xem thuộc tính đã tồn tại chưa để tránh trùng lặp
                $existingBookAttributeValue = BookAttributeValue::where('book_id', $book->id)
                    ->where('attribute_value_id', $data['id'])
                    ->first();
                    
                if (!$existingBookAttributeValue) {
                    BookAttributeValue::create([
                        'id' => (string) Str::uuid(),
                        'book_id' => $book->id,
                        'attribute_value_id' => $data['id'],
                        'extra_price' => $data['extra_price'] ?? 0,
                        'stock' => $data['stock'] ?? 0,
                        'sku' => $this->generateVariantSku($book, $data['id']),
                    ]);
                }
            }
        }

        // Cập nhật quà tặng
        // Xóa quà tặng cũ
        $book->gifts()->delete();
        
        // Tạo quà tặng mới nếu có
         if ($request->filled('gift_name')) {
             // Nếu có chọn sách khác thì dùng gift_book_id, không thì dùng sách hiện tại
             $bookId = $request->filled('gift_book_id') ? $request->input('gift_book_id') : $book->id;
             
             $giftData = [
                 'book_id' => $bookId,
                 'gift_name' => $request->input('gift_name'),
                 'gift_description' => $request->input('gift_description'),
                 'quantity' => $request->input('quantity', 0),
                 'start_date' => $request->input('gift_start_date'),
                 'end_date' => $request->input('gift_end_date'),
             ];
             if ($request->hasFile('gift_image')) {
                 $giftData['gift_image'] = $request->file('gift_image')->store('gifts', 'public');
             }
             BookGift::create($giftData);
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
