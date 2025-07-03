<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Preorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PreorderController extends Controller
{    public function index(Request $request)
    {
        $query = Preorder::query()->orderBy('created_at', 'desc');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->search($search);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->byStatus($request->get('status'));
        }

        // Lọc theo ngày
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $preorders = $query->paginate(15);
        $statuses = Preorder::getStatuses();

        // Thống kê
        $stats = [
            'total' => Preorder::count(),
            'pending' => Preorder::byStatus(Preorder::STATUS_PENDING)->count(),
            'confirmed' => Preorder::byStatus(Preorder::STATUS_CONFIRMED)->count(),
            'delivered' => Preorder::byStatus(Preorder::STATUS_DELIVERED)->count(),
            'cancelled' => Preorder::byStatus(Preorder::STATUS_CANCELLED)->count(),
        ];

        return view('admin.preorders.index', compact('preorders', 'statuses', 'stats'));
    }

    public function show(Preorder $preorder)
    {
        $preorder->load(['book', 'bookFormat']);
        return view('admin.preorders.show', compact('preorder'));
    }

    public function updateStatus(Request $request, Preorder $preorder)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,shipped,delivered,cancelled',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $oldStatus = $preorder->status;
            $preorder->update([
                'status' => $request->status,
                'admin_notes' => $request->admin_notes
            ]);

            Log::info('Preorder status updated', [
                'preorder_id' => $preorder->id,
                'old_status' => $oldStatus,
                'new_status' => $request->status,
                'admin_notes' => $request->admin_notes,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công!',
                'status_text' => $preorder->status_text,
                'status_color' => $preorder->status_color
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update preorder status', [
                'preorder_id' => $preorder->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái!'
            ], 500);
        }
    }

    public function destroy(Preorder $preorder)
    {
        try {
            Log::info('Preorder deleted', [
                'preorder_id' => $preorder->id,
                'customer_name' => $preorder->customer_name,
                'book_title' => $preorder->book_title,
                'deleted_by' => Auth::id()
            ]);

            $preorder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đơn đặt trước thành công!'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete preorder', [
                'preorder_id' => $preorder->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa đơn đặt trước!'
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $query = Preorder::with(['book', 'bookFormat'])
                        ->orderBy('created_at', 'desc');

        // Áp dụng cùng bộ lọc như trang index
        if ($request->filled('search')) {
            $query->search($request->get('search'));
        }

        if ($request->filled('status')) {
            $query->byStatus($request->get('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $preorders = $query->get();

        $filename = 'preorders_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($preorders) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");
            
            // Headers
            fputcsv($file, [
                'ID',
                'Ngày đặt',
                'Tên sách',
                'Định dạng',
                'Khách hàng',
                'Email',
                'Số điện thoại',
                'Địa chỉ',
                'Số lượng',
                'Đơn giá',
                'Tổng tiền sách',
                'Phí vận chuyển',
                'Tổng thanh toán',
                'Trạng thái',
                'Ghi chú admin'
            ]);

            // Data
            foreach ($preorders as $preorder) {
                fputcsv($file, [
                    $preorder->id,
                    $preorder->created_at->format('d/m/Y H:i'),
                    $preorder->book_title,
                    $preorder->book_format_name ?? 'Không xác định',
                    $preorder->customer_name,
                    $preorder->customer_email,
                    $preorder->customer_phone,
                    $preorder->customer_address,
                    $preorder->quantity,
                    $preorder->unit_price,
                    $preorder->book_total,
                    $preorder->shipping_cost,
                    $preorder->total_price,
                    $preorder->status_text,
                    $preorder->admin_notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'preorder_ids' => 'required|array',
            'preorder_ids.*' => 'exists:preorders,id',
            'status' => 'required|in:pending,confirmed,preparing,shipped,delivered,cancelled',
        ]);

        try {
            $updatedCount = Preorder::whereIn('id', $request->preorder_ids)
                ->update([
                    'status' => $request->status,
                    'updated_at' => now()
                ]);

            Log::info('Bulk status update for preorders', [
                'preorder_ids' => $request->preorder_ids,
                'status' => $request->status,
                'updated_count' => $updatedCount,
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Đã cập nhật trạng thái cho {$updatedCount} đơn đặt trước!"
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to bulk update preorder status', [
                'preorder_ids' => $request->preorder_ids,
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái!'
            ], 500);
        }
    }
}
