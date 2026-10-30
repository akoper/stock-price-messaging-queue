<?php

use App\Jobs\ProcessStockPriceTick;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/stocks/ticker', function (Request $request) {

    $validated = $request->validate([
        'symbol' => 'required|string',
        'price' => 'required|numeric',
    ]);

    ProcessStockPriceTick::dispatch(
        strtoupper($validated['symbol']),
        (float) $validated['price'],
        now()->toIso8601String()
    )->onQueue('market-data');

    return response()->json([
        'status' => 'queued',
        'message' => 'Price update message added to queue.',
    ], 202);
});

Route::get('/', function () {
    return view('welcome');
});
