<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Kích Thước',
                'values' => [
                    'A4',
                    'A5',
                    '13x20cm',
                    '14x20.5cm',
                    '16x24cm'
                ]
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Ngôn Ngữ',
                'values' => [
                    'Tiếng Việt',
                    'Tiếng Anh',
                    'Song ngữ Việt - Anh'
                ]
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Loại Bìa',
                'values' => [
                    'Bìa mềm',
                    'Bìa cứng',
                    'Bìa lụa'
                ]
            ],
        ];

        foreach ($attributes as $attr) {
            $attribute = Attribute::updateOrCreate(
                ['id' => $attr['id']],
                ['name' => $attr['name']]
            );

            foreach ($attr['values'] as $value) {
                AttributeValue::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'value' => $value
                ]);
            }
        }
    }
}
