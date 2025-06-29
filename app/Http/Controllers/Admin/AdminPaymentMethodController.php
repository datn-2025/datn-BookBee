<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\EbookPurchaseConfirmation;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AdminPaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = PaymentMethod::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $paymentMethods = $query->latest()->paginate(10);
        $trashCount = PaymentMethod::onlyTrashed()->count();

        // Giữ lại tham số tìm kiếm khi phân trang
        if ($request->has('search')) {
            $paymentMethods->appends(['search' => $search]);
        }

        return view('admin.payment-methods.index', compact('paymentMethods', 'trashCount'));
    }
    public function create()
    {
        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:payment_methods',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Tên phương thức thanh toán là bắt buộc',
            'name.string' => 'Tên phương thức thanh toán phải là chuỗi',
            'name.max' => 'Tên phương thức thanh toán không được vượt quá 100 ký tự',
            'name.unique' => 'Tên phương thức thanh toán đã tồn tại',
            'is_active.boolean' => 'Trạng thái không hợp lệ'
        ]);

        PaymentMethod::create(array_merge($validated, [
            'is_active' => $request->has('is_active')
        ]));

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Phương thức thanh toán đã được thêm thành công');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('payment_methods')->ignore($paymentMethod->id)
            ],
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);
        $validated['is_active'] = $request->has('is_active');

        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Phương thức thanh toán đã được cập nhật');
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|string'
        ]);

        $payment = Payment::findOrFail($id);
        $status = PaymentStatus::where('name', $request->payment_status)->first();

        if (!$status) {
            return redirect()->back()->with('error', 'Trạng thái không hợp lệ');
        }

        $payment->payment_status_id = $status->id;

        // Nếu trạng thái là "Đã Thanh Toán" thì cập nhật ngày thanh toán và gửi email
        if (mb_strtolower($status->name, 'UTF-8') === 'đã thanh toán') {
            $payment->paid_at = now();
            
            // Kiểm tra nếu đơn hàng có sách ebook
            $order = $payment->order;
            $hasEbook = $order->orderItems()
                ->whereHas('book.formats', function($query) {
                    $query->where('format_name', 'Ebook');
                })
                ->exists();

            if ($hasEbook) {
                // Gửi email xác nhận mua ebook
                Mail::to($order->user->email)
                    ->send(new EbookPurchaseConfirmation($order));
            }
        }

        $payment->save();

        // Cập nhật trạng thái cho đơn hàng
        if ($payment->order) {
            $payment->order->payment_status_id = $status->id;
            $payment->order->save();
        }

        Toastr::success('Cập nhật trạng thái thanh toán thành công!');

        return redirect()->back()->with('success', 'Cập nhật trạng thái thanh toán thành công');
    }
    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->payments()->exists()) {
            return back()->with('error', 'Không thể xóa phương thức thanh toán này vì đã có giao dịch liên quan');
        }

        $paymentMethod->delete();

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Phương thức thanh toán đã được xóa');
    }

    public function trash()
    {
        $paymentMethods = PaymentMethod::onlyTrashed()->latest()->paginate(10);
        return view('admin.payment-methods.trash', compact('paymentMethods'));
    }

    public function restore($id)
    {
        $paymentMethod = PaymentMethod::withTrashed()->findOrFail($id);
        $paymentMethod->restore();

        return redirect()->route('admin.payment-methods.trash')
            ->with('success', 'Đã khôi phục phương thức thanh toán thành công');
    }

    public function forceDelete($id)
    {
        $paymentMethod = PaymentMethod::withTrashed()->findOrFail($id);

        // Kiểm tra xem có đơn hàng nào đang sử dụng phương thức thanh toán này không
        if ($paymentMethod->payments()->exists()) {
            return back()->with('error', 'Không thể xóa vĩnh viễn vì có đơn hàng đang sử dụng phương thức thanh toán này');
        }

        $paymentMethod->forceDelete();

        return redirect()->route('admin.payment-methods.trash')
            ->with('success', 'Đã xóa vĩnh viễn phương thức thanh toán');
    }
    public function history(Request $request)
    {
        $payments = Payment::query();

        // Tìm kiếm theo order_id, payment_method_name, order_code và amount
        if ($request->filled('search')) {
            $search = $request->search;
            $payments->where(function ($q) use ($search) {
                // Tìm kiếm theo order_code

                $q->orWhereHas('order', function ($query) use ($search) {
                    $query->where('order_code', 'LIKE', "%{$search}%");
                })
                    // Tìm kiếm theo amount (kiểm tra nếu search là số)
                    ->orWhere(function ($query) use ($search) {
                        if (is_numeric(str_replace([',', '.'], '', $search))) {
                            $searchAmount = (float) str_replace(',', '', $search);
                            $query->where('amount', '=', $searchAmount)
                                ->orWhere('amount', 'LIKE', $searchAmount . '%');
                        }
                    })
                    // Tìm kiếm theo tên phương thức thanh toán
                    ->orWhereHas('paymentMethod', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }
        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_status')) {
            $payments->whereHas('paymentStatus', function ($query) use ($request) {
                $query->where('name', $request->payment_status);
            });
        }
        // Ẩn các phương thức "Thanh toán khi nhận hàng"
        $payments->whereHas('paymentMethod', function ($query) {
            $query->where('name', '!=', 'Thanh toán khi nhận hàng');
        });


        $payments = $payments->latest()->paginate(10);

        return view('admin.payment-methods.history', compact('payments'));
    }
}
