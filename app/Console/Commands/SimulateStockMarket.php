<?php

namespace App\Console\Commands;

use App\Jobs\ProcessStockPriceTick;
use Illuminate\Console\Command;

class SimulateStockMarket extends Command
{
    protected $signature = 'stocks:simulate {--ticks=10 : Number of price ticks to generate} {--delay=1 : Delay in seconds between ticks}';
    protected $description = 'Simulate real-time stock price messages into the queue';

    public function handle(): void
    {
        $symbols = [
            'AAPL' => 180.50,
            'TSLA' => 210.20,
            'NVDA' => 450.00,
            'MSFT' => 330.10,
        ];

        $ticks = (int) $this->option('ticks');
        $delay = (int) $this->option('delay');

        $this->info("Simulating {$ticks} stock price messages into queue 'market-data'...");

        for ($i = 0; $i < $ticks; $i++) {
            $symbol = array_rand($symbols);
            $delta = (mt_rand(-500, 500) / 100);
            $symbols[$symbol] = max(1.0, round($symbols[$symbol] + $delta, 2));

            ProcessStockPriceTick::dispatch(
                $symbol,
                $symbols[$symbol],
                now()->toIso8601String()
            )->onQueue('market-data');

            $this->line("Dispatched message #".($i + 1).": {$symbol} -> \${$symbols[$symbol]}");

            if ($i < $ticks - 1 && $delay > 0) {
                sleep($delay);
            }
        }

        $this->info("Done! Dispatched {$ticks} messages.");
    }
}
