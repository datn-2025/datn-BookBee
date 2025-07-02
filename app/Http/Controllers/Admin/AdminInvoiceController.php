<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use PDF;

class AdminInvoiceController extends Controller
{  
      public function index(Request $request)
    {        
        $query = Invoice::query();
        // $query->with([
        //     'order' => function($q) {
        //         $q->with(['user', 'address', 'paymentMethod']);
        //     },
        //     'items' => function($q) {
        //         $q->with(['book', 'book.author']);
        //     }
        // ]);  
        // Lọc chỉ lấy các invoice có order với trạng thái 'Thành công'
        $query->whereHas('order', function($q) {
            $q->whereHas('orderStatus', function($q) {
                $q->where('name', 'Thành công');
            });
        });
        $query->with([
            'order' => function($q) {
                $q->with(['user', 'address', 'paymentMethod']);
                //   ->where('order_status_id', 'completed'); // Thêm điều kiện lọc đơn hàng đã hoàn thành
            },
            'items' => function($q) {
                $q->with(['book', 'book.author']);
            }
        ]);      
        // Gom các điều kiện tìm kiếm liên quan đến order và user
        $query->whereHas('order', function($q) use ($request) {
            // Mã đơn hàng
            if ($request->filled('search_order_code')) {
                $q->where('order_code', 'like', '%' . $request->search_order_code . '%');
            }
            // Phương thức thanh toán
            if ($request->filled('payment_method')) {
                $q->where('payment_method_id', $request->payment_method);
            }
            // Điều kiện liên quan đến user
            $q->whereHas('user', function($userQ) use ($request) {
                if ($request->filled('customer_name')) {
                    $userQ->where('name', 'like', '%' . $request->customer_name . '%');
                }
                if ($request->filled('customer_email')) {
                    $userQ->where('email', 'like', '%' . $request->customer_email . '%');
                }
            });
        });

        // Tìm kiếm theo ngày tạo hóa đơn
        if ($request->filled(['start_date', 'end_date'])) {
            $startDate = date('Y-m-d', strtotime($request->start_date));
            $endDate = date('Y-m-d', strtotime($request->end_date));
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $paymentMethods = PaymentMethod::all();
        $invoices = $query->latest()->paginate(10)->withQueryString();
        return view('admin.invoices.index', compact('invoices', 'paymentMethods'));
    }

    public function show($id)
    {
        $invoice = Invoice::with(['order', 'order.user', 'items.book'])
            ->findOrFail($id);
        
        return view('admin.invoices.show', compact('invoice'));
    }

    public function generatePdf($id)
    {
        $invoice = Invoice::with(['order', 'order.user', 'items.book'])
            ->findOrFail($id);

        $pdf = PDF::loadView('admin.invoices.pdf', compact('invoice'));
        
        return $pdf->download('invoice-' . $invoice->order->order_code . '.pdf');
    }
}
