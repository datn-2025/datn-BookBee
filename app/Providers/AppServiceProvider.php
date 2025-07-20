<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Payment;
use App\Observers\PaymentObserver;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

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
    }
}
