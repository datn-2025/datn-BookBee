<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Payment;
use App\Observers\PaymentObserver;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use App\Http\ViewComposers\CartComposer;

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
        // Register the PersonalAccessToken model for Sanctum
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        
        // Share cart count with navbar and other views that need it
        View::composer([
            'layouts.partials.navbar',
            'layouts.app'
        ], CartComposer::class);
    }
}
