<?php

namespace App\Http\Controllers\Wishlists;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class WishlistController extends Controller
{
  public function add(Request $request)
  {
    try {
      if (!Auth::check()) {
        return response()->json([
          'success' => false,
          'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
        ], 401);
      }

      $user = Auth::user();
      $bookId = $request->input('book_id');
      if (!$bookId) {
        return response()->json([
          'success' => false,
          'message' => 'Thiếu ID sản phẩm'
        ]);
      }

      // Kiểm tra xem đã có sản phẩm trong wishlist chưa
      $exists = DB::table('wishlists')
        ->where('user_id', $user->id)
        ->where('book_id', $bookId)
        ->exists();

      if ($exists) {
        return response()->json([
          'success' => false,
          'message' => 'Sản phẩm đã có trong danh sách yêu thích'
        ]);
      }

      // Sinh UUID cho trường id
      $uuid = (string) Str::uuid();

      DB::table('wishlists')->insert([
        'id' => $uuid,
        'user_id' => $user->id,
        'book_id' => $bookId,
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      // Get updated wishlist count
      $wishlistCount = DB::table('wishlists')->where('user_id', $user->id)->count();

      return response()->json([
        'success' => true,
        'wishlist_count' => (int) $wishlistCount
      ]);
    } catch (\Exception $e) {
      Log::error('Lỗi khi thêm wishlist: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
      ], 500);
    }
  }

  public function getWishlist(Request $request)
  {
    try {
      if (!Auth::check()) {
        Toastr::error('Bạn cần đăng nhập để xem danh sách yêu thích.', 'Lỗi');
        return redirect()->route('account.login');
      }

      $user = Auth::user();

      // Trước tiên, xóa các wishlist items có sách đã bị xóa
      $deletedWishlistItems = DB::table('wishlists')
        ->leftJoin('books', 'wishlists.book_id', '=', 'books.id')
        ->where('wishlists.user_id', $user->id)
        ->where(function($query) {
          $query->whereNull('books.id') // Sách không tồn tại
                ->orWhereNotNull('books.deleted_at'); // Hoặc sách bị soft delete
        })
        ->delete();

      if ($deletedWishlistItems > 0) {
        Log::info("Đã tự động xóa {$deletedWishlistItems} wishlist items có sách bị xóa cho user {$user->id}");
      }

      $wishlist = DB::table('wishlists')
        ->join('books', 'wishlists.book_id', '=', 'books.id')
        ->leftJoin('brands', 'books.brand_id', '=', 'brands.id')
        ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
        ->leftJoin('author_books', 'books.id', '=', 'author_books.book_id')
        ->leftJoin('authors', 'author_books.author_id', '=', 'authors.id')
        ->where('wishlists.user_id', $user->id)
        ->whereNotNull('books.id') // Chỉ lấy sách còn tồn tại
        ->whereNull('books.deleted_at') // Và chưa bị soft delete
        ->select(
          'books.id as book_id',
          'books.slug',
          'books.cover_image',
          'books.title',
          DB::raw('GROUP_CONCAT(DISTINCT authors.name SEPARATOR ", ") as author_name'),
          'brands.name as brand_name',
          'categories.name as category_name',
          'categories.id as category_id',
          'wishlists.created_at'
        )
        ->groupBy('books.id', 'books.slug', 'books.cover_image', 'books.title', 'brands.name', 'categories.name', 'categories.id', 'wishlists.created_at')
        ->orderBy('wishlists.created_at', 'desc')
        ->paginate(10);

      // Lấy thống kê cơ bản
      $statistics = [
        'total' => $wishlist->total(),
        'recently_added' => $wishlist->take(5)
      ];

      return view('Wishlists.Wishlist', [
        'wishlist' => $wishlist,
        'statistics' => $statistics
      ]);
    } catch (\Exception $e) {
      Log::error('Lỗi khi lấy danh sách yêu thích: ' . $e->getMessage());
      return view('Wishlists.Wishlist', [
        'wishlist' => collect(),
        'statistics' => [
          'total' => 0,
          'in_stock' => 0,
          'out_of_stock' => 0,
          'total_value' => 0
        ]
      ]);
    }
  }

  /**
   * Get wishlist item count for AJAX requests
   */
  public function getWishlistCount()
  {
    try {
      if (!Auth::check()) {
        return response()->json(['count' => 0]);
      }

      $user = Auth::user();
      $count = DB::table('wishlists')
        ->where('user_id', $user->id)
        ->count();

      return response()->json(['count' => (int) $count]);
    } catch (\Exception $e) {
      Log::error('Lỗi khi lấy số lượng wishlist: ' . $e->getMessage());
      return response()->json(['count' => 0]);
    }
  }


  public function delete(Request $request)
  {
    try {
      if (!Auth::check()) {
        return response()->json([
          'success' => false,
          'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
        ], 401);
      }

      $user = Auth::user();
      $bookId = $request->input('book_id');
      
      if (!$bookId) {
        return response()->json([
          'success' => false,
          'message' => 'Thiếu ID sản phẩm'
        ]);
      }

      $deleted = DB::table('wishlists')
        ->where('user_id', $user->id)
        ->where('book_id', $bookId)
        ->delete();

      if ($deleted) {
        // Get updated wishlist count
        $wishlistCount = DB::table('wishlists')->where('user_id', $user->id)->count();
        
        return response()->json([
          'success' => true,
          'wishlist_count' => (int) $wishlistCount
        ]);
      } else {
        return response()->json([
          'success' => false,
          'message' => 'Không tìm thấy sản phẩm trong danh sách yêu thích'
        ]);
      }
    } catch (\Exception $e) {
      Log::error('Lỗi khi xóa sản phẩm khỏi wishlist: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
      ], 500);
    }
  }

  public function deleteAll(Request $request)
  {
    try {
      if (!Auth::check()) {
        return response()->json([
          'success' => false,
          'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
        ], 401);
      }

      $user = Auth::user();
      $deleted = DB::table('wishlists')->where('user_id', $user->id)->delete();

      Log::info("Xóa tất cả wishlist user {$user->id}, số bản ghi bị xóa: $deleted");
      if ($deleted === 0) {
        return response()->json([
          'success' => false,
          'message' => 'Không có sản phẩm nào trong danh sách yêu thích'
        ]);
      }
      // Xóa tất cả wishlist của người dùng

      return response()->json([
        'success' => $deleted > 0,
        'message' => $deleted > 0 ? 'Đã xóa tất cả' : 'Không có sách nào để xóa',
        'wishlist_count' => 0 // After deleting all, count is always 0
      ]);
    } catch (\Exception $e) {
      Log::error('Lỗi khi xóa tất cả wishlist: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Check for deleted books in wishlist and return them
   */
  public function checkDeletedBooks(Request $request)
  {
    try {
      if (!Auth::check()) {
        return response()->json([
          'success' => false,
          'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
        ], 401);
      }

      $user = Auth::user();
      
      // Lấy danh sách wishlist items có sách đã bị xóa (soft deleted)
      $deletedBooks = DB::table('wishlists')
        ->leftJoin('books', 'wishlists.book_id', '=', 'books.id')
        ->where('wishlists.user_id', $user->id)
        ->where(function($query) {
          $query->whereNull('books.id') // Sách không tồn tại
                ->orWhereNotNull('books.deleted_at'); // Hoặc sách bị soft delete
        })
        ->select('wishlists.book_id')
        ->get();

      if ($deletedBooks->isNotEmpty()) {
        // Xóa các wishlist items có sách đã bị xóa
        $deletedBookIds = $deletedBooks->pluck('book_id')->toArray();
        DB::table('wishlists')
          ->where('user_id', $user->id)
          ->whereIn('book_id', $deletedBookIds)
          ->delete();

        // Lấy số lượng wishlist còn lại
        $remainingCount = DB::table('wishlists')->where('user_id', $user->id)->count();

        return response()->json([
          'success' => true,
          'has_deleted_books' => true,
          'deleted_count' => count($deletedBookIds),
          'remaining_count' => $remainingCount,
          'message' => 'Đã phát hiện ' . count($deletedBookIds) . ' sách không còn tồn tại và đã xóa khỏi danh sách yêu thích.'
        ]);
      }

      return response()->json([
        'success' => true,
        'has_deleted_books' => false,
        'message' => 'Tất cả sách trong danh sách yêu thích đều còn tồn tại.'
      ]);

    } catch (\Exception $e) {
      Log::error('Lỗi khi kiểm tra sách bị xóa trong wishlist: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
      ], 500);
    }
  }

  public function addToCartFromWishlist(Request $request)
  {
    try {
      if (!Auth::check()) {
        return response()->json([
          'success' => false,
          'message' => 'Bạn cần đăng nhập để thực hiện chức năng này'
        ], 401);
      }

      $user = Auth::user();
      $bookId = $request->input('book_id');
      $bookFormatId = $request->input('book_format_id'); // có thể null
      $attributes = $request->input('attributes'); // nhận attributes dạng mảng hoặc null

      if (!$bookId) {
        return response()->json(['success' => false, 'message' => 'Thiếu book_id']);
      }

      // Kiểm tra sản phẩm có trong wishlist không
      $existsInWishlist = DB::table('wishlists')
        ->where('user_id', $user->id)
        ->where('book_id', $bookId)
        ->exists();

      if (!$existsInWishlist) {
        return response()->json(['success' => false, 'message' => 'Sản phẩm không có trong danh sách yêu thích']);
      }

      // Tạo query kiểm tra trùng trong carts
      $query = DB::table('carts')
        ->where('user_id', $user->id)
        ->where('book_id', $bookId)
        ->where('book_format_id', $bookFormatId);

      if ($attributes) {
        // So sánh JSON string hóa của attributes
        $query->where('attributes', json_encode($attributes));
      } else {
        // Nếu không có attributes, kiểm tra null hoặc rỗng
        $query->whereNull('attributes');
      }

      $existing = $query->first();

      if ($existing) {
        // Nếu đã có trong giỏ, tăng số lượng lên 1
        DB::table('carts')->where('id', $existing->id)->increment('quantity');
      } else {
        // Lấy giá
        $price = 0;
        if ($bookFormatId) {
          $price = DB::table('book_formats')->where('id', $bookFormatId)->value('price');
        }
        if (!$price) {
          $price = DB::table('book_formats')->where('book_id', $bookId)->orderBy('price', 'asc')->value('price') ?? 0;
        }

        DB::table('carts')->insert([
          'id' => (string) Str::uuid(),
          'user_id' => $user->id,
          'book_id' => $bookId,
          'book_format_id' => $bookFormatId,
          'attributes' => $attributes ? json_encode($attributes) : null,
          'quantity' => 1,
          'price_at_addition' => $price,
          'created_at' => now(),
          'updated_at' => now(),
        ]);
      }

      return response()->json(['success' => true, 'message' => 'Đã thêm sản phẩm vào giỏ hàng']);
    } catch (\Exception $e) {
      Log::error('Lỗi khi thêm vào giỏ hàng từ wishlist: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Lỗi server: ' . $e->getMessage(),
      ], 500);
    }
  }
}
