<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,    
            // // Thêm Settings trước
            // RoleSeeder::class,
            // ActiveUserSeeder::class,
            // UserSeeder::class,
            // CategorySeeder::class,
            // BrandSeeder::class,
            // AuthorSeeder::class,
            // AttributeSeeder::class,
            // AttributeValueSeeder::class,
            // NewsArticleSeeder::class,
            // BookSeeder::class,            // Book sẽ tự tạo Format, Image và Attribute Values
            // // Bỏ 3 seeder này vì đã được xử lý trong BookSeeder
            // // BookFormatSeeder::class,
            // // BookAttributeValueSeeder::class,
            // // BookImageSeeder::class,
            // AddressSeeder::class,
            // PaymentMethodSeeder::class,
            // PaymentStatusSeeder::class,
            // OrderStatusSeeder::class,
            // VoucherSeeder::class,
            // AppliedVoucherSeeder::class,
            // ReviewSeeder::class,
            // PaymentSeeder::class,
            // WishlistSeeder::class,
            // CartSeeder::class,            // Đưa CartSeeder xuống sau các bảng dữ liệu liên quan
            // OrderSeeder::class,
            // InvoiceSeeder::class,
            // InvoiceItemSeeder::class,
            // WalletSeeder::class,
            // WalletTransactionSeeder::class


            // RSeeder
            RRoleSeeder::class,
            ActiveUserSeeder::class,
            RUserSeeder::class,
            RCategorySeeder::class,
            RBrandSeeder::class,
            RAuthorSeeder::class,
            RAttributeSeeder::class,
            RAttributeValueSeeder::class,
            RNewsArticleSeeder::class,
            RBookSeeder::class,
            RAddressSeeder::class,
            PaymentMethodSeeder::class,
            PaymentStatusSeeder::class,
            OrderStatusSeeder::class,
            RVoucherSeeder::class,
            RAppliedVoucherSeeder::class,
            RReviewSeeder::class,
            RPaymentSeeder::class,
            RWishlistSeeder::class,
            RCartSeeder::class,            // Đưa CartSeeder xuống sau các bảng dữ liệu liên quan
            ROrderSeeder::class,
            ROrderItemSeeder::class,
            InvoiceSeeder::class,
            RInvoiceItemSeeder::class,
            RWalletSeeder::class,
            RWalletTransactionSeeder::class
        ]);
    }
}
