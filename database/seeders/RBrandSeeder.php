<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RBrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'NXB Trẻ',
                'description' => 'Nhà xuất bản Trẻ - Xuất bản đa dạng thể loại sách dành cho mọi lứa tuổi.',
                'image' => 'brands/nxbtre.jpg'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'NXB Kim Đồng',
                'description' => 'Nhà xuất bản Kim Đồng - Chuyên xuất bản sách thiếu nhi nổi tiếng nhất Việt Nam.',
                'image' => 'brands/nxbkimdong.jpg'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'NXB Giáo dục',
                'description' => 'Nhà xuất bản Giáo dục Việt Nam - Chuyên cung cấp sách giáo khoa, tài liệu học tập.',
                'image' => 'brands/nxbgiaoduc.jpg'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'NXB Tổng hợp TP.HCM',
                'description' => 'NXB Tổng hợp TP.HCM - Đơn vị xuất bản đa dạng thể loại sách cho độc giả.',
                'image' => 'brands/nxbtonghop.jpg'
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'NXB Văn học',
                'description' => 'Nhà xuất bản Văn học - Xuất bản các tác phẩm văn học nổi tiếng trong nước và quốc tế.',
                'image' => 'brands/nxbvanhoc.jpg'
            ],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['id' => $brand['id']], $brand);
        }
    }
}
