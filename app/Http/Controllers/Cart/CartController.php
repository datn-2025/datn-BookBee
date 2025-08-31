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
use App\Models\Cart;
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

        // Query để lấy tất cả cart items bao gồm cả sách đơn lẻ và combo
        $cartQuery = DB::table('carts')
            ->where('carts.user_id', $user->id)
            ->select(
                'carts.id',
                'carts.user_id',
                'carts.book_id',
                'carts.book_format_id',
                'carts.collection_id',
                'carts.is_combo',
                'carts.quantity',
                'carts.attribute_value_ids',
                'carts.variant_id',
                'carts.price',
                'carts.created_at',
                'carts.updated_at',
                DB::raw('COALESCE(carts.is_selected, 1) as is_selected')
            );

        $cartItems = $cartQuery->get();
        $cart = collect();

        foreach ($cartItems as $cartItem) {
            if ($cartItem->is_combo && $cartItem->collection_id) {
                // Xử lý các combo items
                $combo = DB::table('collections')
                    ->where('id', $cartItem->collection_id)
                    ->where('status', 'active')
                    ->whereNotNull('combo_price')
                    ->first();

                if ($combo) {
                    // Lấy danh sách sách trong combo
                    $comboBooks = DB::table('book_collections')
                        ->join('books', 'book_collections.book_id', '=', 'books.id')
                        ->where('book_collections.collection_id', $cartItem->collection_id)
                        ->select('books.id', 'books.title', 'books.cover_image')
                        ->get();

                    $item = (object) [
                        'id' => $cartItem->id,
                        'user_id' => $cartItem->user_id,
                        'book_id' => null, // Combo không có book_id cụ thể
                        'book_format_id' => null,
                        'collection_id' => $cartItem->collection_id,
                        'is_combo' => true,
                        'quantity' => $cartItem->quantity,
                        'attribute_value_ids' => $cartItem->attribute_value_ids,
                        'price' => $combo->combo_price, // Sử dụng giá combo
                        'created_at' => $cartItem->created_at,
                        'updated_at' => $cartItem->updated_at,
                        'title' => $combo->name,
                        'image' => $combo->cover_image,
                        'format_name' => 'Combo sách',
                        'author_name' => count($comboBooks) . ' cuốn sách',
                        'stock' => null, // Combo không có tồn kho
                        'gifts' => collect(), // Combo không có quà tặng
                        'combo_books' => $comboBooks,
                        'start_date' => $combo->start_date,
                        'end_date' => $combo->end_date,
                        'cover_image' => $combo->cover_image,
                        'is_selected' => isset($cartItem->is_selected) ? $cartItem->is_selected : 1
                    ];

                    $cart->push($item);
                }
            } else {
                // Xử lý sách đơn lẻ
                $bookInfo = DB::table('books')
                    ->leftJoin('book_formats', function ($join) use ($cartItem) {
                        $join->on('book_formats.book_id', '=', 'books.id')
                            ->where('book_formats.id', '=', $cartItem->book_format_id);
                    })
                    ->leftJoin('author_books', 'books.id', '=', 'author_books.book_id')
                    ->leftJoin('authors', 'author_books.author_id', '=', 'authors.id')
                    ->where('books.id', $cartItem->book_id)
                    ->select(
                        'books.title',
                        'books.cover_image',
                        DB::raw('COALESCE(book_formats.format_name, "Bản thường") as format_name'),
                        DB::raw('COALESCE(book_formats.stock, 0) as stock'),
                        DB::raw('COALESCE(book_formats.price, 0) as format_price'),
                        DB::raw('COALESCE(book_formats.discount, 0) as format_discount'),
                        DB::raw('COALESCE(GROUP_CONCAT(DISTINCT authors.name SEPARATOR ", "), "Chưa cập nhật") as author_name')
                    )
                    ->groupBy('books.id', 'books.title', 'books.cover_image', 'book_formats.format_name', 'book_formats.stock', 'book_formats.price', 'book_formats.discount')
                    ->first();

                if ($bookInfo) {
                    // Kiểm tra ebook
                    $isEbook = $bookInfo->format_name && stripos($bookInfo->format_name, 'ebook') !== false;
                    // Nếu là ebook thì không lấy quà tặng
                    if ($isEbook) {
                        $gifts = collect();
                    } else {
                        // Lấy thông tin gifts cho sách đơn lẻ (chỉ sách vật lý)
                        $gifts = DB::table('book_gifts')
                            ->where('book_id', $cartItem->book_id)
                            ->where(function ($query) {
                                $query->whereNull('start_date')
                                    ->orWhere('start_date', '<=', now());
                            })
                            ->where(function ($query) {
                                $query->whereNull('end_date')
                                    ->orWhere('end_date', '>=', now());
                            })
                            ->where('quantity', '>', 0)
                            ->select('gift_name as name', 'gift_description as description', 'gift_image as image')
                            ->get()
                            ->map(function ($gift) {
                                // Đảm bảo tất cả các đối tượng gift có các thuộc tính mong đợi
                                return (object) [
                                    'name' => $gift->name ?? 'Quà tặng',
                                    'description' => $gift->description ?? '',
                                    'image' => $gift->image ?? null
                                ];
                            });
                    }

                    // Calculate the correct stock considering variants
                    $availableStock = $bookInfo->stock; // Start with format stock
                    
                    // Priority: New variant system (variant_id) > Legacy system (attribute_value_ids)
                    if (!empty($cartItem->variant_id)) {
                        // New variant system - use book_variants table
                        $variantInfo = DB::table('book_variants')
                            ->where('id', $cartItem->variant_id)
                            ->where('book_id', $cartItem->book_id)
                            ->first();
                        
                        if ($variantInfo) {
                            // Use hierarchical stock: min(format_stock, variant_stock)
                            $availableStock = min($availableStock, $variantInfo->stock);
                        }
                    } elseif (!empty($cartItem->attribute_value_ids) && $cartItem->attribute_value_ids !== '[]') {
                        // Legacy system - use book_attribute_values table
                        $attributeIds = json_decode($cartItem->attribute_value_ids, true);
                        if ($attributeIds && is_array($attributeIds) && count($attributeIds) > 0) {
                            // Check if this is an ebook
                            $isEbook = $bookInfo->format_name && stripos($bookInfo->format_name, 'ebook') !== false;
                            
                            if (!$isEbook) {
                                // For physical books, get minimum variant stock
                                $minVariantStock = DB::table('book_attribute_values')
                                    ->whereIn('attribute_value_id', $attributeIds)
                                    ->where('book_id', $cartItem->book_id)
                                    ->min('stock');
                                
                                if ($minVariantStock !== null && $minVariantStock >= 0) {
                                    // Use hierarchical stock: min(format_stock, min_variant_stock)
                                    $availableStock = min($availableStock, $minVariantStock);
                                }
                            }
                        }
                    }

                    // Calculate the final price with discount
                    $basePrice = $bookInfo->format_price ?? 0;
                    $discount = $bookInfo->format_discount ?? 0;
                    $finalPrice = max(0, $basePrice - $discount);
                    
                    // Add extra price from variants (only for physical books)
                    $isEbook = $bookInfo->format_name && stripos($bookInfo->format_name, 'ebook') !== false;
                    
                    if (!$isEbook) {
                        // Priority: New variant system (variant_id) > Legacy system (attribute_value_ids)
                        if (!empty($cartItem->variant_id)) {
                            // New variant system - get extra price from book_variants table
                            $variantInfo = DB::table('book_variants')
                                ->where('id', $cartItem->variant_id)
                                ->first();
                            
                            if ($variantInfo) {
                                $finalPrice += $variantInfo->extra_price;
                            }
                        } elseif (!empty($cartItem->attribute_value_ids) && $cartItem->attribute_value_ids !== '[]') {
                            // Legacy system - get extra price from book_attribute_values
                            $attributeIds = json_decode($cartItem->attribute_value_ids, true);
                            if ($attributeIds && is_array($attributeIds)) {
                                $extraPrice = DB::table('book_attribute_values')
                                    ->whereIn('attribute_value_id', $attributeIds)
                                    ->where('book_id', $cartItem->book_id)
                                    ->sum('extra_price');
                                $finalPrice += $extraPrice;
                            }
                        }
                    }

                    $item = (object) [
                        'id' => $cartItem->id,
                        'user_id' => $cartItem->user_id,
                        'book_id' => $cartItem->book_id,
                        'book_format_id' => $cartItem->book_format_id,
                        'collection_id' => null,
                        'is_combo' => false,
                        'quantity' => $cartItem->quantity,
                        'attribute_value_ids' => $cartItem->attribute_value_ids,
                        'variant_id' => $cartItem->variant_id,
                        'price' => $finalPrice, // Use calculated discounted price
                        'original_price' => $basePrice, // Store original price for display
                        'discount' => $discount, // Store discount amount
                        'created_at' => $cartItem->created_at,
                        'updated_at' => $cartItem->updated_at,
                        'title' => $bookInfo->title,
                        'image' => $bookInfo->cover_image,
                        'format_name' => $bookInfo->format_name,
                        'author_name' => $bookInfo->author_name,
                        'stock' => $availableStock, // Use calculated hierarchical stock
                        'gifts' => $gifts,
                        'is_selected' => isset($cartItem->is_selected) ? $cartItem->is_selected : 1
                    ];

                    $cart->push($item);
                }
            }
        }

        // Tính tổng giá trị giỏ hàng
        $total = 0;
        foreach ($cart as $item) {
            if (isset($item->is_selected) && $item->is_selected) {
                // Price already includes extra_price from attributes, no need to add again
                $itemPrice = $item->price;
                $total += $itemPrice * $item->quantity;
            }
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
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.'], 401);
            }
            return back()->with('error', 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.');
        }

        try {
            // Kiểm tra nếu đây là combo request - hỗ trợ nhiều cách phát hiện combo
            if ($request->has(['combo_id', 'collection_id']) || $request->input('type') === 'combo') {
                return $this->addComboToCart($request);
            }

            // Validate dữ liệu request cho sách
            $validated = $request->validate([
                'book_id' => 'required|exists:books,id',
                'quantity' => 'required|integer|min:1',
                'book_format_id' => 'nullable|exists:book_formats,id',
                'attribute_value_ids' => 'nullable|string',
                'attributes' => 'nullable|array',
                'variant_id' => 'nullable|exists:book_variants,id' // Thêm hỗ trợ variant_id cho hệ thống mới
            ]);

            $bookId = $validated['book_id'];
            $quantity = $validated['quantity'];
            $bookFormatId = $validated['book_format_id'] ?? null;
            $variantId = $validated['variant_id'] ?? null;

            // Xử lý attribute_value_ids từ form
            $attributeValueIds = [];

            // Debug logging
            Log::info('addToCart request data:', [
                'all_request' => $request->all(),
                'attribute_value_ids' => $validated['attribute_value_ids'] ?? null,
                'attributes' => $validated['attributes'] ?? null,
                'variant_id' => $variantId
            ]);

            // Nếu có variant_id thì ưu tiên sử dụng hệ thống biến thể mới
            if ($variantId) {
                // Lấy thông tin variant
                $variant = DB::table('book_variants')
                    ->where('id', $variantId)
                    ->where('book_id', $bookId)
                    ->first();

                if (!$variant) {
                    return response()->json(['error' => 'Biến thể không hợp lệ'], 422);
                }

                // Lấy attribute_value_ids từ variant
                $attributeValueIds = DB::table('book_variant_attribute_values')
                    ->where('book_variant_id', $variantId)
                    ->pluck('attribute_value_id')
                    ->toArray();

                Log::info('Cart addToCart - Using new variant system:', [
                    'variant_id' => $variantId,
                    'attribute_value_ids' => $attributeValueIds,
                    'extra_price' => $variant->extra_price,
                    'stock' => $variant->stock
                ]);
            } else {
                // Sử dụng hệ thống cũ - xử lý attribute_value_ids từ form
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

                Log::info('Cart addToCart - Using legacy attribute system:', [
                    'attribute_value_ids' => $attributeValueIds
                ]);
            }

            // Validation: Chỉ giữ lại những UUID hợp lệ và tồn tại trong database
            $validAttributeIds = [];
            $formatInfo = null;

            // Lấy thông tin format trước để biết có phải ebook không
            if ($bookFormatId) {
                $formatInfo = DB::table('book_formats')
                    ->where('id', $bookFormatId)
                    ->first();
            }

            $isEbook = $formatInfo && stripos($formatInfo->format_name, 'ebook') !== false;

            if (!empty($attributeValueIds)) {
                if ($isEbook) {
                    // Đối với ebook: không xử lý thuộc tính nào cả
                    $validAttributeIds = [];
                    
                    Log::info('Cart addToCart - Ebook attributes ignored:', [
                        'book_id' => $bookId,
                        'requested_attributes' => $attributeValueIds,
                        'format_id' => $bookFormatId
                    ]);
                } else {
                    // Đối với sách vật lý: giữ lại tất cả thuộc tính hợp lệ
                    $validAttributeIds = DB::table('attribute_values')
                        ->whereIn('id', $attributeValueIds)
                        ->pluck('id')
                        ->toArray();
                }

                // Log để debug - Cải thiện
                Log::info('Cart addToCart - Attribute validation:', [
                    'requested_ids' => $attributeValueIds,
                    'valid_ids' => $validAttributeIds,
                    'is_ebook' => $isEbook,
                    'format_name' => $formatInfo ? $formatInfo->format_name : 'N/A',
                    'filtered_count' => count($validAttributeIds),
                    'original_count' => count($attributeValueIds)
                ]);
            } else {
                // Nếu không có thuộc tính nào được gửi lên - OK cho cả ebook và physical books
                Log::info('Cart addToCart - No attributes provided:', [
                    'is_ebook' => $isEbook,
                    'book_format_id' => $bookFormatId,
                    'note' => $isEbook ? 'Ebook without any attributes' : 'Physical book without attributes'
                ]);
            }

            // Loại bỏ duplicate và chuyển thành JSON string
            $validAttributeIds = array_unique($validAttributeIds);
            $attributeJson = json_encode(value: array_values($validAttributeIds));
            // dd($attributeJson);
            // Lấy thông tin book format
            if ($bookFormatId) {
                // dd($bookFormatId);
                $bookInfo = DB::table('books')
                    ->join('book_formats', function ($join) use ($bookFormatId) {
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
                // dd($bookInfo);
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

            // Kiểm tra tồn kho theo thứ tự phân cấp (chỉ với sách vật lý, không phải ebook)
            if (!$isEbook) {
                if ($variantId) {
                    // Sử dụng hệ thống biến thể mới
                    $stockValidation = $this->validateVariantStock($variantId, $quantity, $bookInfo);
                } else {
                    // Sử dụng hệ thống cũ
                    $stockValidation = $this->validateHierarchicalStock(
                        $bookId, 
                        $bookFormatId, 
                        $validAttributeIds, 
                        $quantity, 
                        $bookInfo
                    );
                }

                if (!$stockValidation['success']) {
                    return response()->json([
                        'error' => $stockValidation['message'],
                        'available_stock' => $stockValidation['available_stock'] ?? 0,
                        'stock_level' => $stockValidation['stock_level'] ?? null,
                        'variant_sku' => $stockValidation['variant_sku'] ?? null,
                        'variant_id' => $variantId
                    ], 422);
                }

                Log::info('Cart addToCart - Stock validation passed:', [
                    'book_id' => $bookId,
                    'book_format_id' => $bookFormatId,
                    'variant_id' => $variantId,
                    'selected_variants' => $validAttributeIds,
                    'requested_quantity' => $quantity,
                    'validation_result' => $stockValidation
                ]);
            } else {
                // EBOOK: Không kiểm tra stock cho variants, chỉ lưu thông tin ngôn ngữ
                Log::info('Cart addToCart - Ebook variant handling:', [
                    'book_id' => $bookId,
                    'variant_id' => $variantId,
                    'selected_variants' => $validAttributeIds,
                    'note' => 'Ebooks do not require stock validation for variants (e.g., language options)'
                ]);
            }

            // Tính giá cuối cùng sau khi áp dụng discount (nếu có)
            $finalPrice = $bookInfo->price;
            if (isset($bookInfo->discount) && $bookInfo->discount > 0) {
                // Discount giờ là số tiền VNĐ trực tiếp, không phải phần trăm
                $finalPrice = $bookInfo->price - $bookInfo->discount;
                // Đảm bảo giá không âm
                $finalPrice = max(0, $finalPrice);
            }

            // Tính thêm extra_price từ biến thể nếu có (chỉ cho sách vật lý)
            if (!empty($validAttributeIds) && !$isEbook) {
                if ($variantId) {
                    // Sử dụng extra_price từ book_variants
                    $variant = DB::table('book_variants')->where('id', $variantId)->first();
                    if ($variant) {
                        $finalPrice += $variant->extra_price;
                        
                        Log::info('Cart addToCart - Added variant extra price (new system):', [
                            'book_id' => $bookId,
                            'variant_id' => $variantId,
                            'extra_price' => $variant->extra_price,
                            'final_price' => $finalPrice
                        ]);
                    }
                } else {
                    // Sử dụng extra_price từ book_attribute_values (hệ thống cũ)
                    $attributeExtraPrice = DB::table('book_attribute_values')
                        ->whereIn('attribute_value_id', $validAttributeIds)
                        ->where('book_id', $bookId)
                        ->sum('extra_price');

                    $finalPrice += $attributeExtraPrice;

                    Log::info('Cart addToCart - Added attribute extra price (legacy system):', [
                        'book_id' => $bookId,
                        'attribute_ids' => $validAttributeIds,
                        'extra_price' => $attributeExtraPrice,
                        'final_price' => $finalPrice
                    ]);
                }
            } elseif (!empty($validAttributeIds) && $isEbook) {
                Log::info('Cart addToCart - Skipped attribute extra price for ebook:', [
                    'book_id' => $bookId,
                    'variant_id' => $variantId,
                    'attribute_ids' => $validAttributeIds,
                    'note' => 'Ebooks do not have extra price for variants'
                ]);
            }
            // dd($finalPrice);

            // Kiểm tra combo price (nếu sách thuộc combo đang hoạt động)
            // $comboInfo = DB::table('book_collections')
            //     ->join('collections', 'book_collections.collection_id', '=', 'collections.id')
            //     ->where('book_collections.book_id', $bookId)
            //     ->where(function($query) {
            //         $query->whereNull('collections.start_date')
            //               ->orWhere('collections.start_date', '<=', now());
            //     })
            //     ->where(function($query) {
            //         $query->whereNull('collections.end_date')
            //               ->orWhere('collections.end_date', '>=', now());
            //     })
            //     ->whereNull('collections.deleted_at')
            //     ->where('collections.combo_price', '>', 0)
            //     ->first();

            // if ($comboInfo && $comboInfo->combo_price < $finalPrice) {
            //     $finalPrice = $comboInfo->combo_price;
            //     Log::info('Applied combo pricing:', [
            //         'book_id' => $bookId,
            //         'original_price' => $bookInfo->price,
            //         'combo_price' => $comboInfo->combo_price,
            //         'collection_name' => $comboInfo->name ?? 'Unknown'
            //     ]);
            // }

            // Check: Giá tiền không được âm hoặc bằng 0
            if ($finalPrice <= 0) {
                return response()->json([
                    'error' => 'Giá sản phẩm không hợp lệ. Vui lòng liên hệ quản trị viên.'
                ], 422, ['Content-Type' => 'application/json']);
            }

            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            $existingCartQuery = Cart::where('user_id', Auth::id())
                ->where('book_id', $bookId)
                ->where('book_format_id', $bookFormatId);
            
            // Thêm điều kiện cho variant_id hoặc attribute_value_ids
            if ($variantId) {
                // Sử dụng hệ thống biến thể mới - tìm theo variant_id
                $existingCartQuery->where('variant_id', $variantId);
            } else {
                // Sử dụng hệ thống cũ - tìm theo attribute_value_ids
                $existingCartQuery->whereNull('variant_id'); // Đảm bảo không có variant_id
                if (!$isEbook) {
                    $existingCartQuery->whereJsonContains('attribute_value_ids', json_decode($attributeJson, true));
                }
            }
            
            $existingCart = $existingCartQuery->first();
            // dd($existingCart);   
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
                        'error' => 'Sách này đã có trong giỏ hàng',
                        // 'stock' => $bookInfo->stock,
                        // 'current_quantity' => 1,
                        // 'cart_count' => (int) DB::table('carts')->where('user_id', Auth::id())->sum('quantity')
                    ]);
                }
                // Kiểm tra tổng số lượng sau khi thêm (chỉ với sách vật lý)
                $newQuantity = $existingCart->quantity + $quantity;
                if (!$isEbook) {
                    // Kiểm tra tồn kho theo thứ tự phân cấp với tổng số lượng mới
                    if ($variantId) {
                        $stockValidation = $this->validateVariantStock($variantId, $newQuantity, $bookInfo);
                    } else {
                        $stockValidation = $this->validateHierarchicalStock(
                            $bookId, 
                            $bookFormatId, 
                            $validAttributeIds, 
                            $newQuantity, 
                            $bookInfo
                        );
                    }

                    if (!$stockValidation['success']) {
                        return response()->json([
                            'error' => $stockValidation['message'] . " (Số lượng trong giỏ: {$existingCart->quantity})",
                            'available_stock' => $stockValidation['available_stock'] ?? 0,
                            'current_cart_quantity' => $existingCart->quantity,
                            'stock_level' => $stockValidation['stock_level'] ?? null,
                            'variant_sku' => $stockValidation['variant_sku'] ?? null,
                            'variant_id' => $variantId
                        ], 422);
                    }

                    Log::info('Cart addToCart - Existing cart stock validation passed:', [
                        'book_id' => $bookId,
                        'variant_id' => $variantId,
                        'existing_quantity' => $existingCart->quantity,
                        'additional_quantity' => $quantity,
                        'total_quantity' => $newQuantity,
                        'validation_result' => $stockValidation
                    ]);
                }
                // Cập nhật số lượng và giá (case giá có thể thay đổi)
                DB::table('carts')
                    ->where('id', $existingCart->id)
                    ->update([
                        'quantity' => $newQuantity,
                        'price' => $finalPrice,
                        'updated_at' => now()
                    ]);

                // Get updated cart count
                $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();

                return response()->json([
                    'success' => 'Đã thêm ' . $quantity . ' sản phẩm "' . $bookInfo->title . '" vào giỏ hàng',
                    'stock' => $bookInfo->stock,
                    'current_quantity' => $newQuantity,
                    'cart_count' => (int) $cartCount
                ]);
            } else {
                // Nếu là ebook: luôn set quantity = 1
                if ($isEbook) {
                    $quantity = 1;
                }

                try {
                    $cartData = [
                        'id' => Str::uuid(),
                        'user_id' => Auth::id(),
                        'book_id' => $bookId,
                        'book_format_id' => $bookFormatId,
                        'quantity' => $quantity,
                        'price' => $finalPrice,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    // Thêm variant_id hoặc attribute_value_ids tùy theo hệ thống
                    if ($variantId) {
                        $cartData['variant_id'] = $variantId;
                        // Vẫn lưu attribute_value_ids để tương thích ngược
                        $cartData['attribute_value_ids'] = $attributeJson;
                    } else {
                        $cartData['attribute_value_ids'] = $attributeJson;
                    }

                    DB::table('carts')->insert($cartData);

                    // Get updated cart count
                    $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();

                    return response()->json([
                        'success' => 'Đã thêm sản phẩm "' . $bookInfo->title . '" vào giỏ hàng',
                        'stock' => $bookInfo->stock,
                        'current_quantity' => $quantity,
                        'cart_count' => (int) $cartCount
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Nếu bị duplicate key error (unique constraint violation)
                    if ($e->getCode() == 23000) {
                        // Thử lại với logic update
                        $existingCartQuery = DB::table('carts')
                            ->where('user_id', Auth::id())
                            ->where('book_id', $bookId)
                            ->where('book_format_id', $bookFormatId);
                        
                        if ($variantId) {
                            $existingCartQuery->where('variant_id', $variantId);
                        } else {
                            $existingCartQuery->whereNull('variant_id')
                                ->where('attribute_value_ids', $attributeJson);
                        }
                        
                        $existingCart = $existingCartQuery->first();

                        if ($existingCart) {
                            if ($isEbook) {
                                return response()->json([
                                    'success' => 'Sách điện tử đã có trong giỏ hàng',
                                    'stock' => $bookInfo->stock,
                                    'current_quantity' => 1
                                ]);
                            } else {
                                $newQuantity = $existingCart->quantity + $quantity;
                                
                                // Kiểm tra tồn kho theo thứ tự phân cấp
                                $stockValidation = $this->validateHierarchicalStock(
                                    $bookId, 
                                    $bookFormatId, 
                                    $validAttributeIds, 
                                    $newQuantity, 
                                    $bookInfo
                                );

                                if (!$stockValidation['success']) {
                                    return response()->json([
                                        'error' => $stockValidation['message'] . " (Số lượng trong giỏ: {$existingCart->quantity})",
                                        'available_stock' => $stockValidation['available_stock'] ?? 0,
                                        'current_cart_quantity' => $existingCart->quantity,
                                        'stock_level' => $stockValidation['stock_level'] ?? null,
                                        'variant_sku' => $stockValidation['variant_sku'] ?? null
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
            $errorMsg = 'Dữ liệu không hợp lệ: ' . implode(', ', $e->validator->errors()->all());
            if (request()->wantsJson()) {
                return response()->json(['error' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        } catch (\Exception $e) {
            Log::error('Error in addToCart:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            $errorMsg = 'Có lỗi xảy ra khi thêm vào giỏ hàng';
            if (request()->wantsJson()) {
                return response()->json(['error' => $errorMsg], 500);
            }
            return back()->with('error', $errorMsg);
        }
    }

    public function addComboToCart(Request $request)
    {
        if (!Auth::check()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Bạn cần đăng nhập để thêm combo vào giỏ hàng.'], 401);
            }
            return back()->with('error', 'Bạn cần đăng nhập để thêm combo vào giỏ hàng.');
        }

        try {
            Log::info('addComboToCart called with data:', $request->all());

            // Validate request data - accept both combo_id and collection_id
            $validated = $request->validate([
                'combo_id' => 'nullable|exists:collections,id',
                'collection_id' => 'nullable|exists:collections,id',
                'quantity' => 'required|integer|min:1',
                'type' => 'nullable|string'
            ]);

            // Use combo_id if available, otherwise use collection_id
            $collectionId = $validated['combo_id'] ?? $validated['collection_id'];

            if (!$collectionId) {
                if (request()->wantsJson()) {
                    return response()->json(['error' => 'Dữ liệu không hợp lệ: Không tìm thấy ID combo'], 422);
                }
                return back()->with('error', 'Dữ liệu không hợp lệ: Không tìm thấy ID combo');
            }

            $quantity = $validated['quantity'];

            Log::info('Validated data:', ['collection_id' => $collectionId, 'quantity' => $quantity]);

            // Lấy thông tin combo với kiểm tra ngày hiệu lực
            $combo = DB::table('collections')
                ->where('id', $collectionId)
                ->where('status', 'active')
                ->whereNotNull('combo_price')
                ->where(function ($query) {
                    $query->whereNull('start_date')
                        ->orWhere('start_date', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('end_date')
                        ->orWhere('end_date', '>=', now());
                })
                ->whereNull('deleted_at')
                ->first();

            Log::info('Combo found:', ['combo' => $combo]);

            if ($combo && $combo->combo_price <= 0) {
                $errorMsg = 'Giá combo không hợp lệ. Vui lòng liên hệ quản trị viên.';
                if (request()->wantsJson()) {
                    return response()->json(['error' => $errorMsg], 422);
                }
                return back()->with('error', $errorMsg);
            }

            if (!$combo) {
                // Kiểm tra xem combo có tồn tại nhưng không trong thời gian hiệu lực không
                $expiredCombo = DB::table('collections')
                    ->where('id', $collectionId)
                    ->whereNotNull('combo_price')
                    ->whereNull('deleted_at')
                    ->first();

                if ($expiredCombo) {
                    $now = now();
                    $startDate = $expiredCombo->start_date ? \Carbon\Carbon::parse($expiredCombo->start_date) : null;
                    $endDate = $expiredCombo->end_date ? \Carbon\Carbon::parse($expiredCombo->end_date) : null;

                    if ($startDate && $now < $startDate) {
                        $errorMsg = 'Combo chưa bắt đầu. Thời gian bắt đầu: ' . $startDate->format('d/m/Y H:i');
                        if (request()->wantsJson()) {
                            return response()->json(['error' => $errorMsg], 422);
                        }
                        return back()->with('error', $errorMsg);
                    }

                    if ($endDate && $now > $endDate) {
                        $errorMsg = 'Combo đã kết thúc. Thời gian kết thúc: ' . $endDate->format('d/m/Y H:i');
                        if (request()->wantsJson()) {
                            return response()->json(['error' => $errorMsg], 422);
                        }
                        return back()->with('error', $errorMsg);
                    }

                    if ($expiredCombo->status !== 'active') {
                        $errorMsg = 'Combo hiện không khả dụng';
                        if (request()->wantsJson()) {
                            return response()->json(['error' => $errorMsg], 422);
                        }
                        return back()->with('error', $errorMsg);
                    }
                }

                $errorMsg = 'Combo không tồn tại hoặc không còn khả dụng';
                if (request()->wantsJson()) {
                    return response()->json(['error' => $errorMsg], 404);
                }
                return back()->with('error', $errorMsg);
            }

            // Kiểm tra số lượng tồn kho combo
            if ($combo->combo_stock !== null) {
                // Lấy tổng số lượng combo đã có trong giỏ hàng
                $existingQuantity = DB::table('carts')
                    ->where('user_id', Auth::id())
                    ->where('collection_id', $collectionId)
                    ->where('is_combo', true)
                    ->sum('quantity');

                $totalRequestedQuantity = $existingQuantity + $quantity;

                if ($combo->combo_stock <= 0) {
                    $errorMsg = 'Combo đã hết hàng';
                    if (request()->wantsJson()) {
                        return response()->json(['error' => $errorMsg], 422);
                    }
                    return back()->with('error', $errorMsg);
                }

                if ($totalRequestedQuantity > $combo->combo_stock) {
                    $errorMsg = "Số lượng yêu cầu vượt quá số lượng tồn kho. Tồn kho hiện tại: {$combo->combo_stock}, bạn đã có {$existingQuantity} trong giỏ hàng";
                    if (request()->wantsJson()) {
                        return response()->json([
                            'error' => $errorMsg,
                            'available_stock' => $combo->combo_stock,
                            'current_in_cart' => $existingQuantity
                        ], 422);
                    }
                    return back()->with('error', $errorMsg);
                }
            }

            // Kiểm tra xem combo đã có trong giỏ hàng chưa
            $existingCart = DB::table('carts')
                ->where('user_id', Auth::id())
                ->where('collection_id', $collectionId)
                ->where('is_combo', true)
                ->first();

            Log::info('Existing cart check:', ['existing' => $existingCart ? 'yes' : 'no']);

            if ($existingCart) {
                // Cập nhật số lượng
                $newQuantity = $existingCart->quantity + $quantity;

                Log::info('Updating existing cart:', ['old_quantity' => $existingCart->quantity, 'new_quantity' => $newQuantity]);

                try {
                    $updateResult = DB::table('carts')
                        ->where('id', $existingCart->id)
                        ->update([
                            'quantity' => $newQuantity,
                            'price' => $combo->combo_price,
                            'updated_at' => now()
                        ]);

                    Log::info('Cart update result:', ['affected_rows' => $updateResult]);

                    // Get updated cart count
                    $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();

                    Log::info('Cart count calculated:', ['cart_count' => $cartCount]);

                    $successMessage = 'Đã thêm ' . $quantity . ' combo "' . $combo->name . '" vào giỏ hàng';

                    // Check if request wants JSON
                    $wantsJson = request()->wantsJson();
                    $hasAjaxHeader = request()->ajax();
                    $acceptsJson = request()->accepts(['application/json']);

                    Log::info('Response format check:', [
                        'wantsJson' => $wantsJson,
                        'hasAjaxHeader' => $hasAjaxHeader,
                        'acceptsJson' => $acceptsJson,
                        'headers' => request()->headers->all()
                    ]);

                    if ($wantsJson || $hasAjaxHeader) {
                        $response = response()->json([
                            'success' => $successMessage,
                            'current_quantity' => $newQuantity,
                            'cart_count' => (int) $cartCount
                        ]);

                        Log::info('Returning JSON response:', $response->getData(true));
                        return $response;
                    }

                    Log::info('Returning redirect response');
                    return back()->with('success', $successMessage);
                } catch (\Exception $dbException) {
                    Log::error('Database error in cart update:', [
                        'error' => $dbException->getMessage(),
                        'trace' => $dbException->getTraceAsString()
                    ]);

                    if (request()->wantsJson()) {
                        return response()->json(['error' => 'Lỗi cập nhật giỏ hàng: ' . $dbException->getMessage()], 500);
                    }
                    return back()->with('error', 'Lỗi cập nhật giỏ hàng: ' . $dbException->getMessage());
                }
            } else {
                // Thêm combo mới vào giỏ hàng
                Log::info('Adding new combo to cart:', [
                    'user_id' => Auth::id(),
                    'collection_id' => $collectionId,
                    'quantity' => $quantity,
                    'price' => $combo->combo_price
                ]);

                $cartId = Str::uuid();

                $insertData = [
                    'id' => $cartId,
                    'user_id' => Auth::id(),
                    'book_id' => null,
                    'book_format_id' => null,
                    'collection_id' => $collectionId,
                    'is_combo' => true,
                    'quantity' => $quantity,
                    'attribute_value_ids' => json_encode([]),
                    'price' => $combo->combo_price,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                Log::info('Insert data prepared:', $insertData);

                $result = DB::table('carts')->insert($insertData);

                Log::info('Insert result:', ['success' => $result]);

                // Get updated cart count
                $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();

                Log::info('Cart count after insert:', ['cart_count' => $cartCount]);

                $successMessage = 'Đã thêm combo "' . $combo->name . '" vào giỏ hàng';

                // Check if request wants JSON
                $wantsJson = request()->wantsJson();
                $hasAjaxHeader = request()->ajax();

                Log::info('Response format check (new combo):', [
                    'wantsJson' => $wantsJson,
                    'hasAjaxHeader' => $hasAjaxHeader
                ]);

                if ($wantsJson || $hasAjaxHeader) {
                    $response = response()->json([
                        'success' => $successMessage,
                        'current_quantity' => $quantity,
                        'cart_count' => (int) $cartCount
                    ]);

                    Log::info('Returning JSON response (new combo):', $response->getData(true));
                    return $response;
                }

                Log::info('Returning redirect response (new combo)');
                return back()->with('success', $successMessage);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in addComboToCart:', [
                'errors' => $e->validator->errors()->all(),
                'request_data' => $request->all()
            ]);

            $errorMsg = 'Dữ liệu không hợp lệ: ' . implode(', ', $e->validator->errors()->all());
            if (request()->wantsJson()) {
                return response()->json(['error' => $errorMsg], 422);
            }
            return back()->with('error', $errorMsg);
        } catch (\Exception $e) {
            Log::error('Error in addComboToCart:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            $errorMsg = 'Có lỗi xảy ra khi thêm combo vào giỏ hàng';
            if (request()->wantsJson()) {
                return response()->json(['error' => $errorMsg], 500);
            }
            return back()->with('error', $errorMsg);
        }
    }

    public function updateCart(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Bạn cần đăng nhập để cập nhật giỏ hàng.'], 401);
            }

            // Debug logging
            Log::info('UpdateCart request received:', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            $isCombo = $request->boolean('is_combo', false);
            $quantity = (int)$request->quantity;

            if ($isCombo) {
                // Xử lý cập nhật số lượng combo
                $collectionId = $request->collection_id;
                $cartId = $request->cart_id;

                if (!$collectionId && !$cartId) {
                    return response()->json(['error' => 'Thiếu thông tin combo để cập nhật.'], 400);
                }

                // Lấy thông tin cart item combo - ưu tiên cart_id
                if ($cartId) {
                    $cartItem = DB::table('carts')
                        ->where('user_id', Auth::id())
                        ->where('id', $cartId)
                        ->where('is_combo', true)
                        ->first();
                        
                    Log::info('Found combo cart item by cart_id:', [
                        'cart_id' => $cartId,
                        'found' => $cartItem ? 'yes' : 'no'
                    ]);
                } else {
                    // Fallback sử dụng collection_id
                    $cartItem = DB::table('carts')
                        ->where('user_id', Auth::id())
                        ->where('collection_id', $collectionId)
                        ->where('is_combo', true)
                        ->first();
                        
                    Log::info('Found combo cart item by collection_id:', [
                        'collection_id' => $collectionId,
                        'found' => $cartItem ? 'yes' : 'no'
                    ]);
                }

                if (!$cartItem) {
                    return response()->json(['error' => 'Không tìm thấy combo trong giỏ hàng'], 404);
                }

                // Lấy collection_id từ cart item
                $collectionId = $cartItem->collection_id;

                // Lấy thông tin combo với kiểm tra ngày hiệu lực
                $combo = DB::table('collections')
                    ->where('id', $collectionId)
                    ->where('status', 'active')
                    ->whereNotNull('combo_price')
                    ->where(function ($query) {
                        $query->whereNull('start_date')
                            ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                            ->orWhere('end_date', '>=', now());
                    })
                    ->whereNull('deleted_at')
                    ->first();

                if (!$combo) {
                    return response()->json(['error' => 'Combo không còn khả dụng hoặc đã hết thời gian hiệu lực'], 404);
                }

                if ($quantity > 0) {
                    // Kiểm tra số lượng tồn kho combo
                    if ($combo->combo_stock !== null) {
                        if ($combo->combo_stock <= 0) {
                            return response()->json(['error' => 'Combo đã hết hàng'], 422);
                        }

                        if ($quantity > $combo->combo_stock) {
                            return response()->json([
                                'error' => "Số lượng yêu cầu vượt quá số lượng tồn kho. Tồn kho hiện tại: {$combo->combo_stock}",
                                'available_stock' => $combo->combo_stock
                            ], 422);
                        }
                    }

                    // Update combo quantity - ưu tiên sử dụng cart_id
                    if ($cartId) {
                        $updateResult = DB::table('carts')
                            ->where('user_id', Auth::id())
                            ->where('id', $cartId)
                            ->update([
                                'quantity' => $quantity,
                                'price' => $combo->combo_price,
                                'updated_at' => now()
                            ]);
                    } else {
                        // Fallback sử dụng collection_id
                        $updateResult = DB::table('carts')
                            ->where('user_id', Auth::id())
                            ->where('collection_id', $collectionId)
                            ->where('is_combo', true)
                            ->update([
                                'quantity' => $quantity,
                                'price' => $combo->combo_price,
                                'updated_at' => now()
                            ]);
                    }

                    // Lấy số lượng cart đã cập nhật
                    $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();

                    return response()->json([
                        'success' => 'Đã cập nhật số lượng combo',
                        'data' => [
                            'price' => $combo->combo_price,
                            'quantity' => $quantity,
                            'is_combo' => true
                        ],
                        'cart_count' => (int) $cartCount
                    ]);
                } else {
                    // Xóa combo khi số lượng = 0
                    $deletedCount = DB::table('carts')
                        ->where('user_id', Auth::id())
                        ->where('collection_id', $collectionId)
                        ->where('is_combo', true)
                        ->delete();

                    if ($deletedCount > 0) {
                        $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();
                        return response()->json([
                            'success' => 'Đã xóa combo khỏi giỏ hàng',
                            'cart_count' => (int) $cartCount
                        ]);
                    } else {
                        return response()->json(['error' => 'Không tìm thấy combo để xóa'], 404);
                    }
                }
            } else {
                // Xử lý cập nhật số lượng sách đơn lẻ (logic hiện tại)
                $bookId = $request->book_id;
                $bookFormatId = $request->book_format_id;
                $attributeValueIds = $request->attribute_value_ids;

                // Ưu tiên sử dụng cart_id nếu có (method đáng tin cậy nhất)
                $cartId = $request->cart_id;

                Log::info('Processing book update:', [
                    'book_id' => $bookId,
                    'book_format_id' => $bookFormatId,
                    'cart_id' => $cartId,
                    'quantity' => $quantity,
                    'attribute_value_ids' => $attributeValueIds
                ]);

                if ($cartId) {
                    // Sử dụng cart_id để tìm cart item trực tiếp - đây là method đáng tin cậy nhất
                    $cartItem = Cart::where('user_id', Auth::id())
                        ->where('id', $cartId)
                        ->where('is_combo', 0)
                        ->first();
                        
                    Log::info('Found cart item by cart_id:', [
                        'cart_id' => $cartId,
                        'found' => $cartItem ? 'yes' : 'no'
                    ]);
                } else {
                    // Fallback: sử dụng thông tin book để tìm cart item
                    $cartItemQuery = Cart::where('user_id', Auth::id())
                        ->where('book_id', $bookId)
                        ->where('is_combo', 0);

                    if ($bookFormatId) {
                        $cartItemQuery->where('book_format_id', $bookFormatId);
                    }

                    // Hỗ trợ cả hệ thống mới và cũ
                    if ($attributeValueIds) {
                        // Chuẩn hóa attribute_value_ids để so sánh
                        if (is_string($attributeValueIds)) {
                            $attributeValueIds = json_decode($attributeValueIds, true);
                        }
                        if (is_array($attributeValueIds)) {
                            $attributeValueIds = json_encode($attributeValueIds);
                        }
                        $cartItemQuery->where('attribute_value_ids', $attributeValueIds);
                    }

                    $cartItem = $cartItemQuery->first();
                    
                    // Nếu không tìm thấy bằng attribute_value_ids, thử tìm bằng variant_id
                    if (!$cartItem) {
                        // Tìm bất kỳ cart item nào của sách này có variant_id
                        $cartItem = Cart::where('user_id', Auth::id())
                            ->where('book_id', $bookId)
                            ->where('book_format_id', $bookFormatId)
                            ->where('is_combo', 0)
                            ->whereNotNull('variant_id')
                            ->first();
                    }
                    
                    Log::info('Found cart item by book info:', [
                        'book_id' => $bookId,
                        'book_format_id' => $bookFormatId,
                        'found' => $cartItem ? 'yes' : 'no'
                    ]);
                }
                if (!$cartItem) {
                    Log::error('Cart item not found:', [
                        'cart_id' => $cartId,
                        'book_id' => $bookId,
                        'book_format_id' => $bookFormatId,
                        'user_id' => Auth::id()
                    ]);
                    return response()->json(['error' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
                }
                
                // Lấy thông tin sách từ cart item
                $bookId = $cartItem->book_id;
                $bookFormatId = $cartItem->book_format_id;
                
                // Kiểm tra tồn kho và thông tin sách với format cụ thể
                $bookInfo = DB::table('books')
                    ->leftJoin('book_formats', function ($join) use ($bookFormatId) {
                        $join->on('books.id', '=', 'book_formats.book_id')
                            ->where('book_formats.id', '=', $bookFormatId);
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

                // Tính giá hiện tại (bao gồm discount và extra_price từ variants)
                $currentPrice = $bookInfo->price;
                if (isset($bookInfo->discount) && $bookInfo->discount > 0) {
                    $currentPrice = $bookInfo->price - $bookInfo->discount;
                }

                // Add extra price from variants (chỉ cho sách vật lý)
                if (!$isEbook) {
                    // Priority: New variant system (variant_id) > Legacy system (attribute_value_ids)
                    if (!empty($cartItem->variant_id)) {
                        // NEW SYSTEM: Lấy extra price từ book_variants
                        $variantInfo = DB::table('book_variants')
                            ->where('id', $cartItem->variant_id)
                            ->first();
                        
                        if ($variantInfo) {
                            $currentPrice += $variantInfo->extra_price;
                        }
                    } elseif (!empty($cartItem->attribute_value_ids) && $cartItem->attribute_value_ids !== '[]') {
                        // LEGACY SYSTEM: Lấy extra price từ book_attribute_values
                        $attributeIds = json_decode($cartItem->attribute_value_ids, true);
                        if ($attributeIds && is_array($attributeIds)) {
                            $extraPrice = DB::table('book_attribute_values')
                                ->whereIn('attribute_value_id', $attributeIds)
                                ->where('book_id', $cartItem->book_id)
                                ->sum('extra_price');
                            $currentPrice += $extraPrice;
                        }
                    }
                }

                // Kiểm tra combo price
                $comboInfo = DB::table('book_collections')
                    ->join('collections', 'book_collections.collection_id', '=', 'collections.id')
                    ->where('book_collections.book_id', $bookId)
                    ->where(function ($query) {
                        $query->whereNull('collections.start_date')
                            ->orWhere('collections.start_date', '<=', now());
                    })
                    ->where(function ($query) {
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
                    if ($cartId) {
                        // Sử dụng cart_id để cập nhật trực tiếp
                        $updateQuery = DB::table('carts')
                            ->where('user_id', Auth::id())
                            ->where('id', $cartId);
                    } else {
                        // Fallback: sử dụng thông tin book để cập nhật
                        $updateQuery = DB::table('carts')
                            ->where('user_id', Auth::id())
                            ->where('book_id', $bookId)
                            ->where('book_format_id', $cartItem->book_format_id);

                        if ($cartItem->attribute_value_ids) {
                            // Xử lý attribute_value_ids an toàn cho ebook
                            if (is_string($cartItem->attribute_value_ids)) {
                                $attributeIds = json_decode($cartItem->attribute_value_ids, true);
                            } else {
                                $attributeIds = $cartItem->attribute_value_ids;
                            }

                            if (is_array($attributeIds) && !empty($attributeIds)) {
                                // So sánh bằng JSON string
                                $updateQuery->where('attribute_value_ids', json_encode($attributeIds));
                            }
                        }
                    }

                    $updateQuery->update([
                        'quantity' => 1,
                        'price' => $currentPrice, // Cập nhật giá hiện tại
                        'updated_at' => now()
                    ]);

                    // Lấy số lượng cart đã cập nhật
                    $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();

                    return response()->json([
                        'success' => 'Sách điện tử luôn có số lượng cố định là 1',
                        'data' => [
                            'stock' => 999, // Ebook không giới hạn tồn kho
                            'price' => $currentPrice,
                            'quantity' => 1,
                            'is_ebook' => true
                        ],
                        'cart_count' => (int) $cartCount
                    ]);
                } else {
                    // SÁCH VẬT LÝ: Kiểm tra tồn kho và cho phép thay đổi số lượng

                    // Kiểm tra biến thể nếu có attribute_value_ids
                    if ($cartItem->attribute_value_ids) {
                        // Safe handling for attribute_value_ids - check if it's string or array
                        if (is_string($cartItem->attribute_value_ids)) {
                            $attributeIds = json_decode($cartItem->attribute_value_ids, true);
                        } else {
                            $attributeIds = $cartItem->attribute_value_ids; // Already an array
                        }
                    } else {
                        $attributeIds = [];
                    }

                    // Sử dụng logic kiểm tra tồn kho: ưu tiên hệ thống mới > hệ thống cũ
                    if (!empty($cartItem->variant_id)) {
                        // NEW SYSTEM: Sử dụng validateVariantStock cho hệ thống biến thể mới
                        $stockValidation = $this->validateVariantStock($cartItem->variant_id, $quantity, $bookInfo);
                    } else {
                        // LEGACY SYSTEM: Sử dụng validateHierarchicalStock cho hệ thống cũ
                        $stockValidation = $this->validateHierarchicalStock(
                            $bookId, 
                            $bookFormatId, 
                            $attributeIds, 
                            $quantity, 
                            $bookInfo
                        );
                    }

                    if (!$stockValidation['success']) {
                        return response()->json([
                            'error' => $stockValidation['message'],
                            'available_stock' => $stockValidation['available_stock'] ?? 0,
                            'stock_level' => $stockValidation['stock_level'] ?? null,
                            'variant_sku' => $stockValidation['variant_sku'] ?? null,
                            'variant_id' => $cartItem->variant_id ?? null,
                            'is_ebook' => false
                        ], 422);
                    }

                    Log::info('Cart updateCart - Stock validation passed:', [
                        'book_id' => $bookId,
                        'variant_id' => $cartItem->variant_id ?? null,
                        'selected_variants' => $attributeIds,
                        'requested_quantity' => $quantity,
                        'validation_result' => $stockValidation,
                        'system_used' => !empty($cartItem->variant_id) ? 'new_variant_system' : 'legacy_system',
                        'is_ebook' => false
                    ]);

                    if ($quantity > 0) {
                        // Cập nhật số lượng cho sách vật lý
                        if ($cartId) {
                            // Sử dụng cart_id để cập nhật trực tiếp (method đáng tin cậy nhất)
                            $updateQuery = DB::table('carts')
                                ->where('user_id', Auth::id())
                                ->where('id', $cartId);
                        } else {
                            // Fallback: sử dụng thông tin book để cập nhật
                            $updateQuery = DB::table('carts')
                                ->where('user_id', Auth::id())
                                ->where('book_id', $bookId)
                                ->where('book_format_id', $cartItem->book_format_id);

                            // Hỗ trợ cả hệ thống mới và cũ
                            if (!empty($cartItem->variant_id)) {
                                // NEW SYSTEM: Match bằng variant_id
                                $updateQuery->where('variant_id', $cartItem->variant_id);
                            } elseif ($cartItem->attribute_value_ids) {
                                // LEGACY SYSTEM: Match bằng attribute_value_ids
                                if (is_string($cartItem->attribute_value_ids)) {
                                    $attributeIds = json_decode($cartItem->attribute_value_ids, true);
                                } else {
                                    $attributeIds = $cartItem->attribute_value_ids;
                                }

                                if (is_array($attributeIds) && !empty($attributeIds)) {
                                    // So sánh bằng JSON string
                                    $updateQuery->where('attribute_value_ids', json_encode($attributeIds));
                                }
                            }
                        }
                        // dd($quantity, $currentPrice);
                        $updateQuery->update([
                            'quantity' => $quantity,
                            'price' => $currentPrice, // Cậ nhật giá mới (có thể là combo price)
                            'updated_at' => now()
                        ]);
                        // Lấy số lượng cart đã cập nhật
                        $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();
                        // dd($cartCount);

                        return response()->json([
                            'success' => 'Đã cập nhật số lượng sản phẩm',
                            'data' => [
                                'stock' => $bookInfo->stock,
                                'price' => $currentPrice, // Trả về giá đã được áp dụng combo (nếu có)
                                'quantity' => $quantity,
                                'is_ebook' => false
                            ],
                            'cart_count' => (int) $cartCount
                        ]);
                    } else {
                        // Xóa sản phẩm khi số lượng = 0
                        if ($cartId) {
                            // Sử dụng cart_id để xóa trực tiếp
                            $deleteQuery = DB::table('carts')
                                ->where('user_id', Auth::id())
                                ->where('id', $cartId);
                        } else {
                            // Fallback: sử dụng thông tin book để xóa
                            $deleteQuery = DB::table('carts')
                                ->where('user_id', Auth::id())
                                ->where('book_id', $bookId)
                                ->where('book_format_id', $cartItem->book_format_id);

                            // Hỗ trợ cả hệ thống mới và cũ
                            if (!empty($cartItem->variant_id)) {
                                // NEW SYSTEM: Match bằng variant_id
                                $deleteQuery->where('variant_id', $cartItem->variant_id);
                            } elseif ($cartItem->attribute_value_ids) {
                                // LEGACY SYSTEM: Match bằng attribute_value_ids
                                if (is_string($cartItem->attribute_value_ids)) {
                                    $attributeIds = json_decode($cartItem->attribute_value_ids, true);
                                } else {
                                    $attributeIds = $cartItem->attribute_value_ids;
                                }

                                if (is_array($attributeIds) && !empty($attributeIds)) {
                                    // So sánh bằng JSON string
                                    $deleteQuery->where('attribute_value_ids', json_encode($attributeIds));
                                }
                            }
                        }

                        $deletedCount = $deleteQuery->delete();

                        if ($deletedCount > 0) {
                            $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();
                            return response()->json([
                                'success' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                                'cart_count' => (int) $cartCount
                            ]);
                        } else {
                            return response()->json(['error' => 'Không tìm thấy sản phẩm để xóa'], 404);
                        }
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
            $isCombo = $request->boolean('is_combo', false);
            $collectionId = $request->collection_id;

            if ($isCombo) {
                if (!$collectionId) {
                    return response()->json(['error' => 'Thiếu thông tin combo để xóa.'], 400);
                }

                Log::info('Removing combo from cart:', [
                    'user_id' => Auth::id(),
                    'collection_id' => $collectionId
                ]);

                $deletedCount = DB::table('carts')
                    ->where('user_id', Auth::id())
                    ->where('collection_id', $collectionId)
                    ->where('is_combo', true)
                    ->delete();

                if ($deletedCount > 0) {
                    $cartCount = DB::table('carts')->where('user_id', Auth::id())->count();
                    Log::info('Combo removed successfully:', [
                        'deleted_count' => $deletedCount,
                        'remaining_cart_count' => $cartCount
                    ]);
                    return response()->json([
                        'success' => 'Đã xóa combo khỏi giỏ hàng',
                        'cart_count' => (int) $cartCount
                    ]);
                } else {
                    Log::warning('Không tìm thấy combo để xóa');
                    return response()->json(['error' => 'Không tìm thấy combo trong giỏ hàng'], 404);
                }
            } else {
                // Xóa sách đơn lẻ
                $bookId = $request->book_id;
                $bookFormatId = $request->book_format_id;
                $attributeValueIds = $request->attribute_value_ids;
                $cartId = $request->cart_id; // Priority: use cart_id if available
                $variantId = $request->variant_id; // Support new variant system

                if (!$bookId && !$cartId) {
                    return response()->json(['error' => 'Thiếu thông tin sách để xóa.'], 400);
                }

                Log::info('Removing book from cart:', [
                    'user_id' => Auth::id(),
                    'cart_id' => $cartId,
                    'book_id' => $bookId,
                    'book_format_id' => $bookFormatId,
                    'variant_id' => $variantId,
                    'attribute_value_ids' => $attributeValueIds
                ]);

                $query = DB::table('carts')
                    ->where('user_id', Auth::id())
                    ->where('is_combo', false);

                if ($cartId) {
                    // MOST RELIABLE: Use cart_id to delete specific cart item
                    $query->where('id', $cartId);
                    Log::info('Using cart_id for deletion', ['cart_id' => $cartId]);
                } else {
                    // FALLBACK: Use book info to find cart item
                    $query->where('book_id', $bookId);
                    
                    if ($bookFormatId) {
                        $query->where('book_format_id', $bookFormatId);
                    }

                    // Support both new variant system and legacy system
                    if ($variantId) {
                        // NEW SYSTEM: Match by variant_id
                        $query->where('variant_id', $variantId);
                        Log::info('Using variant_id for deletion', ['variant_id' => $variantId]);
                    } elseif ($attributeValueIds) {
                        // LEGACY SYSTEM: Match by attribute_value_ids
                        $query->whereNull('variant_id'); // Ensure no variant_id for legacy items
                        
                        if (is_string($attributeValueIds)) {
                            $attributeValueIdsArray = json_decode($attributeValueIds, true);
                        } else {
                            $attributeValueIdsArray = $attributeValueIds;
                        }
                        
                        if (is_array($attributeValueIdsArray) && !empty($attributeValueIdsArray)) {
                            // Use JSON comparison for exact match
                            $query->where('attribute_value_ids', json_encode($attributeValueIdsArray));
                            Log::info('Using attribute_value_ids for deletion', [
                                'attribute_value_ids' => $attributeValueIdsArray
                            ]);
                        }
                    } else {
                        // No variants: match items without variant_id and without attribute_value_ids
                        $query->whereNull('variant_id')
                              ->where(function($q) {
                                  $q->whereNull('attribute_value_ids')
                                    ->orWhere('attribute_value_ids', '[]')
                                    ->orWhere('attribute_value_ids', '');
                              });
                        Log::info('Deleting non-variant book');
                    }
                }

                $deletedCount = $query->delete();

                if ($deletedCount > 0) {
                    $cartCount = DB::table('carts')->where('user_id', Auth::id())->sum('quantity');
                    return response()->json([
                        'success' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                        'cart_count' => (int) $cartCount
                    ]);
                } else {
                    return response()->json(['error' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
                }
            }
        } catch (\Exception $e) {
            Log::error('Lỗi trong removeFromCart:', [
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
                'deleted_count' => $deletedCount,
                'cart_count' => 0
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
                return response()->json(['error' => 'Cần có Book ID'], 400);
            }

            $comboInfo = DB::table('book_collections')
                ->join('collections', 'book_collections.collection_id', '=', 'collections.id')
                ->where('book_collections.book_id', $bookId)
                ->where(function ($query) {
                    $query->whereNull('collections.start_date')
                        ->orWhere('collections.start_date', '<=', now());
                })
                ->where(function ($query) {
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
                    'message' => 'Không tìm thấy combo đang hoạt động cho cuốn sách này'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi lấy thông tin combo:', [
                'error' => $e->getMessage(),
                'book_id' => $request->book_id ?? null
            ]);
            return response()->json(['error' => 'Lỗi máy chủ'], 500);
        }
    }

    /**
     * Lấy số lượng item trong giỏ hàng cho các request AJAX
     */
    public function getCartCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $user = Auth::user();

        // Đếm số loại sản phẩm khác nhau trong giỏ hàng (không phải tổng số lượng)
        $distinctCount = DB::table('carts')
            ->where('user_id', $user->id)
            ->count();

        return response()->json(['count' => (int) $distinctCount]);
    }

    /**
     * API cập nhật trạng thái chọn sản phẩm trong giỏ hàng
     */
    public function updateSelected(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'is_selected' => 'required|boolean',
        ]);
        $cartId = $request->input('cart_id');
        $isSelected = $request->input('is_selected');
        $userId = Auth::id();
        $cart = DB::table('carts')->where('id', $cartId)->where('user_id', $userId)->first();
        if (!$cart) {
            return response()->json(['error' => 'Không tìm thấy sản phẩm trong giỏ hàng'], 404);
        }
        DB::table('carts')->where('id', $cartId)->update(['is_selected' => $isSelected]);
        return response()->json(['success' => 'Cập nhật trạng thái chọn sản phẩm thành công']);
    }

    /**
     * Làm mới giá cart từ database để đồng bộ với giá hiện tại
     */
    public function refreshCartPrices(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Bạn cần đăng nhập'], 401);
            }

            $user = Auth::user();
            $updatedCount = 0;
            $errors = [];

            // Lấy tất cả cart items của user
            $cartItems = DB::table('carts')
                ->where('user_id', $user->id)
                ->get();

            foreach ($cartItems as $cartItem) {
                if ($cartItem->is_combo) {
                    // Xử lý combo
                    $combo = DB::table('collections')
                        ->where('id', $cartItem->collection_id)
                        ->whereNull('deleted_at')
                        ->first();

                    if ($combo && $combo->combo_price > 0) {
                        if ($cartItem->price != $combo->combo_price) {
                            DB::table('carts')
                                ->where('id', $cartItem->id)
                                ->update([
                                    'price' => $combo->combo_price,
                                    'updated_at' => now()
                                ]);
                            $updatedCount++;
                        }
                    }
                } else {
                    // Xử lý sách đơn lẻ
                    $bookFormat = DB::table('book_formats')
                        ->where('id', $cartItem->book_format_id)
                        ->first();

                    if ($bookFormat) {
                        // Tính giá cuối cùng sau khi áp dụng discount (nếu có)
                        $finalPrice = $bookFormat->price;
                        if (isset($bookFormat->discount) && $bookFormat->discount > 0) {
                            // Discount là số tiền VNĐ trực tiếp, không phải phần trăm
                            $finalPrice = $bookFormat->price - $bookFormat->discount;
                            // Đảm bảo giá không âm
                            $finalPrice = max(0, $finalPrice);
                        }

                        // Thêm extra price từ biến thể nếu có (chỉ cho sách vật lý)
                        // Priority: New variant system (variant_id) > Legacy system (attribute_value_ids)
                        if (!empty($cartItem->variant_id)) {
                            // NEW SYSTEM: Sử dụng book_variants table
                            $variantInfo = DB::table('book_variants')
                                ->where('id', $cartItem->variant_id)
                                ->first();
                            
                            if ($variantInfo) {
                                // Check if this is a physical book (not ebook)
                                $isEbook = $bookFormat->format_name && stripos($bookFormat->format_name, 'ebook') !== false;
                                if (!$isEbook) {
                                    $finalPrice += $variantInfo->extra_price;
                                }
                            }
                        } elseif ($cartItem->attribute_value_ids && $cartItem->attribute_value_ids !== '[]') {
                            // LEGACY SYSTEM: Sử dụng book_attribute_values table
                            $attributeIds = json_decode($cartItem->attribute_value_ids, true);
                            if ($attributeIds && is_array($attributeIds)) {
                                // Kiểm tra xem có phải ebook không
                                $isEbook = $bookFormat->format_name && stripos($bookFormat->format_name, 'ebook') !== false;
                                
                                if (!$isEbook) {
                                    // Chỉ thêm extra price cho sách vật lý
                                    $extraPrice = DB::table('book_attribute_values')
                                        ->whereIn('attribute_value_id', $attributeIds)
                                        ->where('book_id', $cartItem->book_id)
                                        ->sum('extra_price');
                                    $finalPrice += $extraPrice;
                                }
                            }
                        }

                        if ($cartItem->price != $finalPrice) {
                            DB::table('carts')
                                ->where('id', $cartItem->id)
                                ->update([
                                    'price' => $finalPrice,
                                    'updated_at' => now()
                                ]);
                            $updatedCount++;
                        }
                    } else {
                        $errors[] = "Không tìm thấy định dạng sách cho cart item ID: {$cartItem->id}";
                    }
                }
            }

            // Tính lại tổng giỏ hàng
            $newTotal = 0;
            $refreshedCartItems = DB::table('carts')
                ->where('user_id', $user->id)
                ->where('is_selected', true)
                ->get();

            foreach ($refreshedCartItems as $item) {
                $newTotal += $item->price * $item->quantity;
            }

            $message = $updatedCount > 0 
                ? "Đã cập nhật giá cho {$updatedCount} sản phẩm trong giỏ hàng"
                : "Giá tất cả sản phẩm trong giỏ hàng đã được cập nhật";

            return response()->json([
                'success' => $message,
                'updated_count' => $updatedCount,
                'new_total' => $newTotal,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error in refreshCartPrices:', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'Có lỗi xảy ra khi làm mới giá giỏ hàng'], 500);
        }
    }

    /**
     * Kiểm tra tồn kho theo thứ tự phân cấp
     * 
     * @param string $bookId
     * @param string|null $bookFormatId
     * @param array $attributeValueIds
     * @param int $requestedQuantity
     * @param object $bookInfo
     * @return array
     */
    private function validateHierarchicalStock($bookId, $bookFormatId, $attributeValueIds, $requestedQuantity, $bookInfo)
    {
        Log::info('=== HIERARCHICAL STOCK VALIDATION START ===', [
            'book_id' => $bookId,
            'book_format_id' => $bookFormatId,
            'attribute_value_ids' => $attributeValueIds,
            'requested_quantity' => $requestedQuantity
        ]);

        // CẤPX 1: Kiểm tra tồn kho định dạng (books.formats.stock)
        $formatStock = (int) $bookInfo->stock;
        
        Log::info('LEVEL 1 - Format Stock Check:', [
            'format_stock' => $formatStock,
            'requested_quantity' => $requestedQuantity
        ]);

        // Kiểm tra định dạng có hết hàng không
        if ($formatStock <= 0) {
            return [
                'success' => false,
                'message' => 'Định dạng sách này hiện đã hết hàng',
                'available_stock' => 0,
                'stock_level' => 'format'
            ];
        }

        // Kiểm tra số lượng yêu cầu có vượt quá tồn kho định dạng không
        if ($requestedQuantity > $formatStock) {
            return [
                'success' => false,
                'message' => "Số lượng yêu cầu vượt quá tồn kho định dạng. Tồn kho hiện tại: {$formatStock}",
                'available_stock' => $formatStock,
                'stock_level' => 'format'
            ];
        }

        // Thiết lập giá trị stock khả dụng ban đầu = format stock
        $availableStock = $formatStock;

        // CẤIOX 2: Kiểm tra tồn kho biến thể (book_attribute_values.stock) - chỉ khi có thuộc tính
        if (!empty($attributeValueIds)) {
            Log::info('LEVEL 2 - Variant Stock Check:', [
                'selected_attribute_value_ids' => $attributeValueIds
            ]);

            // Lấy thông tin tồn kho các biến thể được chọn
            $variantStockInfo = DB::table('book_attribute_values')
                ->whereIn('attribute_value_id', $attributeValueIds)
                ->where('book_id', $bookId)
                ->select('attribute_value_id', 'stock', 'sku')
                ->get();

            if ($variantStockInfo->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin tồn kho cho biến thể đã chọn',
                    'available_stock' => 0,
                    'stock_level' => 'variant'
                ];
            }

            // Tìm các biến thể hết hàng
            $outOfStockVariants = $variantStockInfo->filter(function ($variant) {
                return $variant->stock <= 0;
            });

            if ($outOfStockVariants->isNotEmpty()) {
                $outOfStockSkus = $outOfStockVariants->pluck('sku')->filter()->implode(', ');
                return [
                    'success' => false,
                    'message' => 'Biến thể đã hết hàng: ' . ($outOfStockSkus ?: 'N/A'),
                    'available_stock' => 0,
                    'stock_level' => 'variant'
                ];
            }

            // Tìm stock thấp nhất trong các biến thể được chọn
            $minVariantStock = $variantStockInfo->min('stock');
            
            Log::info('Variant stock analysis:', [
                'variant_stocks' => $variantStockInfo->pluck('stock', 'sku')->toArray(),
                'min_variant_stock' => $minVariantStock
            ]);

            // Áp dụng logic phân cấp: Math.min(format_stock, min_variant_stock)
            $availableStock = min($formatStock, $minVariantStock);
            
            Log::info('Hierarchical stock calculation:', [
                'format_stock' => $formatStock,
                'min_variant_stock' => $minVariantStock,
                'final_available_stock' => $availableStock
            ]);

            // Kiểm tra số lượng yêu cầu có vượt quá tồn kho biến thể không
            if ($requestedQuantity > $minVariantStock) {
                $lowStockVariant = $variantStockInfo->where('stock', $minVariantStock)->first();
                return [
                    'success' => false,
                    'message' => "Số lượng yêu cầu vượt quá tồn kho biến thể. Tồn kho hiện tại: {$minVariantStock}" .
                        ($lowStockVariant->sku ? " (SKU: {$lowStockVariant->sku})" : ""),
                    'available_stock' => $minVariantStock,
                    'stock_level' => 'variant',
                    'variant_sku' => $lowStockVariant->sku ?? null
                ];
            }
        } else {
            Log::info('LEVEL 2 - No variants selected, using format stock only');
        }

        // Validation cuối cùng với stock khả dụng tính toán được
        if ($requestedQuantity > $availableStock) {
            return [
                'success' => false,
                'message' => "Số lượng yêu cầu vượt quá tồn kho khả dụng. Tồn kho hiện tại: {$availableStock}",
                'available_stock' => $availableStock,
                'stock_level' => !empty($attributeValueIds) ? 'hierarchical' : 'format'
            ];
        }

        Log::info('=== HIERARCHICAL STOCK VALIDATION SUCCESS ===', [
            'final_available_stock' => $availableStock,
            'requested_quantity' => $requestedQuantity,
            'validation_passed' => true
        ]);

        return [
            'success' => true,
            'available_stock' => $availableStock,
            'stock_level' => !empty($attributeValueIds) ? 'hierarchical' : 'format'
        ];
    }

    /**
     * Validate stock for new variant system (book_variants)
     */
    private function validateVariantStock($variantId, $requestedQuantity, $bookInfo)
    {
        Log::info('=== NEW VARIANT STOCK VALIDATION START ===', [
            'variant_id' => $variantId,
            'requested_quantity' => $requestedQuantity
        ]);

        // Lấy thông tin variant
        $variant = DB::table('book_variants')
            ->where('id', $variantId)
            ->first();

        if (!$variant) {
            return [
                'success' => false,
                'message' => 'Biến thể không tồn tại',
                'available_stock' => 0,
                'stock_level' => 'variant'
            ];
        }

        // Lấy format stock
        $formatStock = $bookInfo->stock ?? 0;

        Log::info('Variant stock info:', [
            'variant_stock' => $variant->stock,
            'format_stock' => $formatStock,
            'variant_sku' => $variant->sku
        ]);

        // Áp dụng logic phân cấp: Math.min(format_stock, variant_stock)
        $availableStock = min($formatStock, $variant->stock);

        // Kiểm tra tồn kho variant
        if ($variant->stock <= 0) {
            return [
                'success' => false,
                'message' => "Biến thể đã hết hàng" . ($variant->sku ? " (SKU: {$variant->sku})" : ""),
                'available_stock' => 0,
                'stock_level' => 'variant',
                'variant_sku' => $variant->sku
            ];
        }

        // Kiểm tra số lượng yêu cầu
        if ($requestedQuantity > $availableStock) {
            return [
                'success' => false,
                'message' => "Số lượng yêu cầu vượt quá tồn kho khả dụng. Tồn kho hiện tại: {$availableStock}" .
                    ($variant->sku ? " (SKU: {$variant->sku})" : ""),
                'available_stock' => $availableStock,
                'stock_level' => 'hierarchical',
                'variant_sku' => $variant->sku
            ];
        }

        Log::info('=== NEW VARIANT STOCK VALIDATION SUCCESS ===', [
            'available_stock' => $availableStock,
            'requested_quantity' => $requestedQuantity,
            'validation_passed' => true
        ]);

        return [
            'success' => true,
            'available_stock' => $availableStock,
            'stock_level' => 'hierarchical',
            'variant_sku' => $variant->sku
        ];
    }
}
