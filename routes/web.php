<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminChatrealtimeController;
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
use App\Http\Controllers\Admin\StaffController;
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
use App\Http\Controllers\OrderChatTestController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;

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
    Route::post('/refresh-prices', [CartController::class, 'refreshCartPrices'])->name('cart.refresh-prices');
    Route::post('/add-wishlist', [CartController::class, 'addAllWishlistToCart'])->name('cart.add-wishlist');
    // Route::post('/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.apply-voucher');
    Route::post('/remove-voucher', [CartController::class, 'removeVoucher'])->name('cart.remove-voucher');
    Route::post('/update-selected', [CartController::class, 'updateSelected'])->name('cart.update-selected');
});

// danh sach yeu thich
Route::get('/wishlist', [WishlistController::class, 'getWishlist'])->name('wishlist.index');
Route::get('/wishlist/count', [WishlistController::class, 'getWishlistCount'])->name('wishlist.count');
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
Route::get('/addresses/{id}/shipping', [AddressClientController::class, 'getAddressForShipping'])->name('addresses.shipping');
Route::put('/addresses/{id}', [AddressClientController::class, 'update'])->name('addresses.update');
Route::delete('/addresses/{id}', [AddressClientController::class, 'destroy'])->name('addresses.destroy');
Route::post('/addresses/{id}/set-default', [AddressClientController::class, 'setDefault'])->name('addresses.setDefault');

        Route::get('/purchase', [ReviewClientController::class, 'index'])->name('purchase');

        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/create/{orderId}/{bookId?}', [ReviewClientController::class, 'createForm'])
                ->where(['orderId' => '[0-9]+', 'bookId' => '[0-9]+'])
                ->name('create');
            Route::get('/create-combo/{orderId}/{collectionId}', [ReviewClientController::class, 'createForm'])
                ->where(['orderId' => '[0-9]+', 'collectionId' => '[0-9]+'])
                ->name('create.combo');
            Route::post('/', [ReviewClientController::class, 'storeReview'])->name('store');

            Route::get('/{id}/edit', [ReviewClientController::class, 'editForm'])->name('edit');
            Route::put('/{id}', [ReviewClientController::class, 'update'])->name('update');
            Route::delete('/{id}', [ReviewClientController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('orders')->name('orders.')->group(function () {
            // Redirect old index route to unified
            Route::get('/', function() {
                return redirect()->route('account.orders.unified');
            })->name('index');
            Route::get('/unified', [OrderClientController::class, 'unified'])->name('unified');
            Route::get('/{id}', [OrderClientController::class, 'show'])->name('show');
            Route::put('/{id}', [OrderClientController::class, 'update'])->name('update');
            Route::put('/{id}/cancel', [OrderClientController::class, 'cancel'])->name('cancel');
            Route::delete('/{id}', [OrderClientController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/confirm-received', [OrderClientController::class, 'confirmReceived'])->name('confirm-received');

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
    
    // Preorder routes
    Route::post('/preorder', [\App\Http\Controllers\OrderController::class, 'storePreorder'])->name('preorder.store');
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
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});
Route::middleware(['auth:admin', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Profile Management
    Route::prefix('profile')->name('profile.')->middleware('checkpermission:profile.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AdminProfileController::class, 'index'])->name('index')->middleware('checkpermission:profile.view');
        Route::put('/update', [\App\Http\Controllers\Admin\AdminProfileController::class, 'updateProfile'])->name('update')->middleware('checkpermission:profile.edit');
        Route::put('/password/update', [\App\Http\Controllers\Admin\AdminProfileController::class, 'updatePassword'])->name('password.update')->middleware('checkpermission:profile.edit');
    });

    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/revenue-report', RevenueReport::class)->name('revenue-report')->middleware('checkpermission:dashboard.revenue-report');
    Route::get('/balance-chart', BalanceChart::class)->name('balance-chart')->middleware('checkpermission:dashboard.balance-chart');



    // Contacts
    Route::prefix('contacts')->name('contacts.')->middleware('checkpermission:contact.view')->group(function () {
        Route::get('/', [AdminContactController::class, 'index'])->name('index')->middleware('checkpermission:contact.view');
        Route::get('/show/{id}', [AdminContactController::class, 'show'])->name('show')->middleware('checkpermission:contact.show');
        Route::put('/update/{id}', [AdminContactController::class, 'update'])->name('update')->middleware('checkpermission:contact.edit');
        Route::delete('/delete/{id}', [AdminContactController::class, 'destroy'])->name('destroy')->middleware('checkpermission:contact.delete');
        Route::post('/reply/{contact}', [AdminContactController::class, 'sendReply'])->name('reply')->middleware('checkpermission:contact.reply');
    });

    // Books
    Route::prefix('books')->name('books.')->middleware('checkpermission:book.view')->group(function () {
        Route::get('/', [AdminBookController::class, 'index'])->name('index')->middleware('checkpermission:book.view');
        Route::get('/create', [AdminBookController::class, 'create'])->name('create')->middleware('checkpermission:book.create');
        Route::post('/store', [AdminBookController::class, 'store'])->name('store')->middleware('checkpermission:book.create');
        Route::get('/show/{id}/{slug}', [AdminBookController::class, 'show'])->name('show')->middleware('checkpermission:book.show');
        Route::get('/edit/{id}/{slug}', [AdminBookController::class, 'edit'])->name('edit')->middleware('checkpermission:book.edit');
        Route::put('/update/{id}/{slug}', [AdminBookController::class, 'update'])->name('update')->middleware('checkpermission:book.edit');
        Route::delete('/delete/{id}', [AdminBookController::class, 'destroy'])->name('destroy')->middleware('checkpermission:book.delete');
        Route::get('/trash', [AdminBookController::class, 'trash'])->name('trash')->middleware('checkpermission:book.trash');
        Route::post('/restore/{id}', [AdminBookController::class, 'restore'])->name('restore')->middleware('checkpermission:book.restore');
        Route::delete('/force-delete/{id}', [AdminBookController::class, 'forceDelete'])->name('force-delete')->middleware('checkpermission:book.force-delete');
        Route::delete('/delete-image/{imageId}', [AdminBookController::class, 'deleteImage'])->name('delete-image')->middleware('checkpermission:book.edit');
    });

    // Payment Methods
    Route::prefix('payment-methods')->name('payment-methods.')->middleware('checkpermission:payment-method.view')->group(function () {
        Route::get('/', [AdminPaymentMethodController::class, 'index'])->name('index');
        Route::get('/create', [AdminPaymentMethodController::class, 'create'])->name('create')->middleware('checkpermission:payment-method.create');
        Route::post('/', [AdminPaymentMethodController::class, 'store'])->name('store')->middleware('checkpermission:payment-method.create');
        Route::get('/{paymentMethod}/edit', [AdminPaymentMethodController::class, 'edit'])->name('edit')->middleware('checkpermission:payment-method.edit');
        Route::put('/{paymentMethod}', [AdminPaymentMethodController::class, 'update'])->name('update')->middleware('checkpermission:payment-method.edit');
        Route::delete('/{paymentMethod}', [AdminPaymentMethodController::class, 'destroy'])->name('destroy')->middleware('checkpermission:payment-method.delete');
        Route::get('/trash', [AdminPaymentMethodController::class, 'trash'])->name('trash')->middleware('checkpermission:payment-method.trash');
        Route::put('/{paymentMethod}/restore', [AdminPaymentMethodController::class, 'restore'])->name('restore')->middleware('checkpermission:payment-method.restore');
        Route::delete('/{paymentMethod}/force-delete', [AdminPaymentMethodController::class, 'forceDelete'])->name('force-delete')->middleware('checkpermission:payment-method.force-delete');
        Route::get('/history', [AdminPaymentMethodController::class, 'history'])->name('history')->middleware('checkpermission:payment-method.history');
    });

    // Refunds
    Route::prefix('refunds')->name('refunds.')->middleware('checkpermission:refund.view')->group(function () {
        Route::get('/', [AdminRefundController::class, 'index'])->name('index')->middleware('checkpermission:refund.view');
        Route::get('/{refund}', [AdminRefundController::class, 'show'])->name('show')->middleware('checkpermission:refund.show');
        Route::post('/{refund}/process', [AdminRefundController::class, 'process'])->name('process')->middleware('checkpermission:refund.process');
        Route::get('/statistics', [AdminRefundController::class, 'statistics'])->name('statistics')->middleware('checkpermission:refund.statistics');
    });

    // Wallets
    Route::prefix('wallets')->name('wallets.')->middleware('checkpermission:wallet.view')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index')->middleware('checkpermission:wallet.view');
        Route::get('/deposit-history', [WalletController::class, 'depositHistory'])->name('depositHistory')->middleware('checkpermission:wallet.deposit-history');
        Route::get('/withdraw-history', [WalletController::class, 'withdrawHistory'])->name('withdrawHistory')->middleware('checkpermission:wallet.withdraw-history');
        Route::post('/approve/{id}', [WalletController::class, 'approveTransaction'])->name('approveTransaction')->middleware('checkpermission:wallet.approve');
        Route::post('/reject/{id}', [WalletController::class, 'rejectTransaction'])->name('rejectTransaction')->middleware('checkpermission:wallet.reject');
    });
    // chat real-time
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [AdminChatrealtimeController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminChatrealtimeController::class, 'show'])->name('show');
        Route::post('/send', [AdminChatRealTimeController::class, 'send'])->name('send');
        Route::post('/create-conversation', [AdminChatrealtimeController::class, 'createConversation'])->name('create-conversation');
        Route::get('/users/active', [AdminChatrealtimeController::class, 'getActiveUsers'])->name('users.active');
    });

    // Categories
    Route::prefix('categories')->name('categories.')->middleware('checkpermission:category.view')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index')->middleware('checkpermission:category.view');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create')->middleware('checkpermission:category.create');
        Route::post('/store', [AdminCategoryController::class, 'store'])->name('store')->middleware('checkpermission:category.create');
        Route::get('/edit/{slug}', [AdminCategoryController::class, 'edit'])->name('edit')->middleware('checkpermission:category.edit');
        Route::put('/update/{slug}', [AdminCategoryController::class, 'update'])->name('update')->middleware('checkpermission:category.edit');
        Route::get('/trash', [AdminCategoryController::class, 'trash'])->name('trash')->middleware('checkpermission:category.trash');
        Route::delete('/{slug}', [AdminCategoryController::class, 'destroy'])->name('destroy')->middleware('checkpermission:category.delete');
        Route::put('/{slug}/restore', [AdminCategoryController::class, 'restore'])->name('restore')->middleware('checkpermission:category.restore');
        Route::delete('/{slug}/force', [AdminCategoryController::class, 'forceDelete'])->name('force-delete')->middleware('checkpermission:category.force-delete');
        // Brands
        Route::prefix('brands')->name('brands.')->middleware('checkpermission:brand.view')->group(function () {
            Route::get('/', [CategoryController::class, 'brand'])->name('brand')->middleware('checkpermission:brand.view');
            Route::get('/create', [CategoryController::class, 'BrandCreate'])->name('create')->middleware('checkpermission:brand.create');
            Route::post('/', [CategoryController::class, 'BrandStore'])->name('store')->middleware('checkpermission:brand.create');
            Route::get('/trash', [CategoryController::class, 'BrandTrash'])->name('trash')->middleware('checkpermission:brand.trash');
            Route::delete('/{author}', [CategoryController::class, 'BrandDestroy'])->name('destroy')->middleware('checkpermission:brand.delete');
            Route::put('/{id}/restore', [CategoryController::class, 'BrandRestore'])->name('restore')->middleware('checkpermission:brand.restore');
            Route::delete('/{id}/force', [CategoryController::class, 'BrandForceDelete'])->name('force-delete')->middleware('checkpermission:brand.force-delete');
            Route::get('/{id}/edit', [CategoryController::class, 'BrandEdit'])->name('edit')->middleware('checkpermission:brand.edit');
            Route::put('/{id}', [CategoryController::class, 'BrandUpdate'])->name('update')->middleware('checkpermission:brand.edit');
        });
        // Authors
        Route::prefix('authors')->name('authors.')->middleware('checkpermission:author.view')->group(function () {
            Route::get('/', [AuthorController::class, 'index'])->name('index')->middleware('checkpermission:author.view');
            Route::get('/create', [AuthorController::class, 'create'])->name('create')->middleware('checkpermission:author.create');
            Route::post('/', [AuthorController::class, 'store'])->name('store')->middleware('checkpermission:author.create');
            Route::get('/trash', [AuthorController::class, 'trash'])->name('trash')->middleware('checkpermission:author.trash');
            Route::delete('/{author}', [AuthorController::class, 'destroy'])->name('destroy')->middleware('checkpermission:author.delete');
            Route::put('/{id}/restore', [AuthorController::class, 'restore'])->name('restore')->middleware('checkpermission:author.restore');
            Route::delete('/{id}/force', [AuthorController::class, 'forceDelete'])->name('force-delete')->middleware('checkpermission:author.force-delete');
            Route::get('/{id}/edit', [AuthorController::class, 'edit'])->name('edit')->middleware('checkpermission:author.edit');
            Route::put('/{id}', [AuthorController::class, 'update'])->name('update')->middleware('checkpermission:author.edit');
        });
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->middleware('checkpermission:review.view')->group(function () {
        Route::get('/', [AdminReviewController::class, 'index'])->name('index')->middleware('checkpermission:review.view');
        Route::post('/{review}/response', [AdminReviewController::class, 'storeResponse'])->name('response.store')->middleware('checkpermission:review.response');
        Route::patch('/{review}/update-status', [AdminReviewController::class, 'updateStatus'])->name('update-status')->middleware('checkpermission:review.update-status');
        Route::get('/{review}/response', [AdminReviewController::class, 'showResponseForm'])->name('response')->middleware('checkpermission:review.response');
    });

    // Users
    Route::prefix('users')->name('users.')->middleware('checkpermission:user.view')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('checkpermission:user.view');
        Route::get('/{id}', [UserController::class, 'show'])->name('show')->middleware('checkpermission:user.show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit')->middleware('checkpermission:user.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update')->middleware('checkpermission:user.edit');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy')->middleware('checkpermission:user.delete');
    // Route::get('/{id}/roles-permissions', [UserController::class, 'editRolesPermissions'])->name('roles-permissions.edit')->middleware('checkpermission:user.manage-roles');
    // Route::put('/{id}/roles-permissions', [UserController::class, 'updateRolesPermissions'])->name('roles-permissions.update')->middleware('checkpermission:user.manage-roles');
    });

        // Staff
        Route::prefix('staffs')->name('staff.')->middleware('checkpermission:staff.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('index')->middleware('checkpermission:staff.view');
        Route::get('/create', [\App\Http\Controllers\Admin\StaffController::class, 'create'])->name('create')->middleware('checkpermission:staff.create');
        Route::post('/', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->name('store')->middleware('checkpermission:staff.create');
            Route::get('/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'show'])->name('show')->middleware('checkpermission:staff.show');
            Route::get('/{id}/edit', [\App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('edit')->middleware('checkpermission:staff.edit');
            Route::put('/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->name('update')->middleware('checkpermission:staff.edit');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('destroy')->middleware('checkpermission:staff.delete');
            Route::get('/{id}/roles-permissions', [\App\Http\Controllers\Admin\StaffController::class, 'editRolesPermissions'])->name('roles-permissions.edit')->middleware('checkpermission:staff.manage-roles');
            Route::put('/{id}/roles-permissions', [\App\Http\Controllers\Admin\StaffController::class, 'updateRolesPermissions'])->name('roles-permissions.update')->middleware('checkpermission:staff.manage-roles');
        });

    // Permissions
    Route::prefix('permissions')->name('permissions.')->middleware('checkpermission:permission.view')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index')->middleware('checkpermission:permission.view');
        Route::get('/create', [PermissionController::class, 'create'])->name('create')->middleware('checkpermission:permission.create');
        Route::post('/', [PermissionController::class, 'store'])->name('store')->middleware('checkpermission:permission.create');
        Route::get('/{permission}', [PermissionController::class, 'show'])->name('show')->middleware('checkpermission:permission.show');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit')->middleware('checkpermission:permission.edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update')->middleware('checkpermission:permission.edit');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy')->middleware('checkpermission:permission.delete');
        Route::post('/restore/{id}', [PermissionController::class, 'restore'])->name('restore')->middleware('checkpermission:permission.restore');
        Route::delete('/force-delete/{id}', [PermissionController::class, 'forceDelete'])->name('force-delete')->middleware('checkpermission:permission.force-delete');
        Route::get('/export', [PermissionController::class, 'export'])->name('export')->middleware('checkpermission:permission.export');
    });
    // Collections
    Route::prefix('collections')->name('collections.')->middleware('checkpermission:collection.view')->group(function () {
        Route::get('/', [CollectionController::class, 'index'])->name('index')->middleware('checkpermission:collection.view');
        Route::get('/create', [CollectionController::class, 'create'])->name('create')->middleware('checkpermission:collection.create');
        Route::post('/', [CollectionController::class, 'store'])->name('store')->middleware('checkpermission:collection.create');
        Route::get('/{collection}', [CollectionController::class, 'show'])->name('show')->middleware('checkpermission:collection.show');
        Route::get('/{collection}/edit', [CollectionController::class, 'edit'])->name('edit')->middleware('checkpermission:collection.edit');
        Route::put('/{collection}', [CollectionController::class, 'update'])->name('update')->middleware('checkpermission:collection.edit');
        Route::delete('/{collection}', [CollectionController::class, 'destroy'])->name('destroy')->middleware('checkpermission:collection.delete');
        Route::post('/{collection}/restore', [CollectionController::class, 'restore'])->name('restore')->middleware('checkpermission:collection.restore');
        Route::delete('/{collection}/force', [CollectionController::class, 'forceDelete'])->name('force-delete')->middleware('checkpermission:collection.force-delete');
        Route::post('/{collection}/attach-books', [CollectionController::class, 'attachBooks'])->name('attach-books')->middleware('checkpermission:collection.attach-books');
        Route::get('/trash', [CollectionController::class, 'trash'])->name('trash')->middleware('checkpermission:collection.trash');
        Route::get('/export', [CollectionController::class, 'export'])->name('export')->middleware('checkpermission:collection.export');
    });
    // Vouchers
    Route::prefix('vouchers')->name('vouchers.')->middleware('checkpermission:voucher.view')->group(function () {
        Route::get('/get-condition-options', [VoucherController::class, 'getConditionOptions'])->name('getConditionOptions')->middleware('checkpermission:voucher.get-condition-option');
        Route::get('/search', [VoucherController::class, 'search'])->name('search')->middleware('checkpermission:voucher.search');
        Route::get('/trash', [VoucherController::class, 'trash'])->name('trash')->middleware('checkpermission:voucher.trash');
        Route::post('/restore/{id}', [VoucherController::class, 'restore'])->name('restore')->middleware('checkpermission:voucher.restore');
        Route::delete('/force-delete/{id}', [VoucherController::class, 'forceDelete'])->name('force-delete')->middleware('checkpermission:voucher.force-delete');
        Route::get('/', [VoucherController::class, 'index'])->name('index')->middleware('checkpermission:voucher.view');
        Route::get('/create', [VoucherController::class, 'create'])->name('create')->middleware('checkpermission:voucher.create');
        Route::post('/', [VoucherController::class, 'store'])->name('store')->middleware('checkpermission:voucher.create');
        Route::get('/{voucher}', [VoucherController::class, 'show'])->name('show')->middleware('checkpermission:voucher.show');
        Route::get('/{voucher}/edit', [VoucherController::class, 'edit'])->name('edit')->middleware('checkpermission:voucher.edit');
        Route::put('/{voucher}', [VoucherController::class, 'update'])->name('update')->middleware('checkpermission:voucher.edit');
        Route::delete('/{voucher}', [VoucherController::class, 'destroy'])->name('destroy')->middleware('checkpermission:voucher.delete');
        Route::get('/export', [VoucherController::class, 'export'])->name('export')->middleware('checkpermission:voucher.export');
    });

    // Attributes
    Route::prefix('attributes')->name('attributes.')->middleware('checkpermission:attribute.view')->group(function () {
        Route::get('/', [AttributeController::class, 'index'])->name('index')->middleware('checkpermission:attribute.view');
        Route::get('/create', [AttributeController::class, 'create'])->name('create')->middleware('checkpermission:attribute.create');
        Route::post('/store', [AttributeController::class, 'store'])->name('store')->middleware('checkpermission:attribute.create');
        Route::get('/show/{id}', [AttributeController::class, 'show'])->name('show')->middleware('checkpermission:attribute.show');
        Route::get('/edit/{id}', [AttributeController::class, 'edit'])->name('edit')->middleware('checkpermission:attribute.edit');
        Route::put('/update/{id}', [AttributeController::class, 'update'])->name('update')->middleware('checkpermission:attribute.edit');
        Route::delete('/delete/{id}', [AttributeController::class, 'destroy'])->name('destroy')->middleware('checkpermission:attribute.delete');
    });

    // News
    Route::prefix('news')->name('news.')->middleware('checkpermission:news.view')->group(function () {
        Route::get('/', [NewsArticleController::class, 'index'])->name('index')->middleware('checkpermission:news.view');
        Route::get('/create', [NewsArticleController::class, 'create'])->name('create')->middleware('checkpermission:news.create');
        Route::post('/store', [NewsArticleController::class, 'store'])->name('store')->middleware('checkpermission:news.create');
        Route::get('/show/{article}', [NewsArticleController::class, 'show'])->name('show')->middleware('checkpermission:news.show');
        Route::get('/edit/{article}', [NewsArticleController::class, 'edit'])->name('edit')->middleware('checkpermission:news.edit');
        Route::put('/update/{article}', [NewsArticleController::class, 'update'])->name('update')->middleware('checkpermission:news.edit');
        Route::delete('/delete/{article}', [NewsArticleController::class, 'destroy'])->name('destroy')->middleware('checkpermission:news.delete');
    });


        // GHN routes
        Route::post('/{id}/ghn/create', [OrderController::class, 'createGhnOrder'])->name('orders.ghn.create');
        Route::post('/{id}/ghn/update-tracking', [OrderController::class, 'updateGhnTracking'])->name('orders.ghn.update-tracking');
        Route::post('/{id}/ghn/cancel', [OrderController::class, 'cancelGhnOrder'])->name('orders.ghn.cancel');
    // Orders
    Route::prefix('orders')->name('orders.')->middleware('checkpermission:order.view')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index')->middleware('checkpermission:order.view');
        Route::get('/show/{id}', [OrderController::class, 'show'])->name('show')->middleware('checkpermission:order.show');
        Route::get('/edit/{id}', [OrderController::class, 'edit'])->name('edit')->middleware('checkpermission:order.edit');
        Route::put('/update/{id}', [OrderController::class, 'update'])->name('update')->middleware('checkpermission:order.edit');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->middleware('checkpermission:setting.view')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index')->middleware('checkpermission:setting.view');
        Route::post('/update', [SettingController::class, 'update'])->name('update')->middleware('checkpermission:setting.edit');
    });

    // Invoices
    Route::prefix('invoices')->name('invoices.')->middleware('checkpermission:invoice.view')->group(function () {
        Route::get('/', [AdminInvoiceController::class, 'index'])->name('index')->middleware('checkpermission:invoice.view');
        Route::get('/{id}', [AdminInvoiceController::class, 'show'])->name('show')->middleware('checkpermission:invoice.show');
        Route::get('/{id}/pdf', [AdminInvoiceController::class, 'generatePdf'])->name('generate-pdf')->middleware('checkpermission:invoice.generate-pdf');
    });

    // Roles
    Route::prefix('roles')->name('roles.')->middleware('checkpermission:role.view')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index')->middleware('checkpermission:role.view');
        Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('checkpermission:role.create');
        Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('checkpermission:role.create');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show')->middleware('checkpermission:role.show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('checkpermission:role.edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update')->middleware('checkpermission:role.edit');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy')->middleware('checkpermission:role.delete');
        Route::post('/restore/{id}', [RoleController::class, 'restore'])->name('restore')->middleware('checkpermission:role.restore');
        Route::delete('/force-delete/{id}', [RoleController::class, 'forceDelete'])->name('force-delete')->middleware('checkpermission:role.force-delete');
        Route::get('/export', [RoleController::class, 'export'])->name('export')->middleware('checkpermission:role.export');
    });
});

// Wallet user routes
Route::middleware('auth')->prefix('wallet')->name('wallet.')->group(function () {
    Route::get('/', [App\Http\Controllers\WalletController::class, 'index'])->name('index');
    Route::get('/deposit', [App\Http\Controllers\WalletController::class, 'showDepositForm'])->name('deposit.form');
    Route::post('/deposit', [App\Http\Controllers\WalletController::class, 'deposit'])->name('deposit');
    Route::post('/upload-bill', [App\Http\Controllers\WalletController::class, 'uploadBill'])->name('uploadBill');
    Route::get('/withdraw', [App\Http\Controllers\WalletController::class, 'showWithdrawForm'])->name('withdraw.form');
    Route::post('/withdraw', [App\Http\Controllers\WalletController::class, 'withdraw'])->name('withdraw');
    Route::get('/vnpay-return', [App\Http\Controllers\WalletController::class, 'vnpayReturn'])->name('vnpayReturn');
});

// Ebook Download routes - Secure download with authentication
Route::prefix('ebook')->name('ebook.')->group(function() {
    // Sample downloads (public access)
    Route::get('/sample/download/{formatId}', [App\Http\Controllers\EbookDownloadController::class, 'downloadSample'])->name('sample.download');
    Route::get('/sample/view/{formatId}', [App\Http\Controllers\EbookDownloadController::class, 'viewSample'])->name('sample.view');
    
    // Protected downloads (require authentication and purchase)
    Route::middleware('auth')->group(function() {
        Route::get('/download/{formatId}', [App\Http\Controllers\EbookDownloadController::class, 'download'])->name('download');
        Route::get('/view/{formatId}', [App\Http\Controllers\EbookDownloadController::class, 'view'])->name('view');
    });
});

// Ebook Refund routes
Route::prefix('ebook-refund')->name('ebook-refund.')->middleware('auth')->group(function() {
    Route::get('/{order}', [App\Http\Controllers\EbookRefundController::class, 'show'])->name('show');
    Route::post('/{order}', [App\Http\Controllers\EbookRefundController::class, 'store'])->name('store');
    Route::get('/preview/{order}', [App\Http\Controllers\EbookRefundController::class, 'preview'])->name('preview');
});

// AI Summary routes
Route::prefix('ai-summary')->name('ai-summary.')->middleware(['web'])->group(function () {
    // Book AI Summary routes
    Route::post('/generate/{book}', [App\Http\Controllers\AISummaryController::class, 'generateSummary'])->name('generate');
    Route::get('/get/{book}', [App\Http\Controllers\AISummaryController::class, 'getSummary'])->name('get');
    Route::post('/regenerate/{book}', [App\Http\Controllers\AISummaryController::class, 'regenerateSummary'])->name('regenerate');
    Route::get('/status/{book}', [App\Http\Controllers\AISummaryController::class, 'checkStatus'])->name('status');
    Route::post('/chat/{book}', [App\Http\Controllers\AISummaryController::class, 'chatWithAI'])->name('chat');

    // Combo AI Summary routes
    Route::post('/combo/generate/{combo}', [App\Http\Controllers\AISummaryController::class, 'generateComboSummary'])->name('combo.generate');
    Route::get('/combo/get/{combo}', [App\Http\Controllers\AISummaryController::class, 'getComboSummary'])->name('combo.get');
    Route::post('/combo/regenerate/{combo}', [App\Http\Controllers\AISummaryController::class, 'regenerateComboSummary'])->name('combo.regenerate');
    Route::get('/combo/status/{combo}', [App\Http\Controllers\AISummaryController::class, 'checkComboStatus'])->name('combo.status');
    Route::post('/combo/chat/{combo}', [App\Http\Controllers\AISummaryController::class, 'chatWithComboAI'])->name('combo.chat');
});

// GHN API routes
Route::prefix('api/ghn')->name('ghn.')->group(function() {
    Route::get('/provinces', [App\Http\Controllers\GhnController::class, 'getProvinces'])->name('provinces');
    Route::post('/districts', [App\Http\Controllers\GhnController::class, 'getDistricts'])->name('districts');
    Route::post('/wards', [App\Http\Controllers\GhnController::class, 'getWards'])->name('wards');
    Route::post('/shipping-fee', [App\Http\Controllers\GhnController::class, 'calculateShippingFee'])->name('shipping-fee');
    Route::post('/lead-time', [App\Http\Controllers\GhnController::class, 'getLeadTime'])->name('lead-time');
    Route::post('/services', [App\Http\Controllers\GhnController::class, 'getServices'])->name('services');
    Route::post('/track-order', [App\Http\Controllers\GhnController::class, 'trackOrder'])->name('track-order');
    Route::get('/tracking/{orderCode}', [App\Http\Controllers\GhnController::class, 'trackOrder'])->name('tracking');
});

// Test page for GHN API
Route::get('/test-ghn', function() {
    return view('test-ghn');
});

