<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Quản lý người dùng
            ['name' => 'Xem người dùng', 'slug' => 'users.view', 'module' => 'users'],
            ['name' => 'Thêm người dùng', 'slug' => 'users.create', 'module' => 'users'],
            ['name' => 'Sửa người dùng', 'slug' => 'users.edit', 'module' => 'users'],
            ['name' => 'Xóa người dùng', 'slug' => 'users.delete', 'module' => 'users'],
            ['name' => 'Xóa cứng người dùng', 'slug' => 'users.force-delete', 'module' => 'users'],

            // Quản lý sách
            ['name' => 'Xem sách', 'slug' => 'books.view', 'module' => 'books'],
            ['name' => 'Thêm sách', 'slug' => 'books.create', 'module' => 'books'],
            ['name' => 'Sửa sách', 'slug' => 'books.edit', 'module' => 'books'],
            ['name' => 'Xóa sách', 'slug' => 'books.delete', 'module' => 'books'],

            // Quản lý đơn hàng
            ['name' => 'Xem đơn hàng', 'slug' => 'orders.view', 'module' => 'orders'],
            ['name' => 'Xử lý đơn hàng', 'slug' => 'orders.process', 'module' => 'orders'],
            ['name' => 'Xóa đơn hàng', 'slug' => 'orders.delete', 'module' => 'orders'],

            // Quản lý phân quyền
            ['name' => 'Quản lý vai trò', 'slug' => 'roles.manage', 'module' => 'roles'],

            // Quản lý danh mục
            ['name' => 'Xem danh mục', 'slug' => 'categories.view', 'module' => 'categories'],
            ['name' => 'Thêm danh mục', 'slug' => 'categories.create', 'module' => 'categories'],
            ['name' => 'Sửa danh mục', 'slug' => 'categories.edit', 'module' => 'categories'],
            ['name' => 'Xóa danh mục', 'slug' => 'categories.delete', 'module' => 'categories'],

            // Quản lý tác giả
            ['name' => 'Xem tác giả', 'slug' => 'authors.view', 'module' => 'authors'],
            ['name' => 'Thêm tác giả', 'slug' => 'authors.create', 'module' => 'authors'],
            ['name' => 'Sửa tác giả', 'slug' => 'authors.edit', 'module' => 'authors'],
            ['name' => 'Xóa tác giả', 'slug' => 'authors.delete', 'module' => 'authors'],

            // Quản lý voucher
            ['name' => 'Xem voucher', 'slug' => 'vouchers.view', 'module' => 'vouchers'],
            ['name' => 'Thêm voucher', 'slug' => 'vouchers.create', 'module' => 'vouchers'],
            ['name' => 'Sửa voucher', 'slug' => 'vouchers.edit', 'module' => 'vouchers'],
            ['name' => 'Xóa voucher', 'slug' => 'vouchers.delete', 'module' => 'vouchers'],

            // Quản lý tin tức
            ['name' => 'Xem tin tức', 'slug' => 'news.view', 'module' => 'news'],
            ['name' => 'Thêm tin tức', 'slug' => 'news.create', 'module' => 'news'],
            ['name' => 'Sửa tin tức', 'slug' => 'news.edit', 'module' => 'news'],
            ['name' => 'Xóa tin tức', 'slug' => 'news.delete', 'module' => 'news'],

            // Quản lý đánh giá
            ['name' => 'Xem đánh giá', 'slug' => 'reviews.view', 'module' => 'reviews'],
            ['name' => 'Phản hồi đánh giá', 'slug' => 'reviews.reply', 'module' => 'reviews'],
            ['name' => 'Xóa đánh giá', 'slug' => 'reviews.delete', 'module' => 'reviews'],

            // Quản lý liên hệ
            ['name' => 'Xem liên hệ', 'slug' => 'contacts.view', 'module' => 'contacts'],
            ['name' => 'Phản hồi liên hệ', 'slug' => 'contacts.reply', 'module' => 'contacts'],
            ['name' => 'Xóa liên hệ', 'slug' => 'contacts.delete', 'module' => 'contacts'],

            // Quản lý thuộc tính sách
            ['name' => 'Xem thuộc tính', 'slug' => 'attributes.view', 'module' => 'attributes'],
            ['name' => 'Thêm thuộc tính', 'slug' => 'attributes.create', 'module' => 'attributes'],
            ['name' => 'Sửa thuộc tính', 'slug' => 'attributes.edit', 'module' => 'attributes'],
            ['name' => 'Xóa thuộc tính', 'slug' => 'attributes.delete', 'module' => 'attributes'],

            // Quản lý phương thức thanh toán
            ['name' => 'Xem phương thức thanh toán', 'slug' => 'payment-methods.view', 'module' => 'payment-methods'],
            ['name' => 'Thêm phương thức thanh toán', 'slug' => 'payment-methods.create', 'module' => 'payment-methods'],
            ['name' => 'Sửa phương thức thanh toán', 'slug' => 'payment-methods.edit', 'module' => 'payment-methods'],
            ['name' => 'Xóa phương thức thanh toán', 'slug' => 'payment-methods.delete', 'module' => 'payment-methods'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
