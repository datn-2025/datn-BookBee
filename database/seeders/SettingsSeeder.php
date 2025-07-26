<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Kiểm tra xem đã có dữ liệu settings chưa
        if (!Setting::count()) {
            Setting::create([
                'name_website' => 'BookBee',
                'logo' => 'default_logo.png',
                'favicon' => 'default_favicon.ico',
                'email' => 'info@bookbee.com',
                'phone' => '0123456789',
                'address' => 'TP. Hồ Chí Minh, Việt Nam',
            ]);
        }
    }
}
