<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Preorder;
use App\Models\AttributeValue;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix existing ebook preorders to only have language attributes
        $ebookPreorders = Preorder::with(['bookFormat'])
            ->whereHas('bookFormat', function($query) {
                $query->where('format_name', 'like', '%ebook%');
            })
            ->get();

        foreach ($ebookPreorders as $preorder) {
            if ($preorder->selected_attributes && is_array($preorder->selected_attributes)) {
                // Filter to keep only language attributes
                $languageAttributeIds = AttributeValue::whereIn('id', $preorder->selected_attributes)
                    ->with('attribute')
                    ->get()
                    ->filter(function($attrValue) {
                        $attributeName = strtolower($attrValue->attribute->name);
                        return strpos($attributeName, 'ngôn ngữ') !== false || 
                               strpos($attributeName, 'language') !== false;
                    })
                    ->pluck('id')
                    ->toArray();

                // Update preorder with only language attributes
                $preorder->update([
                    'selected_attributes' => $languageAttributeIds
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be reversed as we don't store the original data
    }
};
