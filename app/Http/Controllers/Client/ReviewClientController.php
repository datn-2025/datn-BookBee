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
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem đánh giá');
        }

        $user = Auth::user();
        $type = $request->query('type', '1');

        // Get completed orders (assuming status 'Thành công' means completed)
        $completedStatus = OrderStatus::where('name', 'Thành công')->first();

        if (!$completedStatus) {
            return redirect()->back()->with('error', 'Không tìm thấy trạng thái đơn hàng đã hoàn thành');
        }

        // Thêm eager loading cho paymentMethod và các quan hệ cần thiết
        $query = $user->orders()
            ->with([
                'orderItems.book',
                'reviews',
                'paymentMethod',  // Thêm quan hệ paymentMethod
                'orderStatus',    // Thêm quan hệ orderStatus
                'address'        // Thêm quan hệ địa chỉ nếu cần
            ])
            ->where('order_status_id', $completedStatus->id);

        // Filter based on review status
        switch ($type) {
            case '2': // Not reviewed
                $query->whereDoesntHave('reviews');
                break;
            case '3': // Already reviewed
                $query->whereHas('reviews');
                break;
                // Default (type=1): Show all
        }

        $orders = $query->withCount(['reviews' => function ($q) {
            $q->whereNull('deleted_at');
        }])
            ->orderBy('reviews_count', 'asc') // Sắp xếp đơn hàng chưa đánh giá lên đầu
            ->latest('orders.created_at') // Sau đó sắp xếp theo thời gian tạo mới nhất
            ->paginate(10);

        return view('clients.account.purchases', [
            'orders' => $orders,
            'currentType' => $type,
        ]);
    }

    public function storeReview(Request $request)
    {
        Log::info('Review data:', $request->all());
        Log::info('User ID:', ['user_id' => Auth::id()]);

        // Validate for both book and combo reviews
        $rules = [
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Either book_id or collection_id must be present
        if ($request->has('book_id')) {
            $rules['book_id'] = 'required|exists:books,id';
        } elseif ($request->has('collection_id')) {
            $rules['collection_id'] = 'required|exists:collections,id';
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Phải có book_id hoặc collection_id'
            ], 400);
        }

        $request->validate($rules, [
            'order_id.required' => 'Đơn hàng không hợp lệ',
            'book_id.required' => 'Sách không hợp lệ',
            'collection_id.required' => 'Combo không hợp lệ',
            'rating.required' => 'Đánh giá không hợp lệ',
            'rating.min' => 'Đánh giá phải từ 1 đến 5',
            'rating.max' => 'Đánh giá phải từ 1 đến 5',
            'comment.max' => 'Nội dung đánh giá không hợp lệ',
            'images.max' => 'Chỉ được tải lên tối đa 5 hình ảnh',
            'images.*.image' => 'File phải là hình ảnh',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'images.*.max' => 'Kích thước hình ảnh không được vượt quá 2MB'
        ]);

        $user = Auth::user();

        // Check if the order belongs to the user and is completed
        $order = $user->orders()
            ->where('id', $request->order_id)
            ->whereHas('orderStatus', function ($q) {
                $q->where('name', 'Thành công');
            })
            ->firstOrFail();

        // Check if the book/combo is in the order
        if ($request->has('book_id')) {
            $order->orderItems()
                ->where('book_id', $request->book_id)
                ->firstOrFail();

            // Check if review already exists for book (bao gồm cả review đã xóa)
            $existingReview = Review::withTrashed()
                ->where('user_id', $user->id)
                ->where('book_id', $request->book_id)
                ->where('order_id', $order->id)
                ->first();
        } else {
            $order->orderItems()
                ->where('collection_id', $request->collection_id)
                ->firstOrFail();

            // Check if review already exists for combo (bao gồm cả review đã xóa)
            $existingReview = Review::withTrashed()
                ->where('user_id', $user->id)
                ->where('collection_id', $request->collection_id)
                ->where('order_id', $order->id)
                ->first();
        }

        if ($existingReview) {
            if ($existingReview->trashed()) {
                // Nếu review đã bị xóa, restore và cập nhật thông tin mới
                $existingReview->restore();

                // Handle image uploads
                $imagePaths = [];
                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $image) {
                        $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $imagePath = $image->storeAs('reviews', $imageName, 'public');
                        $imagePaths[] = $imagePath;
                    }
                }

                // Update review với thông tin mới
                $existingReview->update([
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'images' => empty($imagePaths) ? null : json_encode($imagePaths),
                    'status' => 'visible'
                ]);

                // $message = $request->has('book_id') ?
                //     'Bạn đã xóa đánh giá cho sản phẩm này và không thể đánh giá lại' :
                //     'Bạn đã xóa đánh giá cho combo này và không thể đánh giá lại';
                $message = $request->has('book_id') ?
                    'Đánh giá sản phẩm thành công!' :
                    'Đánh giá combo thành công!';

                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => $message]);
                }
                return redirect()->back()->with('success', $message);
            }

            $message = $request->has('book_id') ?
                'Bạn đã đánh giá sản phẩm này rồi' :
                'Bạn đã đánh giá combo này rồi';

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'error' => $message], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('reviews', $imageName, 'public');
                $imagePaths[] = $imagePath;
            }
        }

        // Create the review
        $reviewData = [
            'id' => (string) Str::uuid(),
            'user_id' => $user->id,
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment ?? '',
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'status' => 'approved',
        ];

        if ($request->has('book_id')) {
            $reviewData['book_id'] = $request->book_id;
        } else {
            $reviewData['collection_id'] = $request->collection_id;
        }

        Review::create($reviewData);

        $successMessage = $request->has('book_id') ?
            'Cảm ơn bạn đã đánh giá sản phẩm!' :
            'Cảm ơn bạn đã đánh giá combo!';

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $successMessage]);
        }
        return redirect()->back()->with('success', $successMessage);
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Kiểm tra thời gian (24h)
        $timeLimit = $review->created_at->addHours(24);
        if (now()->gt($timeLimit)) {
            $message = 'Chỉ có thể cập nhật đánh giá trong vòng 24 giờ';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            return redirect()->back()->with('error', $message);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'images.max' => 'Chỉ được tải lên tối đa 5 hình ảnh',
            'images.*.image' => 'File phải là hình ảnh',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif',
            'images.*.max' => 'Kích thước hình ảnh không được vượt quá 2MB'
        ]);

        // Handle image uploads for update
        $imagePaths = $review->images ?? [];
        if ($request->hasFile('images')) {
            // Delete old images if new ones are uploaded
            if (!empty($review->images)) {
                foreach ($review->images as $oldImagePath) {
                    if (file_exists(storage_path('app/public/' . $oldImagePath))) {
                        unlink(storage_path('app/public/' . $oldImagePath));
                    }
                }
            }

            // Upload new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
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
        return redirect()->back()->with('success', $successMessage);
    }

    public function destroy(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Kiểm tra quyền sở hữu
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        // Kiểm tra thời gian (7 ngày)
        $timeLimit = $review->created_at->addDays(7);
        if (now()->gt($timeLimit)) {
            $message = 'Chỉ có thể xóa đánh giá trong vòng 7 ngày';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }
            Toastr::error($message, 'Lỗi');
            return redirect()->back();
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
    }

    public function createForm(Request $request, $orderId, $bookId = null, $collectionId = null)
    {
        $user = Auth::user();

        // Get collectionId from request if not in URL (for combo route)
        if (!$collectionId && $request->route()->getName() === 'account.reviews.create.combo') {
            $collectionId = $request->route('collectionId');
        }

        // Check if the order belongs to the user and is completed
        $order = $user->orders()
            ->where('id', $orderId)
            ->whereHas('orderStatus', function ($q) {
                $q->where('name', 'Thành công');
            })
            ->with(['orderItems.book', 'orderItems.collection'])
            ->firstOrFail();

        if ($bookId) {
            // Check if the book is in the order
            $orderItem = $order->orderItems()
                ->where('book_id', $bookId)
                ->with('book')
                ->firstOrFail();

            $product = $orderItem->book;
            $productType = 'book';

            // Check if review already exists
            $existingReview = Review::where('user_id', $user->id)
                ->where('book_id', $bookId)
                ->where('order_id', $order->id)
                ->first();
        } elseif ($collectionId) {
            // Check if the combo is in the order
            $orderItem = $order->orderItems()
                ->where('collection_id', $collectionId)
                ->with('collection')
                ->firstOrFail();

            $product = $orderItem->collection;
            $productType = 'combo';

            // Check if review already exists
            $existingReview = Review::where('user_id', $user->id)
                ->where('collection_id', $collectionId)
                ->where('order_id', $order->id)
                ->first();
        } else {
            abort(400, 'Phải có book_id hoặc collection_id');
        }

        if ($existingReview) {
            return redirect()->route('account.reviews.edit', $existingReview->id)
                ->with('info', 'Bạn đã đánh giá sản phẩm này. Bạn có thể chỉnh sửa đánh giá.');
        }

        return view('clients.account.review_form', compact('order', 'product', 'orderItem', 'productType'));
    }

    public function editForm($reviewId)
    {
        $user = Auth::user();
        $review = $user->reviews()->where('id', $reviewId)->firstOrFail();

        $order = $user->orders()
            ->with(['orderItems.book', 'orderItems.collection', 'orderStatus', 'address', 'paymentMethod'])
            ->where('id', $review->order_id)
            ->firstOrFail();

        if ($review->book_id) {
            $orderItem = $order->orderItems->where('book_id', $review->book_id)->first();
            $product = $orderItem->book;
            $productType = 'book';
        } elseif ($review->collection_id) {
            $orderItem = $order->orderItems->where('collection_id', $review->collection_id)->first();
            $product = $orderItem->collection;
            $productType = 'combo';
        } else {
            abort(404, 'Không tìm thấy sản phẩm trong đánh giá');
        }

        if (!$orderItem) abort(404);

        return view('clients.account.review_edit', compact('order', 'orderItem', 'review', 'product', 'productType'));
    }
}
