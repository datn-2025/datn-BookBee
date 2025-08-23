<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Preorder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cập nhật dữ liệu cũ sang constants mới
        $statusMapping = [
            'pending' => Preorder::STATUS_CHO_DUYET,
            'Chờ xác nhận' => Preorder::STATUS_CHO_DUYET,
            'Chờ xử lý' => Preorder::STATUS_CHO_DUYET,
            'confirmed' => Preorder::STATUS_DA_DUYET,
            'Đã xác nhận' => Preorder::STATUS_DA_DUYET,
            'processing' => Preorder::STATUS_SAN_SANG_CHUYEN_DOI,
            'shipped' => Preorder::STATUS_SAN_SANG_CHUYEN_DOI,
            'delivered' => Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG,
            'cancelled' => Preorder::STATUS_DA_HUY,
            'Đã hủy' => Preorder::STATUS_DA_HUY,
        ];

        $totalUpdated = 0;
        foreach ($statusMapping as $oldStatus => $newStatus) {
            $updated = DB::table('preorders')
                ->where('status', $oldStatus)
                ->update(['status' => $newStatus]);
            
            if ($updated > 0) {
                echo "Updated {$updated} records from '{$oldStatus}' to '{$newStatus}'\n";
                $totalUpdated += $updated;
            }
        }
        
        echo "Total updated: {$totalUpdated} preorder records\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback về trạng thái cũ
        $reverseMapping = [
            Preorder::STATUS_CHO_DUYET => 'pending',
            Preorder::STATUS_DA_DUYET => 'confirmed',
            Preorder::STATUS_SAN_SANG_CHUYEN_DOI => 'processing',
            Preorder::STATUS_DA_CHUYEN_THANH_DON_HANG => 'delivered',
            Preorder::STATUS_DA_HUY => 'cancelled',
        ];

        foreach ($reverseMapping as $newStatus => $oldStatus) {
            DB::table('preorders')
                ->where('status', $newStatus)
                ->update(['status' => $oldStatus]);
        }
    }
};
