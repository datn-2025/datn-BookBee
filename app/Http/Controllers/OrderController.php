<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Address;
use App\Models\Voucher;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Payment; // Thêm import Payment
use App\Models\User;
use App\Models\Wallet;
use App\Models\OrderCancellation; // Added for order cancellation
use App\Models\OrderItemAttributeValue; // Added for order item attributes
use App\Models\BookAttributeValue;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\EmailService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;
use App\Services\MixedOrderService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\QrCodeService;
use App\Services\VoucherService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    protected $orderService;
    protected $mixedOrderService;
    protected $voucherService;
    protected $paymentService;
    protected $emailService;
    protected $qrCodeService;
    protected $invoiceService;

    public function __construct(
        OrderService $orderService,
        MixedOrderService $mixedOrderService,
        VoucherService $voucherService,
        PaymentService $paymentService,
        EmailService $emailService,
        QrCodeService $qrCodeService,
        InvoiceService $invoiceService
    ) {
        $this->orderService = $orderService;
        $this->mixedOrderService = $mixedOrderService;
        $this->voucherService = $voucherService;
        $this->paymentService = $paymentService;
        $this->emailService = $emailService;
        $this->qrCodeService = $qrCodeService;
        $this->invoiceService = $invoiceService;
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        
        $wallet = Wallet::where('user_id', $user->id)->first();
        $addresses = $user->addresses;
        $vouchers = $this->voucherService->getAvailableVouchers($user);
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        
        // Lấy thông tin cửa hàng từ settings
        $storeSettings = \App\Models\Setting::first();

        // Validate giỏ hàng (kiểm tra sản phẩm được chọn, số lượng tồn kho và trạng thái)
        try {
            $cartItems = $this->orderService->validateCartItems($user);
        } catch (\Exception $e) {
            // Log detailed stock information for debugging
            try {
                $stockReport = $this->orderService->getCartStockReport($user);
                Log::warning('Checkout validation failed - Stock Report:', $stockReport);
            } catch (\Exception $reportException) {
                Log::error('Failed to generate stock report:', ['error' => $reportException->getMessage()]);
            }
            
            toastr()->error($e->getMessage());
            return redirect()->route('cart.index');
        }

        // Lấy thông tin chi tiết giỏ hàng với relationships
        $cartItems = $user->cart()->with(['book.images', 'bookFormat', 'collection.books'])->where('is_selected', 1)->get();

        // Sau khi validateCartItems, cập nhật lại trường gifts cho từng item
        foreach ($cartItems as $item) {
            // Nếu là combo thì không có quà tặng
            if (isset($item->is_combo) && $item->is_combo) {
                $item->gifts = collect();
            } else {
                // Kiểm tra ebook
                $isEbook = $item->bookFormat && stripos($item->bookFormat->format_name, 'ebook') !== false;
                if ($isEbook) {
                    $item->gifts = collect();
                } else {
                    // Lấy quà tặng cho sách vật lý
                    $item->gifts = DB::table('book_gifts')
                        ->where('book_id', $item->book_id)
                        ->where(function ($query) {
                            $query->whereNull('start_date')
                                ->orWhere('start_date', '<=', now());
                        })
                        ->where(function ($query) {
                            $query->whereNull('end_date')
                                ->orWhere('end_date', '>=', now());
                        })
                        ->where('quantity', '>', 0)
                        ->select('id', 'gift_name as name', 'gift_description as description', 'gift_image as image', 'quantity')
                        ->get()
                        ->map(function ($gift) {
                            return (object) [
                                'id' => $gift->id,
                                'name' => $gift->name ?? 'Quà tặng',
                                'description' => $gift->description ?? '',
                                'image' => $gift->image ?? null,
                                'quantity' => $gift->quantity
                            ];
                        });
                }
            }
        }

        // Kiểm tra nếu giỏ hàng có cả sách vật lý và sách ebook
        $hasPhysicalBook = false;
        $hasEbook = false;
        $mixedFormatCart = false;
        $hasOnlyEbooks = true; // Mặc định là chỉ có ebook

        foreach ($cartItems as $item) {
            // Kiểm tra combo - combo luôn là sách vật lý
            if (isset($item->is_combo) && $item->is_combo) {
                $hasPhysicalBook = true;
                $hasOnlyEbooks = false; // Có combo thì không phải chỉ ebook
                
                // Nếu đã có ebook, thì đây là giỏ hàng hỗn hợp
                if ($hasEbook) {
                    $mixedFormatCart = true;
                    break;
                }
            }
            
            // Kiểm tra sách đơn lẻ
            if ($item->bookFormat) {
                // Kiểm tra format_name để xác định loại sách
                if (strtolower($item->bookFormat->format_name) === 'ebook') {
                    $hasEbook = true;
                    
                    // Nếu đã có sách vật lý (bao gồm combo), thì đây là giỏ hàng hỗn hợp
                    if ($hasPhysicalBook) {
                        $mixedFormatCart = true;
                        break;
                    }
                } else {
                    $hasPhysicalBook = true;
                    $hasOnlyEbooks = false; // Có sách vật lý thì không phải chỉ ebook
                    
                    // Nếu đã có ebook, thì đây là giỏ hàng hỗn hợp
                    if ($hasEbook) {
                        $mixedFormatCart = true;
                        break;
                    }
                }
            }
        }

        // Ẩn phương thức thanh toán COD cho:
        // 1. Giỏ hàng hỗn hợp (có cả sách vật lý và ebook)
        // 2. Đơn hàng chỉ có ebook
        if ($mixedFormatCart || $hasOnlyEbooks) {
            // Lọc bỏ phương thức thanh toán khi nhận hàng (COD)
            $paymentMethods = $paymentMethods->filter(function($method) {
                return !str_contains(strtolower($method->name), 'khi nhận hàng') &&
                       !str_contains(strtolower($method->name), 'cod');
            });
        }

        // Tính tổng tiền
        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        return view('orders.checkout', compact(
            'addresses',
            'wallet',
            'vouchers',
            'paymentMethods',
            'cartItems',
            'subtotal',
            'mixedFormatCart', // Truyền biến này để hiển thị thông báo trong view
            'hasOnlyEbooks', // Truyền biến này để kiểm tra đơn hàng chỉ có ebook
            'storeSettings' // Thông tin cửa hàng
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        // dd($request->all());
        // Validate giỏ hàng (kiểm tra sản phẩm được chọn, số lượng tồn kho và trạng thái)
        try {
            $selectedCartItems = $this->orderService->validateCartItems($user);
        } catch (\Exception $e) {
            // Log detailed stock information for debugging
            try {
                $stockReport = $this->orderService->getCartStockReport($user);
                Log::warning('Order creation validation failed - Stock Report:', $stockReport);
            } catch (\Exception $reportException) {
                Log::error('Failed to generate stock report during order creation:', ['error' => $reportException->getMessage()]);
            }
            
            toastr()->error($e->getMessage());
            return redirect()->route('cart.index');
        }
        
        // Kiểm tra xem có phải đơn hàng ebook không
        $isEbookOrder = $request->delivery_method === 'ebook';
        // dd($request->delivery_method);
        $rules = [
            'voucher_code' => 'nullable|exists:vouchers,code',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'delivery_method' => 'required|in:delivery,pickup,ebook,mixed',
            'shipping_method' => 'required_if:delivery_method,delivery|in:standard,express,pickup,1,2,53320,53321',
            'shipping_fee_applied' => 'required|numeric',
            'note' => 'nullable|string|max:500',
        ];
        
        // Chỉ yêu cầu địa chỉ khi không phải đơn hàng ebook
        if (!$isEbookOrder) {
            $rules = array_merge($rules, [
                // Address rules
                'address_id' => [
                    'required_without:new_address_city_name',
                    'nullable',
                    'exists:addresses,id,user_id,' . ($user ? $user->id : 'NULL')
                ],

                // New address rules
                'new_recipient_name' => [
                    'required_without:address_id',
                    'nullable',
                    'string',
                    'max:255'
                ],
                'new_phone' => [
                    'required_without:address_id',
                    'nullable',
                    'string',
                    'max:20'
                ],
                'new_address_city_name' => [
                    'required_without:address_id',
                    'nullable',
                    'string',
                    'max:100'
                ],
                'new_address_district_name' => [
                    'required_without:address_id',
                    'nullable',
                    'string',
                    'max:100'
                ],
                'new_address_ward_name' => [
                    'required_without:address_id',
                    'nullable',
                    'string',
                    'max:100'
                ],
                'new_address_detail' => [
                    'required_without:address_id',
                    'nullable',
                    'string',
                    'max:255'
                ],
            ]);
        }
        
        // Email luôn bắt buộc
        $rules['new_email'] = [
            'required',
            'string',
            'max:50'
        ];

        $messages = [
            'voucher_code.exists' => 'Mã giảm giá không tồn tại.',
            'payment_method_id.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method_id.exists' => 'Phương thức thanh toán không hợp lệ.',
            'delivery_method.required' => 'Vui lòng chọn phương thức giao hàng.',
            'delivery_method.in' => 'Phương thức giao hàng không hợp lệ.',
            'shipping_method.required_if' => 'Vui lòng chọn phương thức vận chuyển.',
            'shipping_method.in' => 'Phương thức vận chuyển không hợp lệ.',
            'shipping_fee_applied.required' => 'Phí vận chuyển không hợp lệ.',
            'shipping_fee_applied.numeric' => 'Phí vận chuyển phải là số.',
            'note.max' => 'Ghi chú không được quá 500 ký tự.',
            
            // Address validation messages
            'address_id.required_without' => 'Vui lòng chọn địa chỉ hoặc nhập địa chỉ mới.',
            'address_id.exists' => 'Địa chỉ được chọn không hợp lệ.',
            
            // New address validation messages
            'new_recipient_name.required_without' => 'Vui lòng nhập tên người nhận.',
            'new_recipient_name.string' => 'Tên người nhận phải là chuỗi ký tự.',
            'new_recipient_name.max' => 'Tên người nhận không được quá 255 ký tự.',
            
            'new_phone.required_without' => 'Vui lòng nhập số điện thoại.',
            'new_phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'new_phone.max' => 'Số điện thoại không được quá 20 ký tự.',
            
            'new_address_city_name.required_without' => 'Vui lòng chọn tỉnh/thành phố.',
            'new_address_city_name.string' => 'Tên tỉnh/thành phố phải là chuỗi ký tự.',
            'new_address_city_name.max' => 'Tên tỉnh/thành phố không được quá 100 ký tự.',
            
            'new_address_district_name.required_without' => 'Vui lòng chọn quận/huyện.',
            'new_address_district_name.string' => 'Tên quận/huyện phải là chuỗi ký tự.',
            'new_address_district_name.max' => 'Tên quận/huyện không được quá 100 ký tự.',
            
            'new_address_ward_name.required_without' => 'Vui lòng chọn phường/xã.',
            'new_address_ward_name.string' => 'Tên phường/xã phải là chuỗi ký tự.',
            'new_address_ward_name.max' => 'Tên phường/xã không được quá 100 ký tự.',
            
            'new_address_detail.required_without' => 'Vui lòng nhập địa chỉ cụ thể.',
            'new_address_detail.string' => 'Địa chỉ cụ thể phải là chuỗi ký tự.',
            'new_address_detail.max' => 'Địa chỉ cụ thể không được quá 255 ký tự.',
            
            // Email validation messages
            'new_email.required' => 'Vui lòng nhập địa chỉ email.',
            'new_email.string' => 'Email phải là chuỗi ký tự.',
            'new_email.max' => 'Email không được quá 50 ký tự.',
        ];

        $request->validate($rules, $messages);
        $newAddressCreated = !$request->address_id;

        try {
            DB::beginTransaction();
            
            // Kiểm tra xem có phải mixed format cart không
            $cartItems = $this->orderService->validateCartItems($user);
            $isMixedFormat = $this->mixedOrderService->hasMixedFormats($cartItems);
            
            if ($isMixedFormat) {
                // Xử lý đơn hàng hỗn hợp (có cả ebook và sách vật lý)
                $mixedOrderResult = $this->mixedOrderService->createMixedFormatOrders($request, $user);
                $parentOrder = $mixedOrderResult['parent_order'];
                $physicalOrder = $mixedOrderResult['physical_order'];
                $ebookOrder = $mixedOrderResult['ebook_order'];
                $cartItems = $mixedOrderResult['cart_items'];
                
                // Lấy thông tin payment method
                $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);
                
                // Xử lý thanh toán cho đơn hàng hỗn hợp
                $isWalletPayment = $this->mixedOrderService->processMixedOrderPayment(
                    $parentOrder, $physicalOrder, $ebookOrder, $user, $paymentMethod
                );
                
                if ($isWalletPayment) {
                    // Tạo payment records cho các đơn hàng
                    $this->paymentService->createPayment([
                        'order_id' => $parentOrder->id,
                        'transaction_id' => $parentOrder->order_code . '_WALLET',
                        'payment_method_id' => $request->payment_method_id,
                        'payment_status_id' => $parentOrder->payment_status_id,
                        'amount' => $parentOrder->total_amount,
                        'paid_at' => now()
                    ]);
                    
                    DB::commit();
                    
                    // Xử lý sau khi tạo đơn hàng thành công
                    $this->mixedOrderService->handlePostOrderCreation($parentOrder, $physicalOrder, $ebookOrder, $user);
                    
                    // Tạo và gửi hóa đơn
                    try {
                        $this->invoiceService->processInvoiceForPaidOrder($parentOrder);
                        Log::info('Invoice created for mixed order', ['parent_order_id' => $parentOrder->id]);
                    } catch (\Exception $e) {
                        Log::error('Failed to create invoice for mixed order', [
                            'parent_order_id' => $parentOrder->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                    
                    $successMessage = 'Đặt hàng thành công! Đơn hàng của bạn đã được chia thành 2 phần: giao hàng sách in và nhận ebook qua email.';
                    if ($newAddressCreated) {
                        $successMessage .= ' Địa chỉ mới của bạn đã được lưu.';
                    }
                    
                    toastr()->success($successMessage);
                    return redirect()->route('orders.show', $parentOrder->id);
                }
                
                // Xử lý VNPay cho mixed order (nếu cần)
                if ($paymentMethod->name == 'Thanh toán vnpay') {
                    DB::commit();
                    
                    $vnpayData = [
                        'order_id' => $parentOrder->id,
                        'payment_status_id' => $parentOrder->payment_status_id,
                        'payment_method_id' => $parentOrder->payment_method_id,
                        'order_code' => $parentOrder->order_code,
                        'amount' => $parentOrder->total_amount,
                        'order_info' => "Thanh toán đơn hàng hỗn hợp " . $parentOrder->order_code,
                    ];
                    
                    return $this->vnpay_payment($vnpayData);
                }
                
                // Xử lý COD cho mixed order
                $this->paymentService->createPayment([
                    'order_id' => $parentOrder->id,
                    'transaction_id' => $parentOrder->order_code,
                    'payment_method_id' => $request->payment_method_id,
                    'payment_status_id' => $parentOrder->payment_status_id,
                    'amount' => $parentOrder->total_amount,
                    'paid_at' => now()
                ]);
                
                DB::commit();
                
                // Xử lý sau khi tạo đơn hàng thành công
                $this->mixedOrderService->handlePostOrderCreation($parentOrder, $physicalOrder, $ebookOrder, $user);
                
                $successMessage = 'Đặt hàng thành công! Đơn hàng của bạn đã được chia thành 2 phần: giao hàng sách in và nhận ebook qua email.';
                if ($newAddressCreated) {
                    $successMessage .= ' Địa chỉ mới của bạn đã được lưu.';
                }
                
                toastr()->success($successMessage);
                return redirect()->route('orders.show', $parentOrder->id);
            }
            
            // Xử lý đơn hàng thông thường (chỉ có một loại sản phẩm)
            // dd($request->all());
            $orderResult = $this->orderService->processOrderCreationWithWallet($request, $user);
            // dd($orderResult);
            $order = $orderResult['order'];
            $paymentMethod = $orderResult['payment_method'];
            $cartItems = $orderResult['cart_items'];
            $isWalletPayment = $orderResult['is_wallet_payment'];

            // Xử lý thanh toán bằng ví điện tử
            if ($isWalletPayment) {
                // Xử lý thanh toán ví
                $this->orderService->processWalletPayment($order, $user);
                
                // Tạo payment record cho ví
                $payment = $this->paymentService->createPayment([
                    'order_id' => $order->id,
                    'transaction_id' => $order->order_code . '_WALLET',
                    'payment_method_id' => $request->payment_method_id,
                    'payment_status_id' => $order->payment_status_id,
                    'amount' => $order->total_amount,
                    'paid_at' => now()
                ]);
                
                // Xóa giỏ hàng sau khi thanh toán thành công
            $this->orderService->clearUserCart($user);
            
            DB::commit();
            
            // Tạo đơn hàng GHN nếu là đơn hàng giao hàng
            if ($order->delivery_method === 'delivery') {
                $this->orderService->createGhnOrder($order);
            }
            
            // Tạo mã QR và gửi email xác nhận
            $this->qrCodeService->generateOrderQrCode($order);
            $this->emailService->sendOrderConfirmation($order);
            
            // Gửi email ebook nếu đơn hàng có ebook
            $this->emailService->sendEbookPurchaseConfirmation($order);
            
            // Cập nhật trạng thái đơn hàng ebook thành 'Thành công' nếu đã thanh toán
            $this->orderService->updateEbookOrderStatusOnPaymentSuccess($order);
                
                // Tạo và gửi hóa đơn ngay lập tức cho thanh toán ví
                try {
                    $this->invoiceService->processInvoiceForPaidOrder($order);
                    Log::info('Invoice created and sent for wallet payment', ['order_id' => $order->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to create invoice for wallet payment', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }
                
                $successMessage = 'Đặt hàng và thanh toán bằng ví thành công!';
                if ($newAddressCreated) {
                    $successMessage .= ' Địa chỉ mới của bạn đã được lưu.';
                }
                
                toastr()->success($successMessage);
                return redirect()->route('orders.show', $order->id);
            }
            
            // Nếu thanh toán VNPay, tạo order trước rồi chuyển hướng
            if ($paymentMethod->name == 'Thanh toán vnpay') {
                // Tạo mã QR cho đơn hàng
                $this->qrCodeService->generateOrderQrCode($order);
                
                // Commit transaction trước khi chuyển đến VNPay
                DB::commit();
                
                // Dữ liệu để truyền cho VNPay
                $vnpayData = [
                    'order_id' => $order->id,
                    'payment_status_id' => $order->payment_status_id,
                    'payment_method_id' => $order->payment_method_id,
                    'order_code' => $order->order_code,
                    'amount' => $order->total_amount,
                    'order_info' => "Thanh toán đơn hàng " . $order->order_code,
                ];
                
                return $this->vnpay_payment($vnpayData);
            }

            // Xử lý thanh toán thường (COD)
            $payment = $this->paymentService->createPayment([
                'order_id' => $order->id,
                'transaction_id' => $order->order_code,
                'payment_method_id' => $request->payment_method_id,
                'payment_status_id' => $order->payment_status_id,
                'amount' => $order->total_amount,
                'paid_at' => now()
            ]);
            
            // Xóa giỏ hàng sau khi tạo đơn hàng thành công
            $this->orderService->clearUserCart($user);
            
            DB::commit();
            
            // Tạo đơn hàng GHN nếu là đơn hàng giao hàng
            if ($order->delivery_method === 'delivery') {
                $this->orderService->createGhnOrder($order);
            }
            
            // Tạo mã QR và gửi email xác nhận
            $this->qrCodeService->generateOrderQrCode($order);
            $this->emailService->sendOrderConfirmation($order);
            
            // Lưu ý: Hóa đơn cho COD sẽ được tạo khi admin xác nhận thanh toán
            Log::info('COD order created successfully - Invoice will be created when payment is confirmed by admin', ['order_id' => $order->id]);
            
            $successMessage = 'Đặt hàng thành công!';
            if ($newAddressCreated) {
                $successMessage .= ' Địa chỉ mới của bạn đã được lưu.';
            }
            
            toastr()->success($successMessage);
            return redirect()->route('orders.show', $order->id);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            toastr()->error('Lỗi validation: ' . $e->getMessage());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo đơn hàng: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            toastr()->error($e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi đặt hàng: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này');
        }

        $order->load([
            'orderItems.book.images',
            'orderItems.bookFormat',
            'orderItems.collection',
            'orderStatus',
            'paymentStatus',
            'payments.paymentMethod',
            'address',
            'user',
            'paymentMethod',
            'voucher'
        ]);
        
        // Lấy thông tin cài đặt cửa hàng
        $storeSettings = \App\Models\Setting::first();

        return view('clients.account.order-details', compact('order', 'storeSettings'));
    }

    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()
            ->with(['orderStatus', 'paymentStatus'])
            ->orderByDesc('created_at')
            ->paginate(7);
        return view('clients.account.orders', compact('orders'));
    }

    public function applyVoucher(Request $request)
    {
        Log::debug('ApplyVoucher Request Data:', $request->all());
        $request->validate([
            'voucher_code' => 'required|exists:vouchers,code',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $voucher = Voucher::where('code', $request->voucher_code)->first();
        if (!$voucher) {
            return response()->json([
                'success' => false,
                'errors' => ['Voucher không tồn tại']
            ]);
        }

        $discountResult = $this->voucherService->calculateDiscount($voucher, $request->subtotal);

        if (isset($discountResult['errors'])) {
            return response()->json([
                'success' => false,
                'errors' => $discountResult['errors']
            ]);
        }

        return response()->json([
            'success' => true,
            'voucher_code' => $request->voucher_code,
            'voucher_description' => $voucher->description,
            'discount_amount' => $discountResult['discount']
        ]);
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|array|min:1',
            'reason.*' => 'string|max:255',
            'other_reason' => 'nullable|string|max:255',
        ]);

        $order = Order::findOrFail($request->order_id);
        $user = Auth::user();

        // Authorization: Ensure the user owns the order
        if ($order->user_id !== $user->id) {
            toastr()->error('Bạn không có quyền hủy đơn hàng này.');
            return redirect()->back()->with('error', 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Check if order status allows cancellation
        if (!\App\Helpers\OrderStatusHelper::canBeCancelled($order->orderStatus->name)) {
            toastr()->error('Không thể hủy đơn hàng ở trạng thái hiện tại: ' . $order->orderStatus->name);
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng ở trạng thái hiện tại: ' . $order->orderStatus->name);
        }

        DB::beginTransaction();
        try {
            // Ghép "Lý do khác" nếu có
            $selectedReasons = $request->input('reason', []);
            if (!empty($request->input('other_reason'))) {
                $selectedReasons[] = "Lý do khác: " . $request->input('other_reason');
            }
            
            // Create OrderCancellation record
            OrderCancellation::create([
                'order_id' => $order->id,
                'reason' => implode(", ", $selectedReasons),
                'cancelled_by' => $user->id,
                'cancelled_at' => now(),
            ]);

            // Update Order status to 'Cancelled'
            $cancelledStatus = OrderStatus::where('name', 'Đã hủy')->first();
            if (!$cancelledStatus) {
                Log::error('Order status "Đã hủy" not found.');
                toastr()->error('Lỗi hệ thống: Trạng thái hủy đơn hàng không tồn tại.');
                DB::rollBack();
                return redirect()->back()->with('error', 'Lỗi hệ thống khi hủy đơn hàng.');
            }
            
            // Cập nhật đơn hàng với thông tin hủy
            $order->update([
                'order_status_id' => $cancelledStatus->id,
                'cancelled_at' => now(),
                'cancellation_reason' => implode(", ", $selectedReasons)
            ]);

            // Cộng lại tồn kho cho các sản phẩm trong đơn hàng
            $order->orderItems->each(function ($item) {
                if ($item->bookFormat && $item->bookFormat->stock !== null) {
                    Log::info("Cộng lại tồn kho cho book_format_id {$item->bookFormat->id}, số lượng: {$item->quantity}");
                    $item->bookFormat->increment('stock', $item->quantity);
                }
                
                // ✨ THÊM MỚI: Cộng lại stock thuộc tính sản phẩm
                $this->increaseAttributeStock($item);
            });




            // Hoàn tiền vào ví nếu đơn hàng đã thanh toán
            if ($order->paymentStatus->name === 'Đã Thanh Toán') {
                try {
                    $paymentRefundService = app(\App\Services\PaymentRefundService::class);
                    $refundResult = $paymentRefundService->refundToWallet($order, $order->total_amount);
                    
                    if ($refundResult) {
                        Log::info('Order 3 cancellation refund successful', [
                            'order_id' => $order->id,
                            'order_code' => $order->order_code,
                            'amount' => $order->total_amount,
                            'user_id' => $order->user_id
                        ]);
                        
                        toastr()->success('Đơn hàng đã được hủy và hoàn tiền vào ví thành công.');
                    } else {
                        Log::warning('Order cancellation refund failed but order still cancelled', [
                            'order_id' => $order->id,
                            'order_code' => $order->order_code
                        ]);
                        
                        toastr()->success('Đơn hàng đã được hủy thành công. Tiền hoàn sẽ được xử lý trong thời gian sớm nhất.');
                    }
                } catch (\Exception $refundError) {
                    Log::error('Order cancellation refund error', [
                        'order_id' => $order->id,
                        'error' => $refundError->getMessage(),
                        'trace' => $refundError->getTraceAsString()
                    ]);
                    
                    toastr()->success('Đơn hàng đã được hủy thành công. Tiền hoàn sẽ được xử lý trong thời gian sớm nhất.');
                }
            } else {
                toastr()->success('Đơn hàng đã được hủy thành công.');
            }



            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được hủy thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi hủy đơn hàng ' . $order->id . ': ' . $e->getMessage());
            toastr()->error('Có lỗi xảy ra khi hủy đơn hàng: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi hủy đơn hàng.');
        }
    }
    
    public function vnpay_payment($data)
    {
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url = config('services.vnpay.url');
        $vnp_Returnurl = route("vnpay.return");
        $vnp_TxnRef = $data['order_code'];
        $vnp_OrderInfo = $data['order_info'];
        $vnp_Amount = (int)($data['amount'] * 100);
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_OrderType" => "other",
        );

        ksort($inputData);

        $query = http_build_query($inputData);
        $hashdata = $query;

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url = $vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;

        // Tạo payment record với trạng thái "Chờ Xử Lý"
        $this->paymentService->createPayment([
            'order_id' => $data['order_id'],
            'payment_method_id' => $data['payment_method_id'],
            'payment_status_id' => $data['payment_status_id'],
            'transaction_id' => $data['order_code'],
            'amount' => $data['amount'],
            'paid_at' => now()
        ]);

        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_SecureHash = $request->vnp_SecureHash;

        // Lấy tất cả tham số trừ vnp_SecureHash
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if ($key !== 'vnp_SecureHash') {
                $inputData[$key] = $value;
            }
        }

        // Sắp xếp theo key
        ksort($inputData);

        // Tạo hash string
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Kiểm tra tính hợp lệ của chữ ký
        if ($secureHash !== $vnp_SecureHash) {
            Log::error('VNPay signature verification failed', [
                'expected' => $secureHash,
                'received' => $vnp_SecureHash
            ]);
            return redirect()->route('orders.checkout')->with('error', 'Có lỗi xảy ra trong quá trình thanh toán');
        }

        // Lấy thông tin từ VNPay response
        $vnp_ResponseCode = $request->vnp_ResponseCode;
        $vnp_TxnRef = $request->vnp_TxnRef;
        $vnp_Amount = $request->vnp_Amount / 100;
        $vnp_TransactionNo = $request->vnp_TransactionNo;

        try {
            DB::beginTransaction();

            // Tìm đơn hàng theo order_code
            $order = Order::where('order_code', $vnp_TxnRef)->first();

            if (!$order) {
                DB::rollBack();
                Log::error('Order not found for VNPay return', ['order_code' => $vnp_TxnRef]);
                return redirect()->route('orders.checkout')->with('error', 'Không tìm thấy đơn hàng');
            }

            // Tìm payment record
            $payment = Payment::where('order_id', $order->id)
                              ->where('transaction_id', $vnp_TxnRef)
                              ->first();

            if ($vnp_ResponseCode === '00') {
                // Thanh toán thành công
                $paymentStatus = PaymentStatus::where('name', 'Đã Thanh Toán')->first();
                
                if (!$paymentStatus) {
                    Log::error('Payment status "Đã Thanh Toán" not found');
                    throw new \Exception('Trạng thái thanh toán không tồn tại');
                }

                if ($payment) {
                    $payment->update([
                        'payment_status_id' => $paymentStatus->id,
                        'paid_at' => now(),
                        'transaction_id' => $vnp_TransactionNo
                    ]);
                    
                    Log::info('Payment updated successfully', [
                        'payment_id' => $payment->id,
                        'transaction_id' => $vnp_TransactionNo
                    ]);
                }

                // Cập nhật trạng thái thanh toán của đơn hàng
                $order->update([
                    'payment_status_id' => $paymentStatus->id
                ]);
                
                Log::info('Order payment status updated to "Đã Thanh Toán"', [
                    'order_id' => $order->id,
                    'order_code' => $order->order_code
                ]);

                // Xóa giỏ hàng sau khi thanh toán thành công
                Auth::user()->cart()->delete();

                // Kiểm tra xem có phải mixed order không
                if ($order->delivery_method === 'mixed' && $order->isParentOrder()) {
                    // Xử lý mixed order - cập nhật trạng thái cho các đơn con
                    $physicalOrder = $order->childOrders()->where('delivery_method', 'delivery')->first();
                    $ebookOrder = $order->childOrders()->where('delivery_method', 'ebook')->first();
                    
                    if ($physicalOrder) {
                        $physicalOrder->update(['payment_status_id' => $paymentStatus->id]);
                    }
                    if ($ebookOrder) {
                        $ebookOrder->update(['payment_status_id' => $paymentStatus->id]);
                    }
                    
                    // Xử lý sau thanh toán cho mixed order
                    $this->mixedOrderService->handlePostOrderCreation($order, $physicalOrder, $ebookOrder, Auth::user());
                } else {
                    // Xử lý đơn hàng thông thường
                    // Tạo đơn hàng GHN nếu là đơn hàng giao hàng
                    if ($order->delivery_method === 'delivery') {
                        $this->orderService->createGhnOrder($order);
                    }

                    // Gửi email xác nhận
                    $this->emailService->sendOrderConfirmation($order);
                    
                    // Gửi email ebook nếu đơn hàng có ebook
                    $this->emailService->sendEbookPurchaseConfirmation($order);
                    
                    // Cập nhật trạng thái đơn hàng ebook thành 'Thành công' nếu đã thanh toán
                    $this->orderService->updateEbookOrderStatusOnPaymentSuccess($order);
                }
                
                // Tạo và gửi hóa đơn cho thanh toán VNPay thành công
                try {
                    $this->invoiceService->processInvoiceForPaidOrder($order);
                    Log::info('Invoice created and sent for VNPay order', ['order_id' => $order->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to create invoice for VNPay order', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Tạo QR code nếu chưa có
                if (!$order->qr_code) {
                    $this->generateQrCode($order);
                }

                DB::commit();

                toastr()->success('Thanh toán thành công! Đơn hàng của bạn đã được xác nhận.');
                return redirect()->route('orders.show', $order->id);

            } else {
                // Thanh toán thất bại - Hủy đơn hàng
                $cancelledStatus = OrderStatus::where('name', 'Đã hủy')->first();
                $failedPaymentStatus = PaymentStatus::where('name', 'Thất Bại')->first();

                if ($payment) {
                    $payment->update([
                        'payment_status_id' => $failedPaymentStatus->id
                    ]);
                }

                // Kiểm tra xem có phải mixed order không
                if ($order->delivery_method === 'mixed' && $order->isParentOrder()) {
                    // Hủy đơn hàng cha và các đơn con
                    $childOrders = $order->childOrders;
                    
                    foreach ($childOrders as $childOrder) {
                        $childOrder->update([
                            'order_status_id' => $cancelledStatus->id,
                            'payment_status_id' => $failedPaymentStatus->id,
                            'cancelled_at' => now(),
                            'cancellation_reason' => 'Thanh toán VNPay thất bại - Mã lỗi: ' . $vnp_ResponseCode
                        ]);
                        
                        // Tạo bản ghi hủy đơn hàng con
                        OrderCancellation::create([
                            'order_id' => $childOrder->id,
                            'reason' => 'Thanh toán VNPay thất bại - Mã lỗi: ' . $vnp_ResponseCode,
                            'cancelled_by' => $order->user_id,
                            'cancelled_at' => now(),
                        ]);
                    }
                }
                
                // Cập nhật trạng thái đơn hàng thành "Đã hủy"
                $order->update([
                    'order_status_id' => $cancelledStatus->id,
                    'payment_status_id' => $failedPaymentStatus->id,
                    'cancelled_at' => now(),
                    'cancellation_reason' => 'Thanh toán VNPay thất bại - Mã lỗi: ' . $vnp_ResponseCode
                ]);

                // Tạo bản ghi hủy đơn hàng
                OrderCancellation::create([
                    'order_id' => $order->id,
                    'reason' => 'Thanh toán VNPay thất bại - Mã lỗi: ' . $vnp_ResponseCode,
                    'cancelled_by' => $order->user_id,
                    'cancelled_at' => now(),
                ]);

                DB::commit();

                toastr()->error('Thanh toán thất bại! Đơn hàng đã được hủy tự động.');
                return redirect()->route('orders.checkout')->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing VNPay return', [
                'error' => $e->getMessage(),
                'order_code' => $vnp_TxnRef
            ]);

            toastr()->error('Có lỗi xảy ra trong quá trình xử lý thanh toán.');
            return redirect()->route('orders.checkout')->with('error', 'Có lỗi xảy ra trong quá trình xử lý thanh toán.');
        }
    }

    /**
     * Store a preorder
     */
    public function storePreorder(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'book_id' => 'required|exists:books,id',
                'book_format_id' => 'required|exists:book_formats,id',
                'quantity' => 'required|integer|min:1|max:5',
                'customer_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'province_code' => 'required|string',
                'district_code' => 'required|string',
                'ward_code' => 'required|string',
                'address' => 'required|string|max:500',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'selected_attributes' => 'nullable|array',
            ]);

            // For now, just return success since this is a preorder (not immediate order)
            // You can implement actual preorder logic here (save to preorders table, send email, etc.)
            
            return response()->json([
                'success' => true,
                'message' => 'Đặt trước sách thành công! Chúng tôi sẽ liên hệ với bạn khi sách có sẵn.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Dữ liệu không hợp lệ.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Preorder error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra khi đặt trước sách.'
            ], 500);
        }
    }

     private function generateQrCode(Order $order)
    {
        try {
            // Create QR code with order information
            $orderInfo = [
                'id' => $order->id,
                'customer' => $order->user->name ?? 'N/A',
                'total' => $order->total_amount,
                'date' => $order->created_at->format('Y-m-d H:i:s')
            ];

            $qrCode = QrCode::format('png')
                ->size(200)
                ->errorCorrection('H')
                ->generate(json_encode($orderInfo));

            $filename = 'order_qr/order_' . substr($order->id, 0, 8) . '.png';
            Storage::disk('public')->put($filename, $qrCode);

            // Update order with QR code path
            $order->update(['qr_code' => $filename]);

        } catch (\Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage());
        }
    }

    /**
     * Cộng lại stock thuộc tính sản phẩm khi hủy đơn hàng
     */
    private function increaseAttributeStock($orderItem)
    {
        // Lấy thuộc tính từ OrderItemAttributeValue
        $orderItemAttributes = $orderItem->orderItemAttributeValues;
        
        if ($orderItemAttributes && $orderItemAttributes->count() > 0) {
            foreach ($orderItemAttributes as $orderItemAttribute) {
                $bookAttributeValue = BookAttributeValue::where('book_id', $orderItem->book_id)
                    ->where('attribute_value_id', $orderItemAttribute->attribute_value_id)
                    ->first();
                
                if ($bookAttributeValue) {
                    $bookAttributeValue->increment('stock', $orderItem->quantity);
                }
            }
        }
    }

    /**
     * API endpoint to check cart stock status before checkout
     */
    public function checkStockStatus(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Người dùng chưa đăng nhập'
            ], 401);
        }
        
        // Get cart from session for this implementation
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Giỏ hàng trống'
            ], 400);
        }
        
        try {
            // Validate cart items using session cart
            $validationResult = $this->orderService->validateCartItems($cart);
            
            if ($validationResult['is_valid']) {
                // If validation passes, get detailed stock report
                $stockReport = $this->orderService->getCartStockReport($cart);
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Tất cả sản phẩm đều có đủ tồn kho',
                    'stock_report' => $stockReport
                ]);
            } else {
                // If validation fails, provide detailed error information
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tồn kho không đủ cho một số sản phẩm',
                    'stock_report' => $validationResult
                ], 422);
            }
            
        } catch (\Exception $e) {
            Log::error('Stock validation error in checkStockStatus', [
                'user_id' => $user->id,
                'cart_count' => count($cart),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi hệ thống khi kiểm tra tồn kho: ' . $e->getMessage()
            ], 500);
        }
    }
}
