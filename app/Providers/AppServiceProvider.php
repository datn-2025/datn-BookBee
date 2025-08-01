<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Payment;
use App\Observers\PaymentObserver;
use App\Http\ViewComposers\CartComposer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Payment::observe(PaymentObserver::class);
        Blade::if('permission', function ($permission) {
            return Auth::check() && Auth::user()->hasPermission($permission);
        });
        // Share cart count with navbar and other views that need it
        View::composer([
            'layouts.partials.navbar',
            'layouts.app'
        ], CartComposer::class);
    }
}
