<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class RAttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = Attribute::all()->keyBy('name');

        $attributeValues = [
            'Kích Thước' => ['13x20cm', '14x20.5cm', '16x24cm', '17x25cm'],
            'Ngôn Ngữ' => ['Tiếng Việt', 'Tiếng Anh', 'Song ngữ Việt - Anh'],
            'Loại Bìa' => ['Bìa mềm', 'Bìa cứng', 'Bìa lụa'],
        ];

        foreach ($attributeValues as $attrName => $values) {
            $attribute = $attributes->get($attrName);
            if (!$attribute) {
                $this->command->warn("Không tìm thấy thuộc tính: $attrName. Hãy chạy AttributeSeeder trước.");
                continue;
            }

            foreach ($values as $value) {
                AttributeValue::firstOrCreate(
                    [
                        'attribute_id' => $attribute->id,
                        'value' => $value
                    ]
                );
            }
        }
    }
}
