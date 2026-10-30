<?php

namespace App\Listeners;

use App\Events\StockPriceUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CalculateStockMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(StockPriceUpdated $event): void
    {
        Log::info("[Metrics Consumer] Updating moving averages and high/lows for {$event->symbol}");
    }
}
