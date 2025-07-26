<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generate QR code for an order
     *
     * @param Order $order
     * @return bool Success status
     */
    public function generateOrderQrCode(Order $order)
    {
        try {
            // Load all necessary relations for the QR code template
            $order->load([
                'orderItems.book',
                'orderItems.bookFormat',
                'orderItems.attributeValues.attribute', // For attributes
                'voucher', // For voucher code
                'paymentMethod', // For payment method name
                'paymentStatus', // For payment status name
                'address'
            ]);

            // Render the QR code content using the template
            $qrDataString = View::make('orders.qr._order_qr_template', [
                'order' => $order
            ])->render();

            // Create QR code filename
            $qrCodeFileName = 'order_' . $order->id . '_' . $order->order_code . '.png';
            $path = storage_path('app/private/qrcodes/' . $qrCodeFileName);

            // Ensure directory exists
            $directory = dirname($path);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate QR code
            QrCode::encoding('UTF-8')->size(250)->generate($qrDataString, $path);

            // Update order with QR code path
            $order->qr_code = 'qrcodes/' . $qrCodeFileName;
            $order->save();

            return true;
        } catch (\Exception $e) {
            Log::error('Error generating QR Code for order ' . $order->id . ': ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return false;
        }
    }
}
