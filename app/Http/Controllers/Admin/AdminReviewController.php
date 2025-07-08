<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    /**
     * Hiển thị danh sách đánh giá với bộ lọc
     */
    public function index(Request $request)
    {
        $reviews = Review::with(['book', 'user'])
            ->when($request->filled('status'), fn ($q) =>
            $q->where('status', $request->string('status'))
            )

            ->when($request->filled('admin_response'), function ($q) use ($request) {
                match ($request->string('admin_response')) {
                    'responded'     => $q->whereNotNull('admin_response'),
                    'not_responded' => $q->whereNull('admin_response'),
                    default         => null
                };
            })

            ->when($request->filled('product_name'), fn ($q) =>
                $q->whereHas('book', fn ($bq) =>
                    $bq->where('title', 'like', '%' . $request->string('product_name')->trim() . '%')
                )
            )

            ->when($request->filled('customer_name'), fn ($q) =>
                $q->whereHas('user', fn ($uq) =>
                    $uq->where('name', 'like', '%' . $request->string('customer_name')->trim() . '%')
                )
            )

            ->when($request->filled('rating'), fn ($q) =>
                $q->where('rating', (int) $request->input('rating'))
            )

            ->when($request->filled('cmt'), fn ($q) =>
                $q->where(function ($query) use ($request) {
                    $cmt = '%' . $request->string('cmt')->trim() . '%';
                    $query->where('comment', 'like', $cmt)
                        ->orWhere('admin_response', 'like', $cmt);
                })
            )
            ->latest()
            ->paginate(10);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Hiển thị form phản hồi cho đánh giá
     */
    public function showResponseForm($id)
    {
        $review = Review::find($id);

        // Nếu không tồn tại, hiển thị thông báo lỗi và chuyển hướng về danh sách review
        if (!$review) {
            Toastr::error('Không tìm thấy đánh giá.', 'Lỗi');
            return redirect()->route('admin.reviews.index');
        }

        $review->load([
            'book' => function ($q) {
                $q->withCount('reviews')
                    ->withAvg('reviews', 'rating')
                    ->withSum('orderItems as sold_count', 'quantity')
                    ->with(['author', 'brand', 'category']);
            },
            'user'
        ]);

        $otherReviews = Review::where('book_id', $review->book_id)
            ->where('id', '!=', $review->id)
            ->with(['user' => fn($query) => $query->withTrashed()])
            ->latest()
            ->paginate(5);

        return view('admin.reviews.response', compact('review', 'otherReviews'));
    }

    /**
     * Cập nhật trạng thái ẩn/hiện của đánh giá
     */
    public function updateStatus(Review $review)
    {
        if (!in_array($review->status, ['visible', 'hidden'])) {
            Toastr::error('Trạng thái không hợp lệ.', 'Lỗi');
            return redirect()->route('admin.reviews.response', $review);
        }

        $newStatus = $review->status === 'visible' ? 'hidden' : 'visible';
        $review->update(['status' => $newStatus]);

        Toastr::success('Cập nhật trạng thái đánh giá thành công.', 'Thành công');
        return redirect()->route('admin.reviews.response', $review);
    }

    /**
     * Lưu phản hồi của admin (một lần duy nhất)
     */
    public function storeResponse(Request $request, $id)
    {
        $review = Review::lockForUpdate()->find($id);

        // Nếu không tồn tại, hiển thị thông báo lỗi và chuyển hướng về danh sách review
        if (!$review) {
            Toastr::error('Không tìm thấy đánh giá.', 'Lỗi');
            return redirect()->route('admin.reviews.index');
        }

        if ($review->admin_response) {
            Toastr::error('Đánh giá này đã được phản hồi.', 'Lỗi');
            return redirect()->route('admin.reviews.index');
        }

        $request->validate([
            'admin_response' => 'required|string|not_regex:/<.*?>/i|max:1000'
        ], [
            'admin_response.required' => 'Nội dung phản hồi không được để trống.',
            'admin_response.string' => 'Nội dung phản hồi phải là chuỗi văn bản.',
            'admin_response.not_regex' => 'Nội dung phản hồi không được chứa thẻ HTML.',
            'admin_response.max' => 'Nội dung phản hồi không được vượt quá 1000 ký tự.'
        ]);

        $review->update([
            'admin_response' => $request->admin_response,
        ]);

        Toastr::success('Đã gửi phản hồi thành công.', 'Thành công');
        return redirect()->route('admin.reviews.index');
    }
}
