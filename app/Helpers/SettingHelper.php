<?php

use App\Models\Setting;

    if(!function_exists('get_setting')){
        function get_setting()
        {
            $settings = Setting::first();
            if (!$settings) {
                return null;
            }
            return $settings ?? null;
        }
    }
