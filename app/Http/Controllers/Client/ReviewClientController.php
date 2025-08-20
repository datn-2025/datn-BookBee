<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\Review;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReviewClientController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            Toastr::error('Bạn cần đăng nhập để xem đánh giá', 'Lỗi');
            return redirect()->route('login');
        }

        $user = Auth::user();
        $type = $request->query('type', '1');

        // 2. Lấy ID trạng thái đơn hàng "Thành công" (completed)
        $completedStatusId = OrderStatus::where('name', 'Thành công')->value('id');

        if (!$completedStatusId) {
            Toastr::error('Không tìm thấy trạng thái đơn hàng đã hoàn thành', 'Lỗi');
            return redirect()->back();
        }

        // Build query cơ bản với eager loading
        $query = $user->orders()
            ->with([
                'orderItems.book',
                'reviews',
                'paymentMethod',
                'orderStatus',
                'address',
            ])
            ->where('order_status_id', $completedStatusId);

        // Lọc theo trạng thái review
        switch ($type) {
            case '2':
                $query->whereDoesntHave('reviews');
                break;
            case '3':
                $query->whereHas('reviews');
                break;
        }

        // Thêm số lượng review (chỉ tính chưa xóa mềm)
        $orders = $query->withCount([
            'reviews' =>  fn($q) => $q->whereNull('deleted_at')
        ])
            ->orderBy('reviews_count', 'asc') // Ưu tiên đơn chưa đánh giá
            ->latest('orders.created_at') // Tiếp theo là đơn mới nhất
            ->paginate(10);

        return view('clients.account.purchases', compact('orders', 'type'));
    }

    public function storeReview(Request $request)
    {
        $rules = [
            'order_id'  => 'required|exists:orders,id',
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:1000',
            'images'    => 'nullable|array|max:5',
            'images.*'  => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Phải có book_id hoặc collection_id
        if ($request->has('book_id')) {
            $rules['book_id'] = 'required|exists:books,id';
        } elseif ($request->has('collection_id')) {
            $rules['collection_id'] = 'required|exists:collections,id';
        } else {
            return $this->errorResponse('Phải có book_id hoặc collection_id');
        }

        $request->validate($rules, [
            'order_id.required'      => 'Đơn hàng không hợp lệ',
            'book_id.required'       => 'Sách không hợp lệ',
            'collection_id.required' => 'Combo không hợp lệ',
            'rating.required'        => 'Đánh giá không hợp lệ',
            'rating.min'             => 'Đánh giá phải từ 1 đến 5',
            'rating.max'             => 'Đánh giá phải từ 1 đến 5',
            'comment.max'            => 'Nội dung đánh giá không hợp lệ',
            'images.max'             => 'Chỉ được tải lên tối đa 5 hình ảnh',
            'images.*.image'         => 'File phải là hình ảnh',
            'images.*.mimes'         => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'images.*.max'           => 'Kích thước hình ảnh không được vượt quá 2MB'
        ]);

        $user = Auth::user();

        // Check order hợp lệ
        $order = $user->orders()
            ->where('id', $request->order_id)
            ->whereHas('orderStatus', fn($q) => $q->where('name', 'Thành công'))
            ->firstOrFail();

        // 4. Kiểm tra book/combo 
        $itemKey = $request->has('book_id') ? 'book_id' : 'collection_id';
        $itemId  = $request->$itemKey;

        $order->orderItems()->where($itemKey, $itemId)->firstOrFail();

        $existingReview = Review::withTrashed()
            ->where('user_id', $user->id)
            ->where($itemKey, $itemId)
            ->where('order_id', $order->id)
            ->first();

        // Upload ảnh (nếu có)
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('reviews', $imageName, 'public');
                $imagePaths[] = $imagePath;
            }
        }

        if ($existingReview) {
            if ($existingReview->trashed()) {
                $existingReview->restore();
                $existingReview->update([
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'images' => $imagePaths ? json_encode($imagePaths) : null,
                    'status' => 'visible'
                ]);

                $message = $request->has('book_id') ?
                    'Đánh giá sản phẩm thành công!' :
                    'Đánh giá combo thành công!';

                // Nếu request JSON thì trả về JSON
                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => $message]);
                }

                Toastr::success($message);
                return back();
            }

            $message = $request->has('book_id') ?
                'Bạn đã đánh giá sản phẩm này rồi' :
                'Bạn đã đánh giá combo này rồi';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => $message], 400);
            }
            Toastr::success($message);
            return back();
        }

        // Nếu chưa có review → tạo mới
        Review::create([
            'id'            => (string) Str::uuid(),
            'user_id'       => $user->id,
            'order_id'      => $order->id,
            $itemKey        => $itemId,
            'rating'        => $request->rating,
            'comment'       => $request->comment ?? '',
            'images'        => $imagePaths ?: null,
            'status'        => 'approved'
        ]);

        $successMessage = $request->has('book_id') ?
            'Cảm ơn bạn đã đánh giá sản phẩm!' :
            'Cảm ơn bạn đã đánh giá combo!';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $successMessage]);
        }

        Toastr::success($successMessage);
        return back();
    }

    public function editForm($reviewId)
    {
        $user = Auth::user();

        $review = $user->reviews()->findOrFail($reviewId);

        $order = $user->orders()
            ->with(['orderItems.book', 'orderItems.collection', 'orderStatus', 'address', 'paymentMethod'])
            ->findOrFail($review->order_id);

        if ($review->book_id) {
            $orderItem = $order->orderItems->where('book_id', $review->book_id)->first();
            $product = $orderItem->book;
            $productType = 'book';
        } elseif ($review->collection_id) {
            $orderItem = $order->orderItems->where('collection_id', $review->collection_id)->first();
            $product = $orderItem->collection;
            $productType = 'combo';
        } else {
            Toastr::error('Không tìm thấy sản phẩm liên quan đến đánh giá');
            return redirect()->back();
        }

        if (!$orderItem) {
            Toastr::error('Không tìm thấy sản phẩm trong đơn hàng');
            return redirect()->back();
        }

        return view('clients.account.review_edit', compact('order', 'orderItem', 'review', 'product', 'productType'));
    }

    public function update(Request $request, $id)
    {
        try {
            $review = Review::findOrFail($id);

            if ($review->user_id !== Auth::id()) {
                Toastr::error('Bạn không có quyền sửa đánh giá này', 'Lỗi');
                return redirect()->back();
            }

            if (now()->gt($review->created_at->addHours(24))) {
                Toastr::error('Chỉ có thể cập nhật đánh giá trong vòng 24 giờ');
                return redirect()->back();
            }

            // Kiểm tra trạng thái đơn hàng
            if (!$review->order || $review->order->orderStatus->name !== 'Thành công') {
                Toastr::error('Chỉ có thể cập nhật đánh giá của đơn hàng đã hoàn thành');
                return redirect()->back();
            }

            $validated = $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
                'images' => 'nullable|array|max:5',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ], [
                'rating.required'   => 'Vui lòng chọn số sao đánh giá',
                'rating.integer'    => 'Đánh giá không hợp lệ',
                'rating.min'        => 'Đánh giá phải từ 1 đến 5 sao',
                'rating.max'        => 'Đánh giá phải từ 1 đến 5 sao',
                'comment.max'       => 'Nội dung đánh giá không được vượt quá 1000 ký tự',
                'images.max'        => 'Chỉ được tải lên tối đa 5 hình ảnh',
                'images.*.image'    => 'File phải là hình ảnh',
                'images.*.mimes'    => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
                'images.*.max'      => 'Kích thước hình ảnh không được vượt quá 2MB'
            ]);

            $imagePaths = $review->images ?? [];
            if ($request->hasFile('images')) {
                // Xóa ảnh cũ
                if (!empty($review->images)) {
                    foreach ($review->images as $oldImagePath) {
                        $fullPath = storage_path('app/public/' . $oldImagePath);
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                }

                // Upload new images
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imageName = time() . '_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('reviews', $imageName, 'public');
                    $imagePaths[] = $imagePath;
                }
            }

            $review->update([
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? '',
                'images' => !empty($imagePaths) ? $imagePaths : null,
            ]);

            $successMessage = 'Cập nhật đánh giá thành công';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }

            Toastr::success($successMessage, 'Thành công');
            return back();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $e->getCode() === 403 ? 403 : 400);
            }
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $review = Review::findOrFail($id);

            // Kiểm tra quyền sở hữu
            if ($review->user_id !== Auth::id()) {
                throw new \Exception('Bạn không có quyền xóa đánh giá này', 403);
            }

            // Kiểm tra thời gian (7 ngày)
            $timeLimit = $review->created_at->addDays(7);
            if (now()->gt($timeLimit)) {
                throw new \Exception('Chỉ có thể xóa đánh giá trong vòng 7 ngày', 403);
            }

            // Kiểm tra trạng thái đơn hàng
            $order = $review->order;
            if (!$order || $order->orderStatus->name !== 'Thành công') {
                throw new \Exception('Không thể xóa đánh giá của đơn hàng chưa hoàn thành', 403);
            }

            // Xóa hình ảnh
            if (!empty($review->images)) {
                foreach ($review->images as $imagePath) {
                    $fullPath = storage_path('app/public/' . $imagePath);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

            $review->delete();

            $successMessage = 'Xóa đánh giá thành công';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }

            Toastr::success($successMessage, 'Thành công');
            return redirect()->back();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $e->getCode() === 403 ? 403 : 400);
            }

            Toastr::error($errorMessage, 'Lỗi');
            return redirect()->back();
        }
    }
}
