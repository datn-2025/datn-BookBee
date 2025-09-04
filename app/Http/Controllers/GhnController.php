<?php

namespace App\Http\Controllers;

use App\Services\GhnService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GhnController extends Controller
{
    protected $ghnService;

    public function __construct(GhnService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    /**
     * Lấy danh sách tỉnh/thành phố
     */
    public function getProvinces(): JsonResponse
    {
        try {
            $provinces = $this->ghnService->getProvinces();
            
            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting provinces: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách tỉnh/thành phố'
            ], 500);
        }
    }

    /**
     * Lấy danh sách quận/huyện theo tỉnh
     */
    public function getDistricts(Request $request): JsonResponse
    {
        $request->validate([
            'province_id' => 'required|integer'
        ]);

        try {
            $districts = $this->ghnService->getDistricts($request->province_id);
            
            return response()->json([
                'success' => true,
                'data' => $districts
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting districts: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách quận/huyện'
            ], 500);
        }
    }

    /**
     * Lấy danh sách phường/xã theo quận
     */
    public function getWards(Request $request): JsonResponse
    {
        $request->validate([
            'district_id' => 'required|integer'
        ]);

        try {
            $wards = $this->ghnService->getWards($request->district_id);
            
            return response()->json([
                'success' => true,
                'data' => $wards
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting wards: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách phường/xã'
            ], 500);
        }
    }

    /**
     * Tính phí vận chuyển
     */
    public function calculateShippingFee(Request $request): JsonResponse
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'weight' => 'nullable|integer|min:1',
            'service_type_id' => 'nullable|integer'
        ]);

        try {
            $weight = $request->weight ?? 350; // Mặc định 500g
            $serviceTypeId = $request->service_type_id ?? 2; // Mặc định giao hàng tiêu chuẩn
            
            $shippingFee = $this->ghnService->calculateShippingFee(
                $request->to_district_id,
                $request->to_ward_code,
                $weight * $request->quantity, // weight in grams
                25 , // length
                17 , // width
                3 * $request->quantity, // height
                $serviceTypeId
            );
            
            return response()->json([
                'success' => true,
                'fee' => $shippingFee,
                'data' => [
                    'total' => $shippingFee,
                    'shipping_fee' => $shippingFee,
                    'formatted_fee' => number_format($shippingFee) . 'đ'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error calculating shipping fee: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể tính phí vận chuyển'
            ], 500);
        }
    }

    /**
     * Lấy thời gian giao hàng dự kiến
     */
    public function getLeadTime(Request $request): JsonResponse
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
            'service_type_id' => 'nullable|integer'
        ]);

        try {
            $serviceTypeId = $request->service_type_id ?? 2;
            
            $leadTime = $this->ghnService->getLeadTime(
                $request->to_district_id,
                $request->to_ward_code,
                $serviceTypeId
            );
            
            $expectedDate = null;
            if ($leadTime) {
                $expectedDate = date('d/m/Y', $leadTime);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'lead_time' => $leadTime,
                    'expected_date' => $expectedDate
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting lead time: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thời gian giao hàng dự kiến'
            ], 500);
        }
    }

    /**
     * Lấy danh sách dịch vụ vận chuyển
     */
    public function getServices(Request $request): JsonResponse
    {
        $request->validate([
            'to_district_id' => 'required|integer'
        ]);

        try {
            $services = $this->ghnService->getServices($request->to_district_id);
            
            return response()->json([
                'success' => true,
                'data' => $services
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting services: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách dịch vụ vận chuyển'
            ], 500);
        }
    }

    /**
     * Tra cứu thông tin đơn hàng
     */
    public function trackOrder($orderCode): JsonResponse
    {
        try {
            $orderDetail = $this->ghnService->getOrderDetail($orderCode);
            
            return response()->json([
                'success' => true,
                'data' => $orderDetail
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Không thể tra cứu thông tin đơn hàng'
            ], 500);
        }
    }
}