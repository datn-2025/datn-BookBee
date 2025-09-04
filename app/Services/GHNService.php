<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GhnService
{
    private $apiUrl;
    private $token;
    private $shopId;
    private $fromDistrictId;
    private $fromWardCode;

    public function __construct()
    {
        $this->apiUrl = config('services.ghn.api_url');
        $this->token = config('services.ghn.token');
        $this->shopId = config('services.ghn.shop_id');
        $this->fromDistrictId = config('services.ghn.from_district_id');
        $this->fromWardCode = config('services.ghn.from_ward_code');
    }

    /**
     * Lấy danh sách tỉnh/thành phố
     */
    public function getProvinces()
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Content-Type' => 'application/json'
            ])->get($this->apiUrl . '/shiip/public-api/master-data/province');

            if ($response->successful()) {
                $provinces = $response->json()['data'];
                
                // Lọc bỏ dữ liệu test trong sandbox
                if (strpos($this->apiUrl, 'dev-online-gateway') !== false) {
                    $provinces = array_filter($provinces, function($province) {
                        $testKeywords = ['test', 'Test', 'TEST', 'demo', 'Demo', 'ngoc', 'Ngoc', 'alert', 'Alert', '02'];
                        $name = $province['ProvinceName'];
                        
                        foreach ($testKeywords as $keyword) {
                            if (strpos($name, $keyword) !== false) {
                                return false;
                            }
                        }
                        return true;
                    });
                    
                    // Reset array keys
                    $provinces = array_values($provinces);
                }
                
                return $provinces;
            }

            Log::error('GHN API Error - Get Provinces: ' . $response->body());
            return [];
        } catch (Exception $e) {
            Log::error('GHN Service Error - Get Provinces: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách quận/huyện theo tỉnh
     */
    public function getDistricts($provinceId)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/shiip/public-api/master-data/district', [
                'province_id' => $provinceId
            ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            Log::error('GHN API Error - Get Districts: ' . $response->body());
            return [];
        } catch (Exception $e) {
            Log::error('GHN Service Error - Get Districts: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách phường/xã theo quận
     */
    public function getWards($districtId)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/shiip/public-api/master-data/ward', [
                'district_id' => $districtId
            ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            Log::error('GHN API Error - Get Wards: ' . $response->body());
            return [];
        } catch (Exception $e) {
            Log::error('GHN Service Error - Get Wards: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Tính phí vận chuyển
     */
    public function calculateShippingFee($toDistrictId, $toWardCode, $weight = 500, $length = 20, $width = 20, $height = 10, $serviceTypeId = 2)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/shiip/public-api/v2/shipping-order/fee', [
                'from_district_id' => $this->fromDistrictId,
                'service_type_id' => $serviceTypeId,
                'to_district_id' => $toDistrictId,
                'to_ward_code' => $toWardCode,
                'weight' => $weight,
                'length' => $length,
                'width' => $width,
                'height' => $height
            ]);

            if ($response->successful()) {
                return $response->json()['data']['total'];
            }

            Log::error('GHN API Error - Calculate Shipping Fee: ' . $response->body());
            return 0;
        } catch (Exception $e) {
            Log::error('GHN Service Error - Calculate Shipping Fee: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy thời gian giao hàng dự kiến
     */
    public function getLeadTime($toDistrictId, $toWardCode, $serviceTypeId = 2)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/shiip/public-api/v2/shipping-order/leadtime', [
                'from_district_id' => $this->fromDistrictId,
                'from_ward_code' => $this->fromWardCode,
                'to_district_id' => $toDistrictId,
                'to_ward_code' => $toWardCode,
                'service_id' => $serviceTypeId
            ]);

            if ($response->successful()) {
                return $response->json()['data']['leadtime'];
            }

            Log::error('GHN API Error - Get Lead Time: ' . $response->body());
            return null;
        } catch (Exception $e) {
            Log::error('GHN Service Error - Get Lead Time: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo đơn hàng GHN
     */
    public function createOrder($orderData)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/shiip/public-api/v2/shipping-order/create', $orderData);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            Log::error('GHN API Error - Create Order: ' . $response->body());
            return null;
        } catch (Exception $e) {
            Log::error('GHN Service Error - Create Order: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tra cứu thông tin đơn hàng
     */
    public function getOrderDetail($orderCode)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/shiip/public-api/v2/shipping-order/detail', [
                'order_code' => $orderCode
            ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            Log::error('GHN API Error - Get Order Detail: ' . $response->body());
            return null;
        } catch (Exception $e) {
            Log::error('GHN Service Error - Get Order Detail: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy danh sách dịch vụ vận chuyển
     */
    public function getServices($toDistrictId)
    {
        try {
            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json'
            ])->post($this->apiUrl . '/shiip/public-api/v2/shipping-order/available-services', [
                'shop_id' => $this->shopId,
                'from_district' => $this->fromDistrictId,
                'to_district' => $toDistrictId
            ]);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            Log::error('GHN API Error - Get Services: ' . $response->body());
            return [];
        } catch (Exception $e) {
            Log::error('GHN Service Error - Get Services: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Chuẩn bị dữ liệu tạo đơn hàng GHN
     */
    public function prepareOrderData($order, $items)
    {
        $totalWeight = 0;
        $itemsData = [];
        
        foreach ($items as $item) {
            if ($item->is_combo) {
                // Xử lý combo - ước tính trọng lượng combo
                $comboWeight = 1000; // 1kg cho combo
                $totalWeight += $comboWeight * $item->quantity;
                
                $itemsData[] = [
                    'name' => $item->collection->name ?? 'Combo',
                    'quantity' => $item->quantity,
                    'weight' => $comboWeight
                ];
            } else {
                // Xử lý sách lẻ
                $bookWeight = ($item->book->page_count ?? 200) * 5;
                $totalWeight += $bookWeight * $item->quantity;
                
                $itemsData[] = [
                    'name' => $item->book->title ?? 'Sách',
                    'quantity' => $item->quantity,
                    'weight' => $bookWeight
                ];
            }
        }

        // Đảm bảo trọng lượng tối thiểu 200g
        $totalWeight = max($totalWeight, 200);

        return [
            'to_name' => $order->recipient_name,
            'to_phone' => $order->recipient_phone,
            'to_address' => $order->address->address_detail,
            'to_ward_code' => $order->address->ward_code ?? '',
            'to_district_id' => $order->address->district_id ?? 0,
            'weight' => $totalWeight,
            'length' => 30, // cm
            'width' => 20,  // cm
            'height' => 10, // cm
            'service_type_id' => 2, // Giao hàng tiêu chuẩn
            'payment_type_id' => $order->payment_method_id == 'cod' ? 2 : 1, // 1: Người gửi trả, 2: Người nhận trả (COD)
            'required_note' => 'KHONGCHOXEMHANG', // Không cho xem hàng
            'items' => $itemsData,
            'cod_amount' => $order->payment_method_id == 'cod' ? $order->total_amount : 0
        ];
    }
}