<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RAddressSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('Không có người dùng nào, hãy seed bảng users trước!');
            return;
        }

        foreach ($users as $user) {
            // Danh sách địa chỉ mẫu
            $addresses = [
                [
                    'address_detail' => '123 Đường ABC, Phường XYZ',
                    'recipient_name' => 'Nguyễn Văn A',
                    'phone' => '0123456789',
                    'city' => 'Hồ Chí Minh',
                    'district' => 'Quận 1',
                    'ward' => 'Phường Bến Nghé',
                    'is_default' => true
                ],
                [
                    'address_detail' => '456 Đường DEF, Phường UVW',
                    'recipient_name' => 'Trần Thị B',
                    'phone' => '0123456789',
                    'city' => 'Hồ Chí Minh',
                    'district' => 'Quận 3',
                    'ward' => 'Phường Võ Thị Sáu',
                    'is_default' => false
                ],
            ];

            foreach ($addresses as $address) {
                Address::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'address_detail' => $address['address_detail'],
                    'phone' => $address['phone'],
                    'city' => $address['city'],
                    'district' => $address['district'],
                    'ward' => $address['ward'],
                    'recipient_name' => $address['recipient_name'],
                    'is_default' => $address['is_default'],
                ]);
            }
        }
    }
}
