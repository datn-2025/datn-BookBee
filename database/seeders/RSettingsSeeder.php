<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class RSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Cài đặt chung
            [
                'key' => 'site_name',
                'value' => 'BookStore',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Tên website',
                'order' => 1,
            ],
            [
                'key' => 'site_description',
                'value' => 'Cửa hàng sách trực tuyến hàng đầu Việt Nam',
                'type' => 'textarea',
                'group' => 'general',
                'label' => 'Mô tả website',
                'order' => 2,
            ],
            [
                'key' => 'site_logo',
                'value' => 'logo.png',
                'type' => 'image',
                'group' => 'general',
                'label' => 'Logo',
                'order' => 3,
            ],
            [
                'key' => 'site_favicon',
                'value' => 'favicon.ico',
                'type' => 'image',
                'group' => 'general',
                'label' => 'Favicon',
                'order' => 4,
            ],
            
            // Cài đặt liên hệ
            [
                'key' => 'contact_email',
                'value' => 'contact@bookstore.vn',
                'type' => 'email',
                'group' => 'contact',
                'label' => 'Email liên hệ',
                'order' => 1,
            ],
            [
                'key' => 'contact_phone',
                'value' => '1900 1234',
                'type' => 'text',
                'group' => 'contact',
                'label' => 'Số điện thoại',
                'order' => 2,
            ],
            [
                'key' => 'contact_address',
                'value' => 'Số 1, Đường ABC, Quận 1, TP.HCM',
                'type' => 'textarea',
                'group' => 'contact',
                'label' => 'Địa chỉ',
                'order' => 3,
            ],
            
            // Cài đặt thanh toán
            [
                'key' => 'payment_cod',
                'value' => '1',
                'type' => 'checkbox',
                'group' => 'payment',
                'label' => 'Bật thanh toán khi nhận hàng',
                'order' => 1,
            ],
            [
                'key' => 'payment_bank_transfer',
                'value' => '1',
                'type' => 'checkbox',
                'group' => 'payment',
                'label' => 'Bật chuyển khoản ngân hàng',
                'order' => 2,
            ],
            [
                'key' => 'payment_momo',
                'value' => '1',
                'type' => 'checkbox',
                'group' => 'payment',
                'label' => 'Bật thanh toán Momo',
                'order' => 3,
            ],
            
            // Cài đặt vận chuyển
            [
                'key' => 'shipping_fee',
                'value' => '30000',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Phí vận chuyển (VNĐ)',
                'order' => 1,
            ],
            [
                'key' => 'free_shipping_min_amount',
                'value' => '500000',
                'type' => 'number',
                'group' => 'shipping',
                'label' => 'Miễn phí vận chuyển đơn từ (VNĐ)',
                'order' => 2,
            ],
            
            // Cài đặt SEO
            [
                'key' => 'meta_title',
                'value' => 'BookStore - Mua sách online giá tốt',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Meta Title',
                'order' => 1,
            ],
            [
                'key' => 'meta_description',
                'value' => 'Mua sách online với nhiều thể loại sách phong phú, đa dạng. Giao hàng toàn quốc, thanh toán khi nhận hàng.',
                'type' => 'textarea',
                'group' => 'seo',
                'label' => 'Meta Description',
                'order' => 2,
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'sách, mua sách, sách online, sách hay, đọc sách',
                'type' => 'text',
                'group' => 'seo',
                'label' => 'Meta Keywords',
                'order' => 3,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
