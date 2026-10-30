<?php

namespace App\Providers;

use App\Events\StockPriceUpdated;
use App\Listeners\CalculateStockMetrics;
use App\Listeners\CheckStockPriceAlerts;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(
            StockPriceUpdated::class,
            CheckStockPriceAlerts::class,
        );

        Event::listen(
            StockPriceUpdated::class,
            CalculateStockMetrics::class,
        );
    }
}
