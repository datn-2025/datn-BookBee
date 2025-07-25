@php
/**
 * View template for generating order QR code content
 *
 * @param \App\Models\Order $order - The order instance to generate QR code for
 */

$qrDataLines = [
    "════════ THÔNG TIN ĐƠN HÀNG ════════",
    "🔹 Mã đơn hàng: " . $order->order_code,
    "📅 Ngày đặt: " . $order->created_at->format('d/m/Y H:i'),
    "",
    "🛒 SẢN PHẨM",
    str_repeat("─", 28)
];

foreach ($order->orderItems as $item) {
    $productName = $item->book ? $item->book->title : 'Sản phẩm không xác định';
    $formatName = $item->bookFormat ? ' (' . $item->bookFormat->format_name . ')' : '';

    $attributesString = '';
    if ($item->attributeValues && $item->attributeValues->count() > 0) {
        $attrParts = [];
        foreach ($item->attributeValues as $av) {
            if ($av->attribute) {
                $attrParts[] = $av->attribute->name . ': ' . ($av->value ?? '');
            }
        }
        if (!empty($attrParts)) {
            $attributesString = ' [' . implode(', ', $attrParts) . ']';
        }
    }
    
    $qrDataLines[] = "▫️ " . $productName . $formatName . $attributesString;
    $qrDataLines[] = "   SL: " . $item->quantity . " × " . number_format($item->price, 0, ',', '.') . "đ";
}

$qrDataLines = array_merge($qrDataLines, [
    "",
    "🚚 THÔNG TIN GIAO HÀNG",
    str_repeat("─", 28),
    "👤 Người nhận: " . $order->recipient_name,
    "📞 Điện thoại: " . $order->recipient_phone,
    "🏠 Địa chỉ: " . $order->address->ward . ', ' . $order->address->district . ', ' . $order->address->city . ($order->address->address_detail ? ', ' . $order->address->address_detail : ''),
    "",
    "💳 THANH TOÁN",
    str_repeat("─", 28)
]);

$qrDataLines[] = "Phí vận chuyển: " . number_format($order->shipping_fee, 0, ',', '.') . "đ";
if ($order->voucher) {
    $discountAmount = $order->discount_amount ?? 0;
    $qrDataLines[] = "Khuyến mãi (" . $order->voucher->code . "): -" . number_format($discountAmount, 0, ',', '.') . "đ";
}
$qrDataLines[] = "TỔNG CỘNG: " . number_format($order->total_amount, 0, ',', '.') . "đ";
$qrDataLines[] = "Phương thức TT: " . ($order->paymentMethod ? $order->paymentMethod->name : 'N/A');
$qrDataLines[] = "Trạng thái TT: " . ($order->paymentStatus ? $order->paymentStatus->name : 'N/A');
$qrDataLines[] = "";
$qrDataLines[] = "══════════════════════════════";

// Return the data as a string
$qrDataString = implode("\n", $qrDataLines);
@endphp

{{ $qrDataString }}