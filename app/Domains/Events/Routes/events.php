<?php

use App\Domains\Events\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'events'], function () {
    Route::get('/', [EventController::class, 'index']);
    Route::post('/', [EventController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/{id}', [EventController::class, 'show']);
    Route::post('/{id}/update', [EventController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [EventController::class, 'destroy'])->middleware('auth:sanctum');
});
