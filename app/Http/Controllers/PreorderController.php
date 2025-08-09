<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Preorder;
use App\Models\BookFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PreorderConfirmation;
use App\Services\GhnService;

class PreorderController extends Controller
{
    protected $ghnService;

    public function __construct(GhnService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    /**
     * Hiển thị form đặt trước sách
     */
    public function create(Book $book)
    {
        if (!$book->canPreorder()) {
            return redirect()->back()->with('error', 'Sách này không thể đặt trước.');
        }

        $formats = $book->formats()->get();
        $attributes = $book->bookAttributeValues()->with('attributeValue.attribute')->get();
        
        return view('preorders.create', compact('book', 'formats', 'attributes'));
    }

    /**
     * Lưu đơn đặt trước
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'book_format_id' => 'nullable|exists:book_formats,id',
            'quantity' => 'required|integer|min:1|max:10',
            'selected_attributes' => 'nullable|array',
            'customer_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'province_code' => 'nullable|string',
            'province_name' => 'nullable|string',
            'district_code' => 'nullable|string', 
            'district_name' => 'nullable|string',
            'ward_code' => 'nullable|string',
            'ward_name' => 'nullable|string',
            'notes' => 'nullable|string|max:1000'
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $bookFormat = $validated['book_format_id'] ? BookFormat::findOrFail($validated['book_format_id']) : null;

        if (!$book->canPreorder()) {
            return back()->with('error', 'Sách này không thể đặt trước.');
        }

        // Kiểm tra định dạng có phải ebook không
        $isEbook = $bookFormat && strtolower($bookFormat->format_name) === 'ebook';

        // Nếu là sách vật lý, bắt buộc phải có địa chỉ
        if (!$isEbook && (!$validated['address'] || !$validated['province_code'])) {
            return back()->with('error', 'Vui lòng nhập đầy đủ địa chỉ giao hàng cho sách vật lý.');
        }

        try {
            DB::beginTransaction();

            // Tính giá cơ bản
            $basePrice = $book->getPreorderPrice($bookFormat);
            
            // Tính giá thêm từ thuộc tính
             $attributeExtraPrice = 0;
             if (!empty($validated['selected_attributes']) && !$isEbook) {
                 foreach ($validated['selected_attributes'] as $attributeName => $attributeValue) {
                     // Tìm BookAttributeValue tương ứng
                     $bookAttributeValue = $book->bookAttributeValues()
                         ->whereHas('attributeValue', function($query) use ($attributeValue) {
                             $query->where('value', $attributeValue);
                         })
                         ->whereHas('attributeValue.attribute', function($query) use ($attributeName) {
                             $query->where('name', $attributeName);
                         })
                         ->first();
                     
                     if ($bookAttributeValue && $bookAttributeValue->extra_price > 0) {
                         $attributeExtraPrice += $bookAttributeValue->extra_price;
                     }
                 }
             }
            
            $unitPrice = $basePrice + $attributeExtraPrice;
            $totalAmount = $unitPrice * $validated['quantity'];
            
            // Thêm phí ship nếu có (miễn phí cho preorder)
            $shippingFee = 0; // Miễn phí ship cho đặt trước

            $preorderData = [
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'book_format_id' => $bookFormat?->id,
                'customer_name' => $validated['customer_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'selected_attributes' => $validated['selected_attributes'] ?? [],
                'status' => 'pending',
                'notes' => $validated['notes'],
                'expected_delivery_date' => $book->release_date
            ];

            // Chỉ lưu địa chỉ nếu không phải ebook
            if (!$isEbook) {
                $preorderData = array_merge($preorderData, [
                    'address' => $validated['address'],
                    'province_code' => $validated['province_code'],
                    'province_name' => $validated['province_name'],
                    'district_code' => $validated['district_code'],
                    'district_name' => $validated['district_name'],
                    'ward_code' => $validated['ward_code'],
                    'ward_name' => $validated['ward_name']
                ]);
            }

            $preorder = Preorder::create($preorderData);

            DB::commit();

            // Gửi email xác nhận
            try {
                Mail::to($preorder->email)->send(new PreorderConfirmation($preorder));
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi email preorder: ' . $e->getMessage());
            }

            return redirect()->route('preorders.show', $preorder)
                ->with('success', 'Đặt trước thành công! Chúng tôi đã gửi email xác nhận đến bạn.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Lỗi tạo preorder: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
    }

    /**
     * Hiển thị chi tiết đơn đặt trước
     */
    public function show(Preorder $preorder)
    {
        // Kiểm tra quyền truy cập
        if (Auth::id() !== $preorder->user_id) {
            abort(403);
        }

        $preorder->load(['book', 'bookFormat']);
        
        return view('preorders.show', compact('preorder'));
    }

    /**
     * Danh sách đơn đặt trước của user
     */
    public function index()
    {
        $preorders = Preorder::where('user_id', Auth::id())
            ->with(['book', 'bookFormat'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('preorders.index', compact('preorders'));
    }

    /**
     * Hủy đơn đặt trước
     */
    public function cancel(Preorder $preorder)
    {
        // Kiểm tra quyền
        if (Auth::id() !== $preorder->user_id) {
            abort(403);
        }

        if (!$preorder->canBeCancelled()) {
            return back()->with('error', 'Không thể hủy đơn hàng này.');
        }

        $preorder->markAsCancelled();

        return back()->with('success', 'Đã hủy đơn đặt trước thành công.');
    }

    /**
     * API: Lấy thông tin sách để đặt trước
     */
    public function getBookInfo(Book $book)
    {
        if (!$book->canPreorder()) {
            return response()->json(['error' => 'Sách không thể đặt trước'], 400);
        }

        $formats = $book->formats()->get();
        $attributes = $book->bookAttributeValues()->with('attributeValue.attribute')->get();

        return response()->json([
            'book' => $book,
            'formats' => $formats,
            'attributes' => $attributes
        ]);
    }
}
