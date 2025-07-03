<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Danh mục cha
        $parentCategories = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Sách Văn Học',
                'slug' => Str::slug('Sách Văn Học'),
                'description' => 'Các thể loại tiểu thuyết, truyện ngắn, truyện dài...',
                'image' => 'categories/vanhoc.jpg',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Sách Kinh Tế',
                'slug' => Str::slug('Sách Kinh Tế'),
                'description' => 'Sách về kinh doanh, đầu tư, quản trị...',
                'image' => 'categories/kinhte.jpg',
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Sách Thiếu Nhi',
                'slug' => Str::slug('Sách Thiếu Nhi'),
                'description' => 'Sách dành cho trẻ em, truyện tranh thiếu nhi...',
                'image' => 'categories/thieunhi.jpg',
            ],
        ];

        foreach ($parentCategories as $parent) {
            Category::firstOrCreate(['id' => $parent['id']], $parent);
        }

        // Lấy id danh mục cha để gán cho danh mục con
        // $vanHocId = Category::where('name', 'Sách Văn Học')->first()->id;
        // $kinhTeId = Category::where('name', 'Sách Kinh Tế')->first()->id;

        // // Danh mục con có parent_id
        // $childCategories = [
        //     [
        //         'id' => (string) Str::uuid(),
        //         'name' => 'Tiểu Thuyết',
        //         'slug' => Str::slug('Tiểu Thuyết'),
        //         'description' => 'Các thể loại tiểu thuyết nổi bật...',
        //         'image' => 'categories/tieuthuyet.jpg',
        //         'parent_id' => $vanHocId,
        //     ],
        //     [
        //         'id' => (string) Str::uuid(),
        //         'name' => 'Tâm lý - Kỹ năng',
        //         'slug' => Str::slug('Tâm lý - Kỹ năng'),
        //         'description' => 'Sách phát triển bản thân, kỹ năng sống...',
        //         'image' => 'categories/tamlykynang.jpg',
        //         'parent_id' => $kinhTeId,
        //     ],
        // ];

        // foreach ($childCategories as $child) {
        //     Category::firstOrCreate(['id' => $child['id']], $child);
        // }
    }
}
