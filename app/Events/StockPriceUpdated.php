<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockPriceUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $symbol,
        public float $price,
        public string $timestamp
    ) {}
}
