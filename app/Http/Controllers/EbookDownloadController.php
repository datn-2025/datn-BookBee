<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\BookFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class EbookDownloadController extends Controller
{
    /**
     * Download ebook file với bảo mật
     */
    public function download(Request $request, $formatId)
    {
        // Kiểm tra user đã đăng nhập
        if (!Auth::check()) {
            abort(401, 'Bạn cần đăng nhập để tải ebook.');
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

        // Kiểm tra user đã mua ebook này chưa
        $hasPurchased = Order::where('user_id', $user->id)
            ->whereHas('orderItems', function ($query) use ($bookFormat) {
                $query->where(function ($q) use ($bookFormat) {
                    // Trường hợp 1: Mua trực tiếp ebook
                    $q->where('book_format_id', $bookFormat->id)
                      ->where('is_combo', false);
                })->orWhere(function ($q) use ($bookFormat) {
                    // Trường hợp 2: Mua sách vật lý có ebook kèm theo
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
            ->exists();

        if (!$hasPurchased) {
            abort(403, 'Bạn chưa mua ebook này hoặc đơn hàng chưa được thanh toán.');
        }

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
        $hasPurchased = Order::where('user_id', $user->id)
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
            ->exists();

        if (!$hasPurchased) {
            abort(403, 'Bạn chưa mua ebook này hoặc đơn hàng chưa được thanh toán.');
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