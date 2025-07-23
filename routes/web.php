<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\AdminInvoiceController;
use App\Http\Controllers\Admin\AdminPaymentMethodController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\NewsArticleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\Article\NewsController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Client\AddressClientController;
use App\Http\Controllers\Client\OrderClientController;
use App\Http\Controllers\Client\ProfileClientController;
use App\Http\Controllers\Client\ReviewClientController;
use App\Http\Controllers\Client\UserClientController;
use App\Http\Controllers\Client\RefundController;
use App\Http\Controllers\Admin\RefundController as AdminRefundController;
use App\Http\Controllers\Contact\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Login\ActivationController;
use App\Http\Controllers\Login\GoogleController;
use App\Http\Controllers\Wishlists\WishlistController;
use App\Livewire\BalanceChart;
use App\Livewire\RevenueReport;
use Illuminate\Support\Facades\Route;

// Route QR code
Route::get('storage/private/{filename}', function ($filename) {
    $path = storage_path('app/private/' . $filename);

    // Kiểm tra nếu tệp ảnh tồn tại
    if (file_exists($path)) {
        return response()->file($path);
    }

    // Nếu tệp không tồn tại, trả về lỗi 404
    abort(404);
})->where('filename', '.*');

// VNPay routes
Route::get('/vnpay/return', [\App\Http\Controllers\OrderController::class, 'vnpayReturn'])->name('vnpay.return');
// Route public cho books (categoryId optional)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
// Hiển thị danh sách và danh mục
Route::get('/books/{slug?}', [BookController::class, 'index'])->name('books.index');
Route::get('/book/{slug}', [HomeController::class, 'show'])->name('books.show');
Route::get('/books/{categoryId?}', [BookController::class, 'index'])->name('books.index');

// web.php
Route::get('/combos', [HomeController::class, 'combos'])->name('combos.index');
Route::get('combos/{slug}', [HomeController::class, 'showCombo'])->name('combos.show');


// Tìm kiếm sách
Route::get('/search', [BookController::class, 'search'])->name('books.search');

Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
Route::post('/contact', [ContactController::class, 'submitForm'])->name('contact.submit');

// cart
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::get('/count', [CartController::class, 'getCartCount'])->name('cart.count');
    Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/add-combo', [CartController::class, 'addComboToCart'])->name('cart.add-combo');
    Route::post('/update', [CartController::class, 'updateCart'])->name('cart.update');
    Route::post('/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::post('/add-wishlist', [CartController::class, 'addAllWishlistToCart'])->name('cart.add-wishlist');
    // Route::post('/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.apply-voucher');
    Route::post('/remove-voucher', [CartController::class, 'removeVoucher'])->name('cart.remove-voucher');
});

// danh sach yeu thich
Route::get('/wishlist', [WishlistController::class, 'getWishlist'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::post('/wishlist/delete', [WishlistController::class, 'delete'])->name('wishlist.delete');
Route::post('/wishlist/delete-all', [WishlistController::class, 'deleteAll'])->name('wishlist.delete-all');
Route::post('/wishlist/add-to-cart', [WishlistController::class, 'addToCartFromWishlist'])->name('wishlist.addToCart');

// lien he
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');

// Login và tài khoản
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// login with google
Route::controller(GoogleController::class)->group(function () {
    Route::get('auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('auth/google/callback', 'handleGoogleCallback');
});

// Quên mật khẩu
Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [LoginController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}/{email}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [LoginController::class, 'handleResetPassword'])->name('password.update');

Route::get('/register', [LoginController::class, 'register'])->name('register');
Route::post('/register', [LoginController::class, 'handleRegister'])->name('register.submit');


// Route đăng nhập chỉnh ở đây nha, sửa thì sửa vào đây, không được xóa có gì liên hệ Tuyết
Route::middleware('auth')->group(function () {
    // Đăng xuất
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('account')->name('account.')->group(function () {
        // Route::get('/', [LoginController::class, 'index'])->name('index');

        // Profile management
        Route::get('/profile', [ProfileClientController::class, 'showUser'])->name('profile');
        Route::put('/profile/update', [ProfileClientController::class, 'updateProfile'])->name('profile.update');

        // Password change
        Route::get('/password/change', [ProfileClientController::class, 'showChangePasswordForm'])->name('changePassword');
        Route::post('/password/change', [ProfileClientController::class, 'changePassword'])->name('password.update');

        // Address management
        Route::get('/addresses', [AddressClientController::class, 'index'])->name('addresses');
        Route::post('/addresses', [AddressClientController::class, 'store'])->name('addresses.store');
        Route::get('/addresses/{id}/edit', [AddressClientController::class, 'edit'])->name('addresses.edit');
        Route::put('/addresses/{id}', [AddressClientController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{id}', [AddressClientController::class, 'destroy'])->name('addresses.destroy');
        Route::post('/addresses/{id}/set-default', [AddressClientController::class, 'setDefault'])->name('addresses.setDefault');

        Route::get('/purchase', [ReviewClientController::class, 'index'])->name('purchase');

        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/create/{orderId}/{bookId}', [ReviewClientController::class, 'createForm'])->name('create');
            Route::post('/', [ReviewClientController::class, 'storeReview'])->name('store');

            Route::get('/{id}/edit', [ReviewClientController::class, 'editForm'])->name('edit');
            Route::put('/{id}', [ReviewClientController::class, 'update'])->name('update');
            Route::delete('/{id}', [ReviewClientController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderClientController::class, 'index'])->name('index');
            Route::get('/{id}', [OrderClientController::class, 'show'])->name('show');
            Route::put('/{id}', [OrderClientController::class, 'update'])->name('update');
            Route::delete('/{id}', [OrderClientController::class, 'destroy'])->name('destroy');
            
            // Refund routes
            Route::get('/{order}/refund', [RefundController::class, 'create'])->name('refund.create');
            Route::post('/{order}/refund', [RefundController::class, 'store'])->name('refund.request');
            Route::get('/{order}/refund/status', [RefundController::class, 'status'])->name('refund.status');
        });
    });
    // Đơn hàng checkout và storex
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\OrderController::class, 'index'])->name('index');
        Route::get('/checkout', [\App\Http\Controllers\OrderController::class, 'checkout'])->name('checkout');
        Route::get('/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('show');
        Route::post('/cancel', [\App\Http\Controllers\OrderController::class, 'cancel'])->name('cancel');
        Route::post('/store', [\App\Http\Controllers\OrderController::class, 'store'])->name('store');
        Route::post('/apply-voucher', [\App\Http\Controllers\OrderController::class, 'applyVoucher'])->name('apply-voucher');
    });
});

//------------------------------------------------------
// Ai fix đi nhó
Route::prefix('account')->name('account.')->group(function () {
    // Kích hoạt tài khoản
    Route::get('activate', [LoginController::class, 'activate'])->name('activate');
    Route::get('/activate/{token}', [ActivationController::class, 'activate'])->name('activate.token');
    Route::post('/resend-activation', [ActivationController::class, 'resendActivation'])->name('resend.activation');
});

// lỗi nè t cmt lại đó
// return redirect()->route('admin.orders.show', $order->id)->with('success', 'QR Code generated successfully!');

//---------------------------------------------------


// Route đăng nhập admin (chỉ cho khách)
Route::middleware('guest.admin')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});
Route::middleware(['auth:admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminProfileController::class, 'index'])->name('index');
        Route::put('/update', [\App\Http\Controllers\Admin\AdminProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password/update', [\App\Http\Controllers\Admin\AdminProfileController::class, 'updatePassword'])->name('password.update');
    });

    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/revenue-report', RevenueReport::class)->name('revenue-report');
    Route::get('/balance-chart', BalanceChart::class)->name('balance-chart');

    // Route admin/contacts
    Route::resource('contacts', \App\Http\Controllers\Admin\ContactController::class);
    Route::post('contacts/{contact}/reply', [\App\Http\Controllers\Admin\ContactController::class, 'sendReply'])->name('contacts.reply');
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [AdminBookController::class, 'index'])->name('index');
        Route::get('/create', [AdminBookController::class, 'create'])->name('create');
        Route::post('/store', [AdminBookController::class, 'store'])->name('store');
        Route::get('/show/{id}/{slug}', [AdminBookController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{slug}', [AdminBookController::class, 'edit'])->name('edit');
        Route::put('/update/{id}/{slug}', [AdminBookController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [AdminBookController::class, 'destroy'])->name('destroy');

        // Trash routes
        Route::get('/trash', [AdminBookController::class, 'trash'])->name('trash');
        Route::post('/restore/{id}', [AdminBookController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [AdminBookController::class, 'forceDelete'])->name('force-delete');
    });

    // Admin Payment Methods
    Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
        Route::get('/', [AdminPaymentMethodController::class, 'index'])->name('index');
        Route::get('/create', [AdminPaymentMethodController::class, 'create'])->name('create');
        Route::post('/', [AdminPaymentMethodController::class, 'store'])->name('store');
        Route::get('/{paymentMethod}/edit', [AdminPaymentMethodController::class, 'edit'])->name('edit');
        Route::put('/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])->name('update');
        Route::delete('/{paymentMethod}', [AdminPaymentMethodController::class, 'destroy'])->name('destroy');
        Route::get('/trash', [AdminPaymentMethodController::class, 'trash'])->name('trash');
        Route::put('/{paymentMethod}/restore', [AdminPaymentMethodController::class, 'restore'])->name('restore');
        Route::delete('/{paymentMethod}/force-delete', [AdminPaymentMethodController::class, 'forceDelete'])->name('force-delete');
        Route::get('/history', [AdminPaymentMethodController::class, 'history'])->name('history');
    });
    
    // Route hoàn tiền đơn hàng
    // Route::prefix('orders')->name('orders.')->group(function () {
    //     // Route::get('/{id}/refund', [OrderController::class, 'showRefund'])->name('refund.show');
    //     // Route::post('/{id}/refund', [OrderController::class, 'processRefund'])->name('refund.process');
    //     // Route::get('/{id}/refund/status', [OrderController::class, 'refundStatus'])->name('refund.status');
    //     // Route::put('/{id}/status', [AdminPaymentMethodController::class, 'updateStatus'])->name('updateStatus');
        
    //     // Routes cho quản lý yêu cầu hoàn tiền
    //     Route::get('/refunds', [RefundController::class, 'index'])->name('refunds.index');
    //     Route::get('/refunds/{id}', [RefundController::class, 'show'])->name('refunds.show');
    //     Route::post('/refunds/{id}/process', [RefundController::class, 'process'])->name('refunds.process');
        
    //     // Handle GET access to process route - redirect to show page
    //     // Route::get('/refunds/{id}/process', function($id) {
    //     //     return redirect()->route('admin.orders.refunds.show', $id);
    //     // });
    // });

    // Admin Refund Management - Chuyên biệt cho RefundController
    Route::prefix('refunds')->name('refunds.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\RefundController::class, 'index'])->name('index');
        Route::get('/{refund}', [\App\Http\Controllers\Admin\RefundController::class, 'show'])->name('show');
        Route::post('/{refund}/process', [\App\Http\Controllers\Admin\RefundController::class, 'process'])->name('process');
        Route::get('/statistics', [\App\Http\Controllers\Admin\RefundController::class, 'statistics'])->name('statistics');
    });

    Route::prefix('wallets')->name('wallets.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/deposit-history', [\App\Http\Controllers\Admin\WalletController::class, 'depositHistory'])->name('depositHistory');
        Route::get('/withdraw-history', [\App\Http\Controllers\Admin\WalletController::class, 'withdrawHistory'])->name('withdrawHistory');
        Route::post('/approve/{id}', [WalletController::class, 'approveTransaction'])->name('approveTransaction');
        Route::post('/reject/{id}', [WalletController::class, 'rejectTransaction'])->name('rejectTransaction');
        
    //     // Debug page
    //     Route::get('/debug', function() {
    //         return view('admin.wallets.debug');
    //     })->name('debug');
        
    //     // Debug routes for wallet refund
    //     Route::get('/debug-refund/{orderId}', function($orderId) {
    //         $result = \App\Services\WalletRefundDebugService::debugRefund($orderId);
    //         return response()->json($result);
    //     })->name('debug.refund');
        
    //     Route::post('/force-refund/{orderId}/{amount}', function($orderId, $amount) {
    //         $result = \App\Services\WalletRefundDebugService::forceCreateRefundTransaction($orderId, $amount);
    //         return response()->json(['success' => $result]);
    //     })->name('force.refund');
    });

    // Route admin/categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/store', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/edit/{slug}', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/update/{slug}', [AdminCategoryController::class, 'update'])->name('update');
        Route::get('/trash', [AdminCategoryController::class, 'trash'])->name('trash');
        Route::delete('/{slug}', [AdminCategoryController::class, 'destroy'])->name('destroy');
        Route::put('/{slug}/restore', [AdminCategoryController::class, 'restore'])->name('restore');
        Route::delete('/{slug}/force', [AdminCategoryController::class, 'forceDelete'])->name('force-delete');

        // Route admin/brand
        Route::prefix('brands')->name('brands.')->group(function () {
            Route::get('/', [CategoryController::class, 'brand'])->name('brand');
            Route::get('/create', [CategoryController::class, 'BrandCreate'])->name('create');
            Route::post('/', [CategoryController::class, 'BrandStore'])->name('store');
            Route::get('/trash', [CategoryController::class, 'BrandTrash'])->name('trash');
            Route::delete('/{author}', [CategoryController::class, 'BrandDestroy'])->name('destroy');
            Route::put('/{id}/restore', [CategoryController::class, 'BrandRestore'])->name('restore');
            Route::delete('/{id}/force', [CategoryController::class, 'BrandForceDelete'])->name('force-delete');
            Route::get('/{id}/edit', [CategoryController::class, 'BrandEdit'])->name('edit');
            Route::put('/{id}', [CategoryController::class, 'BrandUpdate'])->name('update');
        });
        // Route admin/authors
        Route::prefix('authors')->name('authors.')->group(function () {
            Route::get('/', [AuthorController::class, 'index'])->name('index');
            Route::get('/create', [AuthorController::class, 'create'])->name('create');
            Route::post('/', [AuthorController::class, 'store'])->name('store');
            Route::get('/trash', [AuthorController::class, 'trash'])->name('trash');
            Route::delete('/{author}', [AuthorController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/restore', [AuthorController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [AuthorController::class, 'forceDelete'])->name('force-delete');
            Route::get('/{id}/edit', [AuthorController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AuthorController::class, 'update'])->name('update');
        });
    });

    // routes admin/reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'index'])->name('index'); // Hiển thị danh sách
        Route::post('/{review}/response', [AdminReviewController::class, 'storeResponse'])->name('response.store'); // Lưu phản hồi
        Route::patch('/{review}/update-status', [AdminReviewController::class, 'updateStatus'])->name('update-status'); // Cập nhật trạng thái hiển thị/ẩn
        Route::get('/{review}/response', [AdminReviewController::class, 'showResponseForm'])->name('response'); // Hiển thị form phản hồi
    });

    // Route admin/users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
    });

    // Voucher routes
    Route::prefix('vouchers')->name('vouchers.')->group(function () {
        // Route để lấy danh sách đối tượng theo điều kiện
        Route::get('/get-condition-options', [VoucherController::class, 'getConditionOptions'])
            ->name('getConditionOptions');
        Route::get('/search', [VoucherController::class, 'search'])->name('search');

        // Trash routes - Đặt trước các route khác
        Route::get('/trash', [VoucherController::class, 'trash'])->name('trash');
        Route::post('/restore/{id}', [VoucherController::class, 'restore'])->name('restore');
        Route::delete('/force-delete/{id}', [VoucherController::class, 'forceDelete'])->name('force-delete');

        // Các route CRUD thông thường
        Route::get('/', [VoucherController::class, 'index'])->name('index');
        Route::get('/create', [VoucherController::class, 'create'])->name('create');
        Route::post('/', [VoucherController::class, 'store'])->name('store');
        Route::get('/{voucher}', [VoucherController::class, 'show'])->name('show');
        Route::get('/{voucher}/edit', [VoucherController::class, 'edit'])->name('edit');
        Route::put('/{voucher}', [VoucherController::class, 'update'])->name('update');
        Route::delete('/{voucher}', [VoucherController::class, 'destroy'])->name('destroy');

        Route::get('/export', [VoucherController::class, 'export'])->name('export');
    });
    // Route admin/vouchers
    Route::resource('vouchers', VoucherController::class);

    // Route admin/attributes
    Route::prefix('attributes')->name('attributes.')->group(function () {
        Route::get('/', [AttributeController::class, 'index'])->name('index');
        Route::get('/create', [AttributeController::class, 'create'])->name('create');
        Route::post('/store', [AttributeController::class, 'store'])->name('store');
        Route::get('/show/{id}', [AttributeController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [AttributeController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [AttributeController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [AttributeController::class, 'destroy'])->name('destroy');
    });

    // Route admin/contacts
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/', [AdminContactController::class, 'index'])->name('index');
        Route::get('/show/{id}', [AdminContactController::class, 'show'])->name('show');
        Route::put('/update/{id}', [AdminContactController::class, 'update'])->name('update'); // Cập nhật trạng thái
        Route::delete('/delete/{id}', [AdminContactController::class, 'destroy'])->name('destroy'); // Xóa liên hệ
        Route::post('/reply/{contact}', [AdminContactController::class, 'sendReply'])->name('reply'); // Gửi phản hồi
    });

    // Route admin/news
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/', [NewsArticleController::class, 'index'])->name('index');
        Route::get('/create', [NewsArticleController::class, 'create'])->name('create');
        Route::post('/store', [NewsArticleController::class, 'store'])->name('store');
        Route::get('/show/{article}', [NewsArticleController::class, 'show'])->name('show');
        Route::get('/edit/{article}', [NewsArticleController::class, 'edit'])->name('edit');
        Route::put('/update/{article}', [NewsArticleController::class, 'update'])->name('update');
        Route::delete('/delete/{article}', [NewsArticleController::class, 'destroy'])->name('destroy');
    });

    // Route admin/orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/show/{id}', [OrderController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [OrderController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [OrderController::class, 'update'])->name('update');
    });

    // Route admin/settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/update', [SettingController::class, 'update'])->name('update');
    });

    // PDF
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [AdminInvoiceController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminInvoiceController::class, 'show'])->name('show');
        Route::get('/{id}/pdf', [AdminInvoiceController::class, 'generatePdf'])->name('generate-pdf');
    });

    Route::resource('collections', CollectionController::class);
    Route::post('collections/{collection}/attach-books', [CollectionController::class, 'attachBooks'])->name('collections.attachBooks');
    Route::resource('collections', CollectionController::class);
    Route::delete('collections/{id}/force', [CollectionController::class, 'forceDelete'])->name('collections.forceDelete');
    Route::get('collections-trash', [CollectionController::class, 'trash'])->name('collections.trash');
    Route::post('collections/{id}/restore', [CollectionController::class, 'restore'])->name('collections.restore');
});

// Wallet user routes
Route::middleware('auth')->prefix('wallet')->name('wallet.')->group(function () {
    Route::get('/', [App\Http\Controllers\WalletController::class, 'index'])->name('index');
    Route::get('/deposit', [App\Http\Controllers\WalletController::class, 'showDepositForm'])->name('deposit.form');
    Route::post('/deposit', [App\Http\Controllers\WalletController::class, 'deposit'])->name('deposit');
    Route::get('/withdraw', [App\Http\Controllers\WalletController::class, 'showWithdrawForm'])->name('withdraw.form');
    Route::post('/withdraw', [App\Http\Controllers\WalletController::class, 'withdraw'])->name('withdraw');
    Route::get('/vnpay-return', [App\Http\Controllers\WalletController::class, 'vnpayReturn'])->name('vnpayReturn');
});

// AI Summary routes
Route::prefix('ai-summary')->name('ai-summary.')->group(function() {
    Route::post('/generate/{book}', [App\Http\Controllers\AISummaryController::class, 'generateSummary'])->name('generate');
    Route::get('/get/{book}', [App\Http\Controllers\AISummaryController::class, 'getSummary'])->name('get');
    Route::post('/regenerate/{book}', [App\Http\Controllers\AISummaryController::class, 'regenerateSummary'])->name('regenerate');
    Route::get('/status/{book}', [App\Http\Controllers\AISummaryController::class, 'checkStatus'])->name('status');
    Route::post('/chat/{book}', [App\Http\Controllers\AISummaryController::class, 'chatWithAI'])->name('chat');
});
