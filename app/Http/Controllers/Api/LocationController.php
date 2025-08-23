<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Ward;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Lấy danh sách quận/huyện theo tỉnh/thành phố
     */
    public function getDistricts($provinceId)
    {
        $districts = District::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($districts);
    }
    
    /**
     * Lấy danh sách phường/xã theo quận/huyện
     */
    public function getWards($districtId)
    {
        $wards = Ward::where('district_id', $districtId)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($wards);
    }
}