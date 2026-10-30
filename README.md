# Real-Time Stock Market Messaging & Queue Processing

This application is an event-driven message processing system built with Laravel. It demonstrates asynchronous queue processing, publisher-subscriber patterns, and decoupled event handling using simulated stock market price updates.

---

## Overview

The application ingests real-time or simulated stock price updates, pushes them into a dedicated message queue (`market-data`), and processes them asynchronously via queued jobs and event listeners.

```
+------------------------------------------------------+
| Ingestion Sources:                                   |
|   1. CLI Simulation (`php artisan stocks:simulate`)  |
|   2. HTTP API Endpoint (`POST /stocks/ticker`)       |
+------------------------------------------------------+
                           │
                           ▼
            [Queue: `market-data`]
                           │
                           ▼
          [Job: `ProcessStockPriceTick`]
                           │ (dispatches event)
                           ▼
             [Event: `StockPriceUpdated`]
             ┌─────────────┴─────────────┐
             ▼                           ▼
[Listener: `CheckStockPriceAlerts`]  [Listener: `CalculateStockMetrics`]
```

---

## How It Works

### 1. Ingestion / Publishing
Stock price ticks can enter the system through two channels:
- **CLI Simulation Command (`app/Console/Commands/SimulateStockMarket.php`)**:
  Generates randomized price fluctuations for preset stock symbols (`AAPL`, `TSLA`, `NVDA`, `MSFT`) and dispatches jobs onto the `market-data` queue.
- **HTTP REST Endpoint (`routes/web.php`)**:
  Receives external updates via `POST /stocks/ticker` with a JSON payload (`symbol`, `price`), responds immediately with `202 Accepted`, and dispatches the task to the queue.

### 2. Queue Job Processing
- **`ProcessStockPriceTick` (`app/Jobs/ProcessStockPriceTick.php`)**:
  Implements `ShouldQueue`. When executed by the queue worker:
  - Logs the incoming stock tick details (symbol, price, ISO timestamp).
  - Fires domain event `StockPriceUpdated`.

### 3. Event-Driven Listeners (Subscribers)
When `StockPriceUpdated` is dispatched, Laravel invokes all registered listeners asynchronously (both implement `ShouldQueue`):
- **`CheckStockPriceAlerts` (`app/Listeners/CheckStockPriceAlerts.php`)**: Checks whether the stock price crosses configured thresholds and logs alerts.
- **`CalculateStockMetrics` (`app/Listeners/CalculateStockMetrics.php`)**: Updates analytical metrics such as moving averages, daily highs, and lows.

Listeners are mapped to the event inside `app/Providers/AppServiceProvider.php`.

---

## Key Components & Architecture

| Component | Path | Description |
| :--- | :--- | :--- |
| **CLI Simulation** | `app/Console/Commands/SimulateStockMarket.php` | Artisan command to simulate live stock price ticks |
| **HTTP Ingestion** | `routes/web.php` (`POST /stocks/ticker`) | Web endpoint to push ticker data asynchronously |
| **Queued Job** | `app/Jobs/ProcessStockPriceTick.php` | Processes incoming market messages from `market-data` queue |
| **Domain Event** | `app/Events/StockPriceUpdated.php` | Broadcasts price update data to downstream consumers |
| **Alert Listener** | `app/Listeners/CheckStockPriceAlerts.php` | Evaluates price alert triggers for target stocks |
| **Metrics Listener** | `app/Listeners/CalculateStockMetrics.php` | Calculates running statistics and financial metrics |
| **Event Registration**| `app/Providers/AppServiceProvider.php` | Binds events to their corresponding queued listeners |

---

## Running the Application

### 1. Start the Queue Worker
To process jobs and queued event listeners, run the queue worker:

```bash
php artisan queue:work --queue=market-data,default
```

### 2. Trigger Stock Price Simulations
Run the built-in simulation command:

```bash
# Default (10 ticks with 1-second delay)
php artisan stocks:simulate

# Custom ticks and delay
php artisan stocks:simulate --ticks=20 --delay=0.5
```

### 3. Send Updates via HTTP API
Start the local development server:

```bash
php artisan serve
```

Send a POST request with stock data:

```bash
curl -X POST http://127.0.0.1:8000/stocks/ticker \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"symbol": "AAPL", "price": 182.45}'
```

### 4. Monitor Logs
Check incoming logs and processed updates in `storage/logs/laravel.log`:

```bash
tail -f storage/logs/laravel.log
```
