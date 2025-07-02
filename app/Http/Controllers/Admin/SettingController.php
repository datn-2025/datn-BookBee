<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Brian2694\Toastr\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('admin.settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name_website' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // validate bank_setting fields
            'bank_name' => 'nullable|string|max:255',
            'bank_number' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $setting = Setting::first();
            if (!$setting) {
                $setting = new Setting();
            }

            $setting->name_website = $request->name_website;
            $setting->email = $request->email;
            $setting->phone = $request->phone;
            $setting->address = $request->address;

            // Lưu cấu hình ngân hàng
            $setting->bank_setting = json_encode([
                'bank_name' => $request->bank_name,
                'bank_number' => $request->bank_number,
                'customer_name' => $request->customer_name,
            ]);

            // Xử lý logo
            if ($request->hasFile('logo')) {
                try {
                    if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                        Storage::disk('public')->delete($setting->logo);
                    }
                    $logoPath = $request->file('logo')->store('settings', 'public');
                    $setting->logo = $logoPath;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Lỗi upload logo: ' . $e->getMessage());
                    toastr()->error('Lỗi khi tải lên logo');
                    return redirect()->back()->withInput();
                }
            }

            // Xử lý favicon
            if ($request->hasFile('favicon')) {
                try {
                    if ($setting->favicon && Storage::disk('public')->exists($setting->favicon)) {
                        Storage::disk('public')->delete($setting->favicon);
                    }
                    $faviconPath = $request->file('favicon')->store('settings', 'public');
                    $setting->favicon = $faviconPath;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Lỗi upload favicon: ' . $e->getMessage());
                    toastr()->error('Lỗi khi tải lên favicon');
                    return redirect()->back()->withInput();
                }
            }

            $setting->save();

            DB::commit();
            toastr()->success('Cập nhật cài đặt thành công');
            return redirect()->route('admin.settings.index');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật cài đặt: ' . $e->getMessage());
            toastr()->error('Đã xảy ra lỗi khi cập nhật cài đặt');
            return redirect()->back()->withInput();
        }
    }
}
