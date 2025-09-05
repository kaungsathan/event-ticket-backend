<?php

use App\Domains\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// All order routes require authentication
Route::group(['prefix' => 'orders'], function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::post('/{id}/update', [OrderController::class, 'update']);
    Route::delete('/{id}', [OrderController::class, 'destroy']);
});
