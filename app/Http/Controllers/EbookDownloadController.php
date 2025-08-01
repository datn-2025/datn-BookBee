<?php

namespace App\Http\Controllers;

use App\Models\BookFormat;
use App\Models\Order;
use App\Models\EbookDownload;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EbookDownloadController extends Controller
{
    /**
     * Download ebook file với bảo mật và DRM
     */
    public function download(Request $request, $formatId)
    {
        // Kiểm tra user đã đăng nhập
        if (!Auth::check()) {
            abort(401, 'Bạn cần đăng nhập để tải ebook.');
        }

        $user = Auth::user();
        $bookFormat = BookFormat::findOrFail($formatId);
        // dd($request->order_id);
        // Kiểm tra định dạng có phải ebook không
        if ($bookFormat->format_name !== 'Ebook') {
            abort(403, 'File này không phải là ebook.');
        }

        // Kiểm tra file có tồn tại không
        if (!$bookFormat->file_url || !Storage::disk('public')->exists($bookFormat->file_url)) {
            abort(404, 'File ebook không tồn tại.');
        }

        $order = \App\Models\Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->whereHas('paymentStatus', fn($q) => $q->where('name', 'Đã Thanh Toán'))
            ->first();

        if (!$order) {
            return abort(403, 'Đơn hàng không hợp lệ hoặc chưa thanh toán.');
        }

        // Kiểm tra trạng thái hoàn tiền - Không cho phép tải ebook khi đang hoàn tiền
        if ($order->paymentStatus && in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền'])) {
            abort(403, 'Không thể tải ebook khi đơn hàng đang trong quá trình hoàn tiền hoặc đã được hoàn tiền.');
        }

        // Kiểm tra có yêu cầu hoàn tiền đang chờ xử lý không
        $hasActiveRefundRequest = \App\Models\RefundRequest::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
            
        if ($hasActiveRefundRequest) {
            toastr()->error('Không thể tải ebook khi có yêu cầu hoàn tiền đang được xử lý.');
            return redirect()->back();
        }

        // dd($order);
        if (!$order) {
            abort(403, 'Bạn chưa mua ebook này hoặc đơn hàng chưa được thanh toán.');
        }
        // dd($orderItem);
        $orderItem = $order->orderItems()
        ->where('is_combo', false)
        ->whereHas('bookFormat', fn($q) => $q->where('format_name', 'Ebook'))
        ->first();
        // dd($orderItem->bookFormat->format_name);
        if (!$orderItem) {
            abort(403, 'Không tìm thấy order item tương ứng với ebook này.');
        }

        // Kiểm tra DRM - số lần tải
        if ($bookFormat->drm_enabled && !$bookFormat->canUserDownload($user->id, $order->id)) {
            $remaining = $bookFormat->getRemainingDownloads($user->id, $order->id);
            abort(403, "Bạn đã hết lượt tải cho ebook này. Số lần tải tối đa: {$bookFormat->max_downloads}");
        }

        // Kiểm tra thời hạn tải (nếu có)
        if ($bookFormat->drm_enabled && $bookFormat->download_expiry_days > 0) {
            $purchaseDate = $order->created_at;
            $expiryDate = $purchaseDate->addDays($bookFormat->download_expiry_days);
            
            if (now() > $expiryDate) {
                abort(403, "Thời hạn tải ebook đã hết. Thời hạn: {$bookFormat->download_expiry_days} ngày kể từ ngày mua.");
            }
        }

        // Ghi lại lượt tải
        EbookDownload::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'order_item_id' => $orderItem->id,
            'book_format_id' => $bookFormat->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Log hoạt động tải
        Log::info('Ebook downloaded', [
            'user_id' => $user->id,
            'book_format_id' => $bookFormat->id,
            'order_id' => $order->id,
            'book_title' => $bookFormat->book->title,
            'remaining_downloads' => $bookFormat->getRemainingDownloads($user->id, $order->id) - 1
        ]);

        // Lấy đường dẫn file
        $filePath = Storage::disk('public')->path($bookFormat->file_url);
        $fileName = $bookFormat->book->title . '.pdf';

        // Trả về file để download
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Download sample ebook file (không cần xác thực)
     */
    public function downloadSample($formatId)
    {
        $bookFormat = BookFormat::findOrFail($formatId);

        // Kiểm tra có cho phép đọc thử không
        if (!$bookFormat->allow_sample_read) {
            abort(403, 'Sách này không cho phép đọc thử.');
        }

        // Kiểm tra file sample có tồn tại không
        if (!$bookFormat->sample_file_url || !Storage::disk('public')->exists($bookFormat->sample_file_url)) {
            abort(404, 'File đọc thử không tồn tại.');
        }

        // Lấy đường dẫn file
        $filePath = Storage::disk('public')->path($bookFormat->sample_file_url);
        $fileName = $bookFormat->book->title . '_sample.pdf';

        // Trả về file để download
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Xem ebook online (stream) với bảo mật
     */
    public function view($formatId)
    {
        // Kiểm tra user đã đăng nhập
        if (!Auth::check()) {
            abort(401, 'Bạn cần đăng nhập để xem ebook.');
        }

        $user = Auth::user();
        $bookFormat = BookFormat::findOrFail($formatId);

        // Kiểm tra định dạng có phải ebook không
        if ($bookFormat->format_name !== 'Ebook') {
            abort(403, 'File này không phải là ebook.');
        }

        // Kiểm tra file có tồn tại không
        if (!$bookFormat->file_url || !Storage::disk('public')->exists($bookFormat->file_url)) {
            abort(404, 'File ebook không tồn tại.');
        }

        // Kiểm tra user đã mua ebook này chưa (tương tự như download)
        $order = Order::where('user_id', $user->id)
            ->whereHas('orderItems', function ($query) use ($bookFormat) {
                $query->where(function ($q) use ($bookFormat) {
                    $q->where('book_format_id', $bookFormat->id)
                      ->where('is_combo', false);
                })->orWhere(function ($q) use ($bookFormat) {
                    $q->where('book_id', $bookFormat->book_id)
                      ->where('is_combo', false)
                      ->whereHas('bookFormat', function ($subQuery) {
                          $subQuery->where('format_name', '!=', 'Ebook');
                      });
                });
            })
            ->whereHas('paymentStatus', function ($query) {
                $query->where('name', 'Đã Thanh Toán');
            })
            ->first();

        if (!$order) {
            abort(403, 'Bạn chưa mua ebook này hoặc đơn hàng chưa được thanh toán.');
        }

        // Kiểm tra trạng thái hoàn tiền - Không cho phép xem ebook khi đang hoàn tiền
        if ($order->paymentStatus && in_array($order->paymentStatus->name, ['Đang Hoàn Tiền', 'Đã Hoàn Tiền'])) {
            abort(403, 'Không thể xem ebook khi đơn hàng đang trong quá trình hoàn tiền hoặc đã được hoàn tiền.');
        }

        // Kiểm tra có yêu cầu hoàn tiền đang chờ xử lý không
        $hasActiveRefundRequest = \App\Models\RefundRequest::where('order_id', $order->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();
            
        if ($hasActiveRefundRequest) {
            abort(403, 'Không thể xem ebook khi có yêu cầu hoàn tiền đang được xử lý.');
        }

        // Stream file PDF
        $filePath = Storage::disk('public')->path($bookFormat->file_url);
        
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $bookFormat->book->title . '.pdf"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Xem sample ebook online (không cần xác thực)
     */
    public function viewSample($formatId)
    {
        $bookFormat = BookFormat::findOrFail($formatId);

        // Kiểm tra có cho phép đọc thử không
        if (!$bookFormat->allow_sample_read) {
            abort(403, 'Sách này không cho phép đọc thử.');
        }

        // Kiểm tra file sample có tồn tại không
        if (!$bookFormat->sample_file_url || !Storage::disk('public')->exists($bookFormat->sample_file_url)) {
            abort(404, 'File đọc thử không tồn tại.');
        }

        // Stream file PDF
        $filePath = Storage::disk('public')->path($bookFormat->sample_file_url);
        
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $bookFormat->book->title . '_sample.pdf"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}