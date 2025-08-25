<?php

use App\Domains\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// All order routes require authentication
Route::middleware('auth:sanctum')->group(function () {

    // Standard CRUD operations
    Route::apiResource('orders', OrderController::class);

    // Special order actions
    Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])
        ->middleware('permission:edit orders');

    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

    Route::post('/orders/{order}/refund', [OrderController::class, 'refund'])
        ->middleware('permission:refund orders');

    // Order statistics (admin only)
    Route::middleware(['role:super-admin,admin,event-manager'])->group(function () {
        Route::get('/orders/statistics', [OrderController::class, 'statistics']);
    });
});
