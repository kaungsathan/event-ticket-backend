<?php

use App\Domains\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// All order routes require authentication
Route::middleware('auth:sanctum')->group(function () {

    // Standard CRUD operations
    Route::group(['prefix' => 'orders', 'middleware' => 'auth:sanctum'], function () {
        Route::get('/', [OrderController::class, 'index'])->middleware('permission:view-orders');
        Route::post('/', [OrderController::class, 'store'])->middleware('permission:create-orders');
        Route::get('/{id}', [OrderController::class, 'show'])->middleware('permission:view-orders');
        Route::post('/{id}/update', [OrderController::class, 'update'])->middleware('permission:edit-orders');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->middleware('permission:delete-orders');
    });
});
