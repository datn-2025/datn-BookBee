<?php

namespace App\Helpers;

class OrderStatusHelper
{
    public static function validTransitions(): array
    {
        return [
            'Chờ xác nhận' => ['Đã xác nhận', 'Đã hủy'],
            'Đã xác nhận' => ['Đang chuẩn bị', 'Đã hủy'],
            'Đang chuẩn bị' => ['Đang giao hàng'],
            'Đang giao hàng' => ['Đã giao thành công', 'Giao thất bại'],
            'Đã giao thành công' => ['Đã nhận hàng'],
            'Đã nhận hàng' => ['Thành công'],
            'Giao thất bại' => ['Đã hoàn tiền', 'Đang giao hàng'],
            'Đã hủy' => ['Đã hoàn tiền'],
        ];
    }

    public static function getNextStatuses(string $current): array
    {
        return self::validTransitions()[$current] ?? [];
    }

    /**
     * Kiểm tra xem đơn hàng có thể hủy hay không
     * 
     * @param string $orderStatus Trạng thái hiện tại của đơn hàng
     * @return bool
     */
    public static function canBeCancelled(string $orderStatus): bool
    {
        return in_array($orderStatus, ['Chờ xác nhận', 'Đã xác nhận']);
    }

    /**
     * Lấy danh sách trạng thái có thể hủy đơn hàng
     * 
     * @return array
     */
    public static function getCancellableStatuses(): array
    {
        return ['Chờ xác nhận', 'Đã xác nhận'];
    }
}
