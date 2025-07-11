<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Book;
use Carbon\Carbon;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng của người dùng
     */
    public function index()
    {
        if (!Auth::check()) {
            Toastr::error('Bạn cần đăng nhập để xem giỏ hàng của bạn.', 'Lỗi');
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Query cơ bản cho cart items mà không join với collections để tránh duplicate
        $cart = DB::table('carts')
            ->join('books', 'carts.book_id', '=', 'books.id')
            ->leftJoin('book_formats', 'carts.book_format_id', '=', 'book_formats.id')
            ->leftJoin('author_books', 'books.id', '=', 'author_books.book_id')
            ->leftJoin('authors', 'author_books.author_id', '=', 'authors.id')
            ->where('carts.user_id', $user->id)
            ->select(
                'carts.id',
                'carts.user_id',
                'carts.book_id',
                'carts.book_format_id',
                'carts.quantity',
                'carts.attribute_value_ids',
                'carts.price',
                'carts.created_at',
                'carts.updated_at',
                'books.title',
                'books.cover_image as image',
                DB::raw('COALESCE(book_formats.format_name, "Bản thường") as format_name'),
                DB::raw('COALESCE(GROUP_CONCAT(DISTINCT authors.name SEPARATOR ", "), "Chưa cập nhật") as author_name'),
                DB::raw('COALESCE(carts.quantity, 1) as quantity'),
                DB::raw('COALESCE(carts.price, 0) as price'),
                DB::raw('COALESCE(book_formats.stock, 0) as stock'),
                DB::raw('COALESCE(carts.attribute_value_ids, "[]") as attribute_value_ids')
            )
            ->groupBy(
                'carts.id',
                'carts.user_id',
                'carts.book_id',
                'carts.book_format_id',
                'carts.quantity',
                'carts.attribute_value_ids',
                'carts.price',
                'carts.created_at',
                'carts.updated_at',
                'books.title',
                'books.cover_image',
                'book_formats.format_name',
                'book_formats.stock'
            )
            ->get();

        // Sau đó thêm thông tin collection/combo cho từng item riêng biệt
        foreach ($cart as $item) {
            // Lấy thông tin combo (collection) đang hoạt động - chỉ lấy 1 combo đầu tiên
            $comboInfo = DB::table('book_collections')
                ->join('collections', 'book_collections.collection_id', '=', 'collections.id')
                ->where('book_collections.book_id', $item->book_id)
                ->where(function($query) {
                    $query->whereNull('collections.start_date')
                          ->orWhere('collections.start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('collections.end_date')
                          ->orWhere('collections.end_date', '>=', now());
                })
                ->whereNull('collections.deleted_at')
                ->where('collections.combo_price', '>', 0)
                ->select(
                    'collections.id as collection_id',
                    'collections.name as collection_name',
                    'collections.combo_price',
                    'collections.start_date as combo_start_date',
                    'collections.end_date as combo_end_date'
                )
                ->first();

            // Thêm thông tin combo vào item
            if ($comboInfo) {
                $item->collection_id = $comboInfo->collection_id;
                $item->collection_name = $comboInfo->collection_name;
                $item->combo_price = $comboInfo->combo_price;
                $item->combo_start_date = $comboInfo->combo_start_date;
                $item->combo_end_date = $comboInfo->combo_end_date;
            } else {
                $item->collection_id = null;
                $item->collection_name = null;
                $item->combo_price = null;
                $item->combo_start_date = null;
                $item->combo_end_date = null;
            }
        }
        
        // Thêm thông tin gifts cho từng item
        foreach ($cart as $item) {
            $item->gifts = DB::table('book_gifts')
                ->where('book_id', $item->book_id)
                ->where(function($query) {
                    $query->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                })
                ->where('quantity', '>', 0)
                ->get();
        }

        $total = 0;
        foreach ($cart as $item) {
            // Ưu tiên giá combo nếu có và đang trong thời gian khuyến mại
            $itemPrice = $item->price;
            if ($item->collection_id && $item->combo_price) {
                // Kiểm tra thời gian combo
                $now = now();
                $comboValid = true;
                
                if ($item->combo_start_date && $now->lt($item->combo_start_date)) {
                    $comboValid = false;
                }
                if ($item->combo_end_date && $now->gt($item->combo_end_date)) {
                    $comboValid = false;
                }
                
                if ($comboValid && $item->combo_price < $itemPrice) {
                    $itemPrice = $item->combo_price;
                    $item->is_combo_price = true;
                    $item->original_price = $item->price;
                    $item->price = $itemPrice; // Update giá hiển thị
                }
            }
            
            $total += $itemPrice * $item->quantity;
        }
        // dd($total);

        // Lấy thông tin voucher đã áp dụng (nếu có)
        $appliedVoucher = session()->get('applied_voucher');
        $discount = $appliedVoucher ? $appliedVoucher['discount_amount'] : 0;

        return view('clients.cart.cart', compact('cart', 'total', 'discount'));
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.'], 401);
        }

        try {
            // Validate request data
            $validated = $request->validate([
                'book_id' => 'required|exists:books,id',
                'quantity' => 'required|integer|min:1',
                'book_format_id' => 'nullable|exists:book_formats,id',
                'attribute_value_ids' => 'nullable|string',
                'attributes' => 'nullable|array'
            ]);

            $bookId = $validated['book_id'];
            $quantity = $validated['quantity'];
            $bookFormatId = $validated['book_format_id'] ?? null;
            
            // Xử lý attribute_value_ids từ form
            $attributeValueIds = [];
            
            // Cách 1: Nếu gửi lên dưới dạng JSON string
            if (!empty($validated['attribute_value_ids'])) {
                $decoded = json_decode($validated['attribute_value_ids'], true);
                if (is_array($decoded)) {
                    $attributeValueIds = $decoded;
                }
            }
            
            // Cách 2: Nếu gửi lên dưới dạng attributes[key] = value
            if (!empty($validated['attributes']) && is_array($validated['attributes'])) {
                foreach ($validated['attributes'] as $key => $value) {
                    if (!empty($value)) {
                        $attributeValueIds[] = $value;
                    }
                }
            }
            
            // Validation: Chỉ giữ lại những UUID hợp lệ và tồn tại trong database
            $validAttributeIds = [];
            if (!empty($attributeValueIds)) {
                $validAttributeIds = DB::table('attribute_values')
                    ->whereIn('id', $attributeValueIds)
                    ->pluck('id')
                    ->toArray();
                
                // Log để debug
                Log::info('Cart addToCart - Attribute validation:', [
                    'requested_ids' => $attributeValueIds,
                    'valid_ids' => $validAttributeIds
                ]);
            }
            
            // Loại bỏ duplicate và chuyển thành JSON string
            $validAttributeIds = array_unique($validAttributeIds);
            $attributeJson = json_encode(array_values($validAttributeIds));

            // Lấy thông tin book format
            if ($bookFormatId) {
                $bookInfo = DB::table('books')
                    ->join('book_formats', function($join) use ($bookFormatId) {
                        $join->on('books.id', '=', 'book_formats.book_id')
                             ->where('book_formats.id', '=', $bookFormatId);
                    })
                    ->where('books.id', $bookId)
                    ->select(
                        'books.id',
                        'books.title',
                        'book_formats.id as format_id',
                        'book_formats.price',
                        'book_formats.format_name',
                        'book_formats.stock',
                        'book_formats.discount'
                    )
                    ->first();
            } else {
                // Nếu không có format được chọn, lấy format đầu tiên
                $bookInfo = DB::table('books')
                    ->join('book_formats', 'books.id', '=', 'book_formats.book_id')
                    ->where('books.id', $bookId)
                    ->orderBy('book_formats.price', 'asc')
                    ->select(
                        'books.id',
                        'books.title',
                        'book_formats.id as format_id',
                        'book_formats.price',
                        'book_formats.format_name',
                        'book_formats.stock',
                        'book_formats.discount'
                    )
                    ->first();
                
                if ($bookInfo) {
                    $bookFormatId = $bookInfo->format_id;
                }
            }

            if (!$bookInfo) {
                return response()->json(['error' => 'Không tìm thấy sách hoặc định dạng sách'], 404);
            }

            // Kiểm tra ebook
            $isEbook = false;
            if (isset($bookInfo->format_name)) {
                $isEbook = stripos($bookInfo->format_name, 'ebook') !== false;
            }
            
            // Debug log
            Log::info('Cart addToCart - Product type check:', [
                'book_id' => $bookId,
                'format_name' => $bookInfo->format_name ?? 'N/A',
                'is_ebook' => $isEbook
            ]);
            
            // Log thông tin sản phẩm đang thêm vào giỏ hàng (không còn kiểm tra xung đột loại sản phẩm)
            Log::info('Cart addToCart - Adding product to cart:', [
                'book_id' => $bookId,
                'format_name' => $bookInfo->format_name ?? 'N/A',
                'is_ebook' => $isEbook,
                'quantity' => $quantity,
                'user_id' => Auth::id()
            ]);
            
            // Nếu là ebook: luôn set quantity = 1, bỏ qua check tồn kho
            if ($isEbook) {
                $quantity = 1;
            }

            // Kiểm tra tồn kho (chỉ với sách vật lý, không phải ebook)
            if (!$isEbook) {
                if ($bookInfo->stock <= 0) {
                    return response()->json([
                        'error' => 'Sản phẩm đã hết hàng',
                        'available_stock' => $bookInfo->stock
                    ], 422);
                }
                if ($quantity > $bookInfo->stock) {
                    return response()->json([
                        'error' => "Số lượng yêu cầu vượt quá số lượng tồn kho. Tồn kho hiện tại: {$bookInfo->stock}",
                        'available_stock' => $bookInfo->stock
                    ], 422);
                }
            }

            // Tính giá cuối cùng sau khi áp dụng discount (nếu có)
            $finalPrice = $bookInfo->price;
            if (isset($bookInfo->discount) && $bookInfo->discount > 0) {
                $finalPrice = $bookInfo->price * (1 - $bookInfo->discount / 100);
            }
            
            // Kiểm tra combo price (nếu sách thuộc combo đang hoạt động)
            $comboInfo = DB::table('book_collections')
                ->join('collections', 'book_collections.collection_id', '=', 'collections.id')
                ->where('book_collections.book_id', $bookId)
                ->where(function($query) {
                    $query->whereNull('collections.start_date')
                          ->orWhere('collections.start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('collections.end_date')
                          ->orWhere('collections.end_date', '>=', now());
                })
                ->whereNull('collections.deleted_at')
                ->where('collections.combo_price', '>', 0)
                ->first();
                
            if ($comboInfo && $comboInfo->combo_price < $finalPrice) {
                $finalPrice = $comboInfo->combo_price;
                Log::info('Applied combo pricing:', [
                    'book_id' => $bookId,
                    'original_price' => $bookInfo->price,
                    'combo_price' => $comboInfo->combo_price,
                    'collection_name' => $comboInfo->name ?? 'Unknown'
                ]);
            }

            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa (bao gồm cả thuộc tính)
            $existingCart = DB::table('carts')
                ->where('user_id', Auth::id())
                ->where('book_id', $bookId)
                ->where('book_format_id', $bookFormatId)
                ->where('attribute_value_ids', $attributeJson)
                ->first();

            if ($existingCart) {
                // Nếu là ebook: luôn giữ số lượng là 1
                if ($isEbook) {
                    DB::table('carts')
                        ->where('id', $existingCart->id)
                        ->update([
                            'quantity' => 1,
                            'updated_at' => now()
                        ]);
                    return response()->json([
                        'success' => 'Đã thêm sách điện tử vào giỏ hàng',
                        'stock' => $bookInfo->stock,
                        'current_quantity' => 1
                    ]);
                }
                // Kiểm tra tổng số lượng sau khi thêm (chỉ với sách vật lý)
                $newQuantity = $existingCart->quantity + $quantity;
                if (!$isEbook && $newQuantity > $bookInfo->stock) {
                    return response()->json([
                        'error' => "Số lượng tổng cộng vượt quá tồn kho. Tồn kho hiện tại: {$bookInfo->stock}, số lượng trong giỏ: {$existingCart->quantity}",
                        'available_stock' => $bookInfo->stock,
                        'current_cart_quantity' => $existingCart->quantity
                    ], 422);
                }
                // Cập nhật số lượng và giá (case giá có thể thay đổi)
                DB::table('carts')
                    ->where('id', $existingCart->id)
                    ->update([
                        'quantity' => $newQuantity,
                        'price' => $finalPrice,
                        'updated_at' => now()
                    ]);
                return response()->json([
                    'success' => 'Đã thêm ' . $quantity . ' sản phẩm "' . $bookInfo->title . '" vào giỏ hàng',
                    'stock' => $bookInfo->stock,
                    'current_quantity' => $newQuantity
                ]);
            } else {
                // Nếu là ebook: luôn set quantity = 1
                if ($isEbook) {
                    $quantity = 1;
                }
                
                try {
                    DB::table('carts')->insert([
                        'id' => Str::uuid(),
                        'user_id' => Auth::id(),
                        'book_id' => $bookId,
                        'book_format_id' => $bookFormatId,
                        'quantity' => $quantity,
                        'attribute_value_ids' => $attributeJson,
                        'price' => $finalPrice,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    return response()->json([
                        'success' => 'Đã thêm sản phẩm "' . $bookInfo->title . '" vào giỏ hàng',
                        'stock' => $bookInfo->stock,
                        'current_quantity' => $quantity
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Nếu bị duplicate key error (unique constraint violation)
                    if ($e->getCode() == 23000) {
                        // Thử lại với logic update
                        $existingCart = DB::table('carts')
                            ->where('user_id', Auth::id())
                            ->where('book_id', $bookId)
                            ->where('book_format_id', $bookFormatId)
                            ->where('attribute_value_ids', $attributeJson)
                            ->first();
                            
                        if ($existingCart) {
                            if ($isEbook) {
                                return response()->json([
                                    'success' => 'Sách điện tử đã có trong giỏ hàng',
                                    'stock' => $bookInfo->stock,
                                    'current_quantity' => 1
                                ]);
                            } else {
                                $newQuantity = $existingCart->quantity + $quantity;
                                if ($newQuantity > $bookInfo->stock) {
                                    return response()->json([
                                        'error' => "Số lượng tổng cộng vượt quá tồn kho. Tồn kho hiện tại: {$bookInfo->stock}, số lượng trong giỏ: {$existingCart->quantity}",
                                        'available_stock' => $bookInfo->stock,
                                        'current_cart_quantity' => $existingCart->quantity
                                    ], 422);
                                }
                                
                                DB::table('carts')
                                    ->where('id', $existingCart->id)
                                    ->update([
                                        'quantity' => $newQuantity,
                                        'price' => $finalPrice,
                                        'updated_at' => now()
                                    ]);
                                    
                                return response()->json([
                                    'success' => 'Đã thêm ' . $quantity . ' sản phẩm "' . $bookInfo->title . '" vào giỏ hàng',
                                    'stock' => $bookInfo->stock,
                                    'current_quantity' => $newQuantity
                                ]);
                            }
                        }
                    }
                    throw $e; // Rethrow nếu không phải duplicate key error
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Dữ liệu không hợp lệ: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in addToCart:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Có lỗi xảy ra khi thêm vào giỏ hàng'], 500);
        }
    }

    public function updateCart(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Bạn cần đăng nhập để cập nhật giỏ hàng.'], 401);
            }

            $bookId = $request->book_id;
            $bookFormatId = $request->book_format_id; // Thêm để xác định chính xác item cần update
            $attributeValueIds = $request->attribute_value_ids; // Thêm để xác định chính xác item
            $quantity = (int)$request->quantity;

            // Get the specific cart item with all identification data
            $cartItemQuery = DB::table('carts')
                ->where('user_id', Auth::id())
                ->where('book_id', $bookId);

            // Add specific format and attribute constraints if provided
            if ($bookFormatId) {
                $cartItemQuery->where('book_format_id', $bookFormatId);
            }

            if ($attributeValueIds) {
                $cartItemQuery->where('attribute_value_ids', $attributeValueIds);
            }

            $cartItem = $cartItemQuery->first();

            if (!$cartItem) {
                return response()->json(['error' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
            }

            // Kiểm tra tồn kho và thông tin sách với format cụ thể
            $bookInfo = DB::table('books')
                ->leftJoin('book_formats', function($join) use ($cartItem) {
                    $join->on('books.id', '=', 'book_formats.book_id')
                         ->where('book_formats.id', '=', $cartItem->book_format_id);
                })
                ->leftJoin('author_books', 'books.id', '=', 'author_books.book_id')
                ->leftJoin('authors', 'author_books.author_id', '=', 'authors.id')
                ->where('books.id', $bookId)
                ->select(
                    'books.*',
                    'book_formats.id as format_id',
                    DB::raw('COALESCE(book_formats.format_name, "Bản thường") as format_name'),
                    DB::raw('COALESCE(authors.name, "Chưa cập nhật") as author_name'),
                    DB::raw('COALESCE(book_formats.stock, 0) as stock'),
                    DB::raw('COALESCE(book_formats.price, 0) as price')
                )
                ->first();

            if (!$bookInfo) {
                return response()->json(['error' => 'Không tìm thấy sách hoặc định dạng sách'], 404);
            }

            // Kiểm tra loại sách (ebook hay sách vật lý)
            $isEbook = false;
            if (isset($bookInfo->format_name)) {
                $isEbook = stripos($bookInfo->format_name, 'ebook') !== false;
            }
            
            // Tính giá hiện tại (bao gồm combo nếu có)
            $currentPrice = $bookInfo->price;
            if (isset($bookInfo->discount) && $bookInfo->discount > 0) {
                $currentPrice = $bookInfo->price * (1 - $bookInfo->discount / 100);
            }
            
            // Kiểm tra combo price
            $comboInfo = DB::table('book_collections')
                ->join('collections', 'book_collections.collection_id', '=', 'collections.id')
                ->where('book_collections.book_id', $bookId)
                ->where(function($query) {
                    $query->whereNull('collections.start_date')
                          ->orWhere('collections.start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('collections.end_date')
                          ->orWhere('collections.end_date', '>=', now());
                })
                ->whereNull('collections.deleted_at')
                ->where('collections.combo_price', '>', 0)
                ->first();
                
            if ($comboInfo && $comboInfo->combo_price < $currentPrice) {
                $currentPrice = $comboInfo->combo_price;
            }

            // Logic tách biệt cho ebook và sách vật lý
            if ($isEbook) {
                // EBOOK: Luôn giữ số lượng = 1, không cho phép thay đổi
                $updateQuery = DB::table('carts')
                    ->where('user_id', Auth::id())
                    ->where('book_id', $bookId)
                    ->where('book_format_id', $cartItem->book_format_id);

                if ($cartItem->attribute_value_ids) {
                    $updateQuery->where('attribute_value_ids', $cartItem->attribute_value_ids);
                }

                $updateQuery->update([
                    'quantity' => 1,
                    'updated_at' => now()
                ]);

                return response()->json([
                    'success' => 'Sách điện tử luôn có số lượng cố định là 1',
                    'data' => [
                        'stock' => 999, // Ebook không giới hạn tồn kho
                        'price' => $bookInfo->price,
                        'quantity' => 1,
                        'is_ebook' => true
                    ]
                ]);
            } else {
                // SÁCH VẬT LÝ: Kiểm tra tồn kho và cho phép thay đổi số lượng
                if ($quantity > $bookInfo->stock) {
                    return response()->json([
                        'error' => "Số lượng yêu cầu vượt quá số lượng tồn kho. Tồn kho hiện tại: {$bookInfo->stock}",
                        'available_stock' => $bookInfo->stock,
                        'is_ebook' => false
                    ], 422);
                }

                if ($quantity > 0) {
                    // Cập nhật số lượng cho sách vật lý
                    $updateQuery = DB::table('carts')
                        ->where('user_id', Auth::id())
                        ->where('book_id', $bookId)
                        ->where('book_format_id', $cartItem->book_format_id);

                    if ($cartItem->attribute_value_ids) {
                        $updateQuery->where('attribute_value_ids', $cartItem->attribute_value_ids);
                    }

                    $updateQuery->update([
                        'quantity' => $quantity,
                        'price' => $currentPrice, // Cập nhật giá mới (có thể là combo price)
                        'updated_at' => now()
                    ]);

                    return response()->json([
                        'success' => 'Đã cập nhật số lượng sản phẩm',
                        'data' => [
                            'stock' => $bookInfo->stock,
                            'price' => $currentPrice, // Trả về giá đã được áp dụng combo (nếu có)
                            'quantity' => $quantity,
                            'is_ebook' => false
                        ]
                    ]);
                } else {
                    // Xóa sản phẩm khi số lượng = 0
                    $deleteQuery = DB::table('carts')
                        ->where('user_id', Auth::id())
                        ->where('book_id', $bookId)
                        ->where('book_format_id', $cartItem->book_format_id);

                    if ($cartItem->attribute_value_ids) {
                        $deleteQuery->where('attribute_value_ids', $cartItem->attribute_value_ids);
                    }

                    $deletedCount = $deleteQuery->delete();

                    if ($deletedCount > 0) {
                        return response()->json(['success' => 'Đã xóa sản phẩm khỏi giỏ hàng']);
                    } else {
                        return response()->json(['error' => 'Không tìm thấy sản phẩm để xóa'], 404);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in updateCart:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['error' => 'Có lỗi xảy ra khi cập nhật giỏ hàng: ' . $e->getMessage()], 500);
        }
    }

    public function removeFromCart(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Bạn cần đăng nhập để xóa sản phẩm khỏi giỏ hàng.'], 401);
            }

            $bookId = $request->book_id;
            $bookFormatId = $request->book_format_id;
            $attributeValueIds = $request->attribute_value_ids;

            Log::info('Removing cart item:', [
                'user_id' => Auth::id(),
                'book_id' => $bookId,
                'book_format_id' => $bookFormatId,
                'attribute_value_ids' => $attributeValueIds
            ]);

            // Tìm cart item cụ thể để xóa
            $query = DB::table('carts')
                ->where('user_id', Auth::id())
                ->where('book_id', $bookId);

            // Nếu có book_format_id, thêm vào điều kiện
            if ($bookFormatId) {
                $query->where('book_format_id', $bookFormatId);
            }

            // Nếu có attribute_value_ids, thêm vào điều kiện
            if ($attributeValueIds) {
                $query->where('attribute_value_ids', $attributeValueIds);
            }

            $deletedCount = $query->delete();

            if ($deletedCount > 0) {
                Log::info('Cart item removed successfully:', [
                    'deleted_count' => $deletedCount
                ]);
                return response()->json(['success' => 'Đã xóa sản phẩm khỏi giỏ hàng']);
            } else {
                Log::warning('No cart item found to delete');
                return response()->json(['error' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
            }

        } catch (\Exception $e) {
            Log::error('Error in removeFromCart:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Có lỗi xảy ra khi xóa sản phẩm'], 500);
        }
    }

    // public function applyVoucher(Request $request)
    // {
    //     $request->validate([
    //         'code' => 'required|string',
    //         'total' => 'required|numeric|min:0'
    //     ]);

    //     $voucherCode = strtoupper($request->code);
        
    //     // Kiểm tra voucher có tồn tại không
    //     $basicVoucher = DB::table('vouchers')
    //         ->where('code', $voucherCode)
    //         ->first();
            
    //     if (!$basicVoucher) {
    //         Log::info("Voucher không tồn tại trong database: " . $voucherCode);
    //         return response()->json([
    //             'error' => 'Mã giảm giá không tồn tại'
    //         ], 404);
    //     }

    //     // Debug: Log chi tiết thông tin voucher
    //     Log::info("Chi tiết voucher:", [
    //         'code' => $basicVoucher->code,
    //         'status' => $basicVoucher->status,
    //         'valid_from' => $basicVoucher->valid_from,
    //         'valid_to' => $basicVoucher->valid_to,
    //         'quantity' => $basicVoucher->quantity,
    //         'deleted_at' => $basicVoucher->deleted_at
    //     ]);

    //     // 1. Kiểm tra voucher đã bị xóa mềm (deleted_at)
    //     if ($basicVoucher->deleted_at !== null) {
    //         Log::info("Voucher đã bị xóa: " . $voucherCode);
    //         return response()->json([
    //             'error' => 'Mã giảm giá không tồn tại'
    //         ], 404);
    //     }

    //     // 2. Kiểm tra trạng thái voucher
    //     switch (strtolower($basicVoucher->status)) {
    //         case 'inactive':
    //         case 'locked':
    //         case 'disabled':
    //             Log::info("Voucher bị khóa hoặc vô hiệu hóa: " . $voucherCode, [
    //                 'status' => $basicVoucher->status
    //             ]);
    //             return response()->json([
    //                 'error' => 'Mã giảm giá đã bị vô hiệu hóa hoặc bị khóa'
    //             ], 400);
                
    //         case 'expired':
    //             Log::info("Voucher đã hết hạn theo status: " . $voucherCode);
    //             return response()->json([
    //                 'error' => 'Mã giảm giá đã hết hạn'
    //             ], 400);
                
    //         case 'used':
    //         case 'exhausted':
    //             Log::info("Voucher đã được sử dụng hết: " . $voucherCode);
    //             return response()->json([
    //                 'error' => 'Mã giảm giá đã hết lượt sử dụng'
    //             ], 400);
                
    //         case 'pending':
    //         case 'scheduled':
    //             Log::info("Voucher chưa được kích hoạt: " . $voucherCode);
    //             return response()->json([
    //                 'error' => 'Mã giảm giá chưa được kích hoạt'
    //             ], 400);
                
    //         case 'active':
    //             // Trạng thái hợp lệ, tiếp tục kiểm tra
    //             break;
                
    //         default:
    //             Log::info("Voucher có trạng thái không xác định: " . $voucherCode, [
    //                 'status' => $basicVoucher->status
    //             ]);
    //             return response()->json([
    //                 'error' => 'Mã giảm giá không hợp lệ'
    //             ], 400);
    //     }

    //     // 3. Kiểm tra thời gian hiệu lực
    //     $now = Carbon::now();
        
    //     if ($basicVoucher->valid_from !== null || $basicVoucher->valid_to !== null) {
    //         $validFrom = $basicVoucher->valid_from ? Carbon::parse($basicVoucher->valid_from) : null;
    //         $validTo = $basicVoucher->valid_to ? Carbon::parse($basicVoucher->valid_to) : null;
            
    //         // Kiểm tra chưa tới thời gian hiệu lực
    //         if ($validFrom && $now < $validFrom) {
    //             Log::info("Voucher chưa tới thời gian hiệu lực: " . $voucherCode, [
    //                 'now' => $now->format('Y-m-d H:i:s'),
    //                 'valid_from' => $validFrom->format('Y-m-d H:i:s')
    //             ]);
    //             return response()->json([
    //                 'error' => sprintf('Mã giảm giá này sẽ có hiệu lực từ %s', 
    //                     $validFrom->format('d/m/Y H:i'))
    //             ], 400);
    //         }
            
    //         // Kiểm tra đã hết hạn
    //         if ($validTo && $now > $validTo) {
    //             Log::info("Voucher đã hết hạn: " . $voucherCode, [
    //                 'now' => $now->format('Y-m-d H:i:s'),
    //                 'valid_to' => $validTo->format('Y-m-d H:i:s')
    //             ]);
    //             return response()->json([
    //                 'error' => sprintf('Mã giảm giá đã hết hạn vào %s', 
    //                     $validTo->format('d/m/Y H:i'))
    //             ], 400);
    //         }
    //     }

    //     // 4. Kiểm tra số lượng còn lại
    //     if ($basicVoucher->quantity !== null && $basicVoucher->quantity <= 0) {
    //         Log::info("Voucher hết số lượng: " . $voucherCode, [
    //             'quantity' => $basicVoucher->quantity
    //         ]);
    //         return response()->json([
    //             'error' => 'Mã giảm giá đã hết lượt sử dụng'
    //         ], 400);
    //     }

    //     // 5. Kiểm tra giá trị đơn hàng tối thiểu
    //     if ($basicVoucher->min_order_value && $request->total < $basicVoucher->min_order_value) {
    //         Log::info("Đơn hàng không đạt giá trị tối thiểu: " . $voucherCode, [
    //             'order_total' => $request->total,
    //             'min_order_value' => $basicVoucher->min_order_value
    //         ]);
    //         return response()->json([
    //             'error' => sprintf('Giá trị đơn hàng tối thiểu phải từ %s để sử dụng mã giảm giá này', 
    //                 number_format($basicVoucher->min_order_value) . 'đ')
    //         ], 400);
    //     }

    //     // 6. Kiểm tra xem user đã sử dụng voucher này chưa (nếu cần)
    //     if (Auth::check()) {
    //         $userUsedVoucher = session('applied_voucher');
    //         if ($userUsedVoucher && $userUsedVoucher['code'] === $basicVoucher->code) {
    //             return response()->json([
    //                 'error' => 'Bạn đã áp dụng mã giảm giá này rồi'
    //             ], 400);
    //         }
    //     }

    //     // 7. Tính toán số tiền giảm
    //     $discountAmount = ($request->total * $basicVoucher->discount_percent) / 100;
    //     if ($basicVoucher->max_discount && $discountAmount > $basicVoucher->max_discount) {
    //         $discountAmount = $basicVoucher->max_discount;
    //     }

    //     // Đảm bảo số tiền giảm không vượt quá tổng đơn hàng
    //     if ($discountAmount > $request->total) {
    //         $discountAmount = $request->total;
    //     }

    //     // 8. Áp dụng voucher thành công - cập nhật số lượng và lưu session
    //     try {
    //         DB::beginTransaction();
            
    //         // Giảm số lượng voucher nếu có giới hạn số lượng
    //         if ($basicVoucher->quantity !== null) {
    //             $updated = DB::table('vouchers')
    //                 ->where('code', $voucherCode)
    //                 ->where('quantity', '>', 0) // Đảm bảo vẫn còn số lượng
    //                 ->decrement('quantity');
                    
    //             if (!$updated) {
    //                 DB::rollBack();
    //                 return response()->json([
    //                     'error' => 'Mã giảm giá đã hết lượt sử dụng'
    //                 ], 400);
    //             }
    //         }

    //         // Lưu voucher vào session
    //         session(['applied_voucher' => [
    //             'code' => $basicVoucher->code,
    //             'discount_amount' => $discountAmount,
    //             'applied_at' => now()->toDateTimeString()
    //         ]]);
            
    //         DB::commit();
            
    //         Log::info("Áp dụng voucher thành công: " . $voucherCode, [
    //             'discount_amount' => $discountAmount,
    //             'order_total' => $request->total
    //         ]);

    //         return response()->json([
    //             'success' => 'Áp dụng mã giảm giá thành công',
    //             'discount' => $discountAmount,
    //             'voucher' => [
    //                 'code' => $basicVoucher->code,
    //                 'description' => $basicVoucher->description,
    //                 'discount_percent' => $basicVoucher->discount_percent,
    //                 'max_discount' => $basicVoucher->max_discount,
    //                 'min_order_value' => $basicVoucher->min_order_value
    //             ]
    //         ]);
            
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Lỗi khi áp dụng voucher: ' . $voucherCode, [
    //             'error' => $e->getMessage(),
    //             'trace' => $e->getTraceAsString()
    //         ]);
    //         return response()->json([
    //             'error' => 'Có lỗi xảy ra khi áp dụng mã giảm giá'
    //         ], 500);
    //     }
    // }

    // public function removeVoucher()
    // {
    //     session()->forget('applied_voucher');
        
    //     return response()->json([
    //         'success' => 'Đã xóa mã giảm giá'
    //     ]);
    // }

    /**
     * Xóa tất cả sản phẩm trong giỏ hàng
     */
    public function clearCart(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Bạn cần đăng nhập để thực hiện chức năng này.'], 401);
            }

            $deletedCount = DB::table('carts')
                ->where('user_id', Auth::id())
                ->delete();

            // Xóa voucher đã áp dụng
            session()->forget('applied_voucher');

            return response()->json([
                'success' => "Đã xóa tất cả sản phẩm khỏi giỏ hàng",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in clearCart:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Có lỗi xảy ra khi xóa giỏ hàng'], 500);
        }
    }

    /**
     * Thêm tất cả sản phẩm từ wishlist vào giỏ hàng (giờ chỉ chuyển hướng sang trang wishlist)
     */
    public function addAllWishlistToCart(Request $request)
    {
        // Chỉ chuyển hướng sang trang wishlist
        return redirect()->route('wishlist.index');
    }

    /**
     * Lấy thông tin combo cho một sách cụ thể
     */
    public function getComboInfo(Request $request)
    {
        try {
            $bookId = $request->book_id;
            
            if (!$bookId) {
                return response()->json(['error' => 'Book ID is required'], 400);
            }
            
            $comboInfo = DB::table('book_collections')
                ->join('collections', 'book_collections.collection_id', '=', 'collections.id')
                ->where('book_collections.book_id', $bookId)
                ->where(function($query) {
                    $query->whereNull('collections.start_date')
                          ->orWhere('collections.start_date', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('collections.end_date')
                          ->orWhere('collections.end_date', '>=', now());
                })
                ->whereNull('collections.deleted_at')
                ->where('collections.combo_price', '>', 0)
                ->select([
                    'collections.id as collection_id',
                    'collections.name as collection_name',
                    'collections.combo_price',
                    'collections.start_date',
                    'collections.end_date'
                ])
                ->first();
                
            if ($comboInfo) {
                return response()->json([
                    'success' => true,
                    'combo' => $comboInfo
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No active combo found for this book'
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error getting combo info:', [
                'error' => $e->getMessage(),
                'book_id' => $request->book_id ?? null
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
