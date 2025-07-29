<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Dashboard
            ['name' => 'Xem dashboard', 'slug' => 'dashboard.view', 'module' => 'dashboard'],
            ['name' => 'Xem báo cáo doanh thu', 'slug' => 'dashboard.revenue-report', 'module' => 'dashboard'],
            ['name' => 'Xem biểu đồ số dư', 'slug' => 'dashboard.balance-chart', 'module' => 'dashboard'],
            // Profile
            ['name' => 'Xem hồ sơ', 'slug' => 'profile.view', 'module' => 'profile'],
            ['name' => 'Sửa hồ sơ', 'slug' => 'profile.edit', 'module' => 'profile'],
            // Contacts
            ['name' => 'Xem liên hệ', 'slug' => 'contact.view', 'module' => 'contact'],
            ['name' => 'Xem chi tiết liên hệ', 'slug' => 'contact.show', 'module' => 'contact'],
            ['name' => 'Sửa liên hệ', 'slug' => 'contact.edit', 'module' => 'contact'],
            ['name' => 'Xóa liên hệ', 'slug' => 'contact.delete', 'module' => 'contact'],
            ['name' => 'Phản hồi liên hệ', 'slug' => 'contact.reply', 'module' => 'contact'],
            // Books
            ['name' => 'Xem sách', 'slug' => 'book.view', 'module' => 'book'],
            ['name' => 'Thêm sách', 'slug' => 'book.create', 'module' => 'book'],
            ['name' => 'Sửa sách', 'slug' => 'book.edit', 'module' => 'book'],
            ['name' => 'Xóa sách', 'slug' => 'book.delete', 'module' => 'book'],
            ['name' => 'Xem chi tiết sách', 'slug' => 'book.show', 'module' => 'book'],
            ['name' => 'Xem sách đã xóa', 'slug' => 'book.trash', 'module' => 'book'],
            ['name' => 'Khôi phục sách', 'slug' => 'book.restore', 'module' => 'book'],
            ['name' => 'Xóa cứng sách', 'slug' => 'book.force-delete', 'module' => 'book'],
            // Payment Methods
            ['name' => 'Xem phương thức thanh toán', 'slug' => 'payment-method.view', 'module' => 'payment-method'],
            ['name' => 'Thêm phương thức thanh toán', 'slug' => 'payment-method.create', 'module' => 'payment-method'],
            ['name' => 'Sửa phương thức thanh toán', 'slug' => 'payment-method.edit', 'module' => 'payment-method'],
            ['name' => 'Xóa phương thức thanh toán', 'slug' => 'payment-method.delete', 'module' => 'payment-method'],
            ['name' => 'Khôi phục phương thức thanh toán', 'slug' => 'payment-method.restore', 'module' => 'payment-method'],
            ['name' => 'Xóa cứng phương thức thanh toán', 'slug' => 'payment-method.force-delete', 'module' => 'payment-method'],
            ['name' => 'Xem phương thức thanh toán đã xóa', 'slug' => 'payment-method.trash', 'module' => 'payment-method'],
            ['name' => 'Xem chi tiết phương thức thanh toán', 'slug' => 'payment-method.show', 'module' => 'payment-method'],
            ['name' => 'Xem lịch sử phương thức thanh toán', 'slug' => 'payment-method.history', 'module' => 'payment-method'],
            // Refunds
            ['name' => 'Xem hoàn tiền', 'slug' => 'refund.view', 'module' => 'refund'],
            ['name' => 'Xem chi tiết hoàn tiền', 'slug' => 'refund.show', 'module' => 'refund'],
            ['name' => 'Xử lý hoàn tiền', 'slug' => 'refund.process', 'module' => 'refund'],
            ['name' => 'Thống kê hoàn tiền', 'slug' => 'refund.statistics', 'module' => 'refund'],
            // Wallets
            ['name' => 'Xem ví', 'slug' => 'wallet.view', 'module' => 'wallet'],
            ['name' => 'Lịch sử nạp ví', 'slug' => 'wallet.deposit-history', 'module' => 'wallet'],
            ['name' => 'Lịch sử rút ví', 'slug' => 'wallet.withdraw-history', 'module' => 'wallet'],
            ['name' => 'Duyệt giao dịch ví', 'slug' => 'wallet.approve', 'module' => 'wallet'],
            ['name' => 'Từ chối giao dịch ví', 'slug' => 'wallet.reject', 'module' => 'wallet'],
            // Categories

            ['name' => 'Xem danh mục', 'slug' => 'category.view', 'module' => 'category'],
            ['name' => 'Thêm danh mục', 'slug' => 'category.create', 'module' => 'category'],
            ['name' => 'Sửa danh mục', 'slug' => 'category.edit', 'module' => 'category'],
            ['name' => 'Xóa danh mục', 'slug' => 'category.delete', 'module' => 'category'],
            ['name' => 'Khôi phục danh mục', 'slug' => 'category.restore', 'module' => 'category'],
            ['name' => 'Xóa cứng danh mục', 'slug' => 'category.force-delete', 'module' => 'category'],
            ['name' => 'Xem danh sách danh mục đã xóa', 'slug' => 'category.trash', 'module' => 'category'],
            // Brands
            ['name' => 'Xem thương hiệu', 'slug' => 'brand.view', 'module' => 'brand'],
            ['name' => 'Thêm thương hiệu', 'slug' => 'brand.create', 'module' => 'brand'],
            ['name' => 'Sửa thương hiệu', 'slug' => 'brand.edit', 'module' => 'brand'],
            ['name' => 'Xóa thương hiệu', 'slug' => 'brand.delete', 'module' => 'brand'],
            ['name' => 'Khôi phục thương hiệu', 'slug' => 'brand.restore', 'module' => 'brand'],
            ['name' => 'Xóa cứng thương hiệu', 'slug' => 'brand.force-delete', 'module' => 'brand'],
            ['name' => 'Xem danh sách thương hiệu đã xóa', 'slug' => 'brand.trash', 'module' => 'brand'],
            // Authors
            ['name' => 'Xem tác giả', 'slug' => 'author.view', 'module' => 'author'],
            ['name' => 'Thêm tác giả', 'slug' => 'author.create', 'module' => 'author'],
            ['name' => 'Sửa tác giả', 'slug' => 'author.edit', 'module' => 'author'],
            ['name' => 'Xóa tác giả', 'slug' => 'author.delete', 'module' => 'author'],
            ['name' => 'Khôi phục tác giả', 'slug' => 'author.restore', 'module' => 'author'],
            ['name' => 'Xóa cứng tác giả', 'slug' => 'author.force-delete', 'module' => 'author'],
            ['name' => 'Xem danh sách tác giả đã xóa', 'slug' => 'author.trash', 'module' => 'author'],
            // Reviews
            ['name' => 'Xem đánh giá', 'slug' => 'review.view', 'module' => 'review'],
            ['name' => 'Phản hồi đánh giá', 'slug' => 'review.response', 'module' => 'review'],
            ['name' => 'Cập nhật trạng thái đánh giá', 'slug' => 'review.update-status', 'module' => 'review'],
            ['name' => 'Thêm phản hồi', 'slug' => 'review.response', 'module' => 'review'],
            // Users
            ['name' => 'Xem người dùng', 'slug' => 'user.view', 'module' => 'user'],
            ['name' => 'Xem chi tiết người dùng', 'slug' => 'user.show', 'module' => 'user'],
            ['name' => 'Sửa người dùng', 'slug' => 'user.edit', 'module' => 'user'],
            ['name' => 'Xóa người dùng', 'slug' => 'user.delete', 'module' => 'user'],
            ['name' => 'Quản lý vai trò người dùng', 'slug' => 'user.manage-roles', 'module' => 'user'],
            // Permissions
            ['name' => 'Xem quyền', 'slug' => 'permission.view', 'module' => 'permission'],
            ['name' => 'Thêm quyền', 'slug' => 'permission.create', 'module' => 'permission'],
            ['name' => 'Xem chi tiết quyền', 'slug' => 'permission.show', 'module' => 'permission'],
            ['name' => 'Sửa quyền', 'slug' => 'permission.edit', 'module' => 'permission'],
            ['name' => 'Xóa quyền', 'slug' => 'permission.delete', 'module' => 'permission'],
            ['name' => 'Khôi phục quyền', 'slug' => 'permission.restore', 'module' => 'permission'],
            ['name' => 'Xóa cứng quyền', 'slug' => 'permission.force-delete', 'module' => 'permission'],
            ['name' => 'Export quyền', 'slug' => 'permission.export', 'module' => 'permission'],
            // Collections
            ['name' => 'Xem bộ sưu tập', 'slug' => 'collection.view', 'module' => 'collection'],
            ['name' => 'Thêm bộ sưu tập', 'slug' => 'collection.create', 'module' => 'collection'],
            ['name' => 'Sửa bộ sưu tập', 'slug' => 'collection.edit', 'module' => 'collection'],
            ['name' => 'Xóa bộ sưu tập', 'slug' => 'collection.delete', 'module' => 'collection'],
            ['name' => 'Khôi phục bộ sưu tập', 'slug' => 'collection.restore', 'module' => 'collection'],
            ['name' => 'Xóa cứng bộ sưu tập', 'slug' => 'collection.force-delete', 'module' => 'collection'],
            ['name' => 'Xem chi tiết bộ sưu tập', 'slug' => 'collection.show', 'module' => 'collection'],
            ['name' => 'Gắn sách vào bộ sưu tập', 'slug' => 'collection.attach-books', 'module' => 'collection'],
            ['name' => 'Xem bộ sưu tập đã xóa', 'slug' => 'collection.trash', 'module' => 'collection'],
            ['name' => 'Export bộ sưu tập', 'slug' => 'collection.export', 'module' => 'collection'],
            ['name' => 'Xem chi tiết bộ sưu tập đã xóa', 'slug' => 'collection.trash.show', 'module' => 'collection'],
            // Vouchers
            ['name' => 'Xem voucher', 'slug' => 'voucher.view', 'module' => 'voucher'],
            ['name' => 'Thêm voucher', 'slug' => 'voucher.create', 'module' => 'voucher'],
            ['name' => 'Sửa voucher', 'slug' => 'voucher.edit', 'module' => 'voucher'],
            ['name' => 'Xóa voucher', 'slug' => 'voucher.delete', 'module' => 'voucher'],
            ['name' => 'Khôi phục voucher', 'slug' => 'voucher.restore', 'module' => 'voucher'],
            ['name' => 'Xóa cứng voucher', 'slug' => 'voucher.force-delete', 'module' => 'voucher'],
            ['name' => 'Xem chi tiết voucher', 'slug' => 'voucher.show', 'module' => 'voucher'],
            ['name' => 'Export voucher', 'slug' => 'voucher.export', 'module' => 'voucher'],
            ['name' => 'Lấy điều kiện áp dụng voucher', 'slug' => 'voucher.get-condition-option', 'module' => 'voucher'],
            ['name' => 'Tìm kiếm voucher', 'slug' => 'voucher.search', 'module' => 'voucher'],
            ['name' => 'Xem danh sách voucher đã xóa', 'slug' => 'voucher.trash', 'module' => 'voucher'],
            // Attributes
            ['name' => 'Xem thuộc tính', 'slug' => 'attribute.view', 'module' => 'attribute'],
            ['name' => 'Thêm thuộc tính', 'slug' => 'attribute.create', 'module' => 'attribute'],
            ['name' => 'Sửa thuộc tính', 'slug' => 'attribute.edit', 'module' => 'attribute'],
            ['name' => 'Xóa thuộc tính', 'slug' => 'attribute.delete', 'module' => 'attribute'],
            ['name' => 'Xem chi tiết thuộc tính', 'slug' => 'attribute.show', 'module' => 'attribute'],
            // News
            ['name' => 'Xem tin tức', 'slug' => 'news.view', 'module' => 'news'],
            ['name' => 'Thêm tin tức', 'slug' => 'news.create', 'module' => 'news'],
            ['name' => 'Sửa tin tức', 'slug' => 'news.edit', 'module' => 'news'],
            ['name' => 'Xóa tin tức', 'slug' => 'news.delete', 'module' => 'news'],
            ['name' => 'Xem chi tiết tin tức', 'slug' => 'news.show', 'module' => 'news'],
            // Orders
            ['name' => 'Xem đơn hàng', 'slug' => 'order.view', 'module' => 'order'],
            ['name' => 'Xem chi tiết đơn hàng', 'slug' => 'order.show', 'module' => 'order'],
            ['name' => 'Sửa đơn hàng', 'slug' => 'order.edit', 'module' => 'order'],
            // Settings
            ['name' => 'Xem cài đặt', 'slug' => 'setting.view', 'module' => 'setting'],
            ['name' => 'Sửa cài đặt', 'slug' => 'setting.edit', 'module' => 'setting'],
            // Invoices
            ['name' => 'Xem hóa đơn', 'slug' => 'invoice.view', 'module' => 'invoice'],
            ['name' => 'Xem chi tiết hóa đơn', 'slug' => 'invoice.show', 'module' => 'invoice'],
            ['name' => 'Xuất PDF hóa đơn', 'slug' => 'invoice.generate-pdf', 'module' => 'invoice'],
            // Roles
            ['name' => 'Xem vai trò', 'slug' => 'role.view', 'module' => 'role'],
            ['name' => 'Thêm vai trò', 'slug' => 'role.create', 'module' => 'role'],
            ['name' => 'Sửa vai trò', 'slug' => 'role.edit', 'module' => 'role'],
            ['name' => 'Xóa vai trò', 'slug' => 'role.delete', 'module' => 'role'],
            ['name' => 'Khôi phục vai trò', 'slug' => 'role.restore', 'module' => 'role'],
            ['name' => 'Xóa cứng vai trò', 'slug' => 'role.force-delete', 'module' => 'role'],
            ['name' => 'Xem chi tiết vai trò', 'slug' => 'role.show', 'module' => 'role'],
            ['name' => 'Export vai trò', 'slug' => 'role.export', 'module' => 'role'],
        ];

        foreach ($permissions as $permission) {
            $existing = Permission::where('slug', $permission['slug'])->first();

            if ($existing) {
                $existing->update([
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                ]);
            } else {
                Permission::create(array_merge($permission, ['id' => Str::uuid()]));
            }
        }
    }
}
