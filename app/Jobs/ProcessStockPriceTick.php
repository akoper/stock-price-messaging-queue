<?php

namespace App\Jobs;

use App\Events\StockPriceUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessStockPriceTick implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public string $symbol,
        public float $price,
        public string $timestamp
    ) {}

    public function handle(): void
    {
        Log::info("Processing stock tick message: {$this->symbol} at \${$this->price} (time: {$this->timestamp})");

        // Broadcast/dispatch the domain event to async listeners
        StockPriceUpdated::dispatch($this->symbol, $this->price, $this->timestamp);
    }
}
