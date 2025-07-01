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
                    'city' => 'Hồ Chí Minh',
                    'district' => 'Quận 1',
                    'ward' => 'Phường Bến Nghé',
                    'is_default' => true
                ],
                [
                    'address_detail' => '456 Đường DEF, Phường UVW',
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
                    'city' => $address['city'],
                    'district' => $address['district'],
                    'ward' => $address['ward'],
                    'is_default' => $address['is_default'],
                ]);
            }
        }
    }
}
