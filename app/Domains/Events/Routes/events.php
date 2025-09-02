<?php

use App\Domains\Events\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'events', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [EventController::class, 'index'])->middleware('permission:view-events');
    Route::post('/', [EventController::class, 'store'])->middleware('permission:create-events');
    Route::get('/{id}', [EventController::class, 'show'])->middleware('permission:view-events');
    Route::post('/{id}/update', [EventController::class, 'update'])->middleware('permission:edit-events');
    Route::delete('/{id}', [EventController::class, 'destroy'])->middleware('permission:delete-events');
});
