<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

// Public events routes (for browsing without authentication)
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);

// Protected events routes
Route::middleware('auth:sanctum')->group(function () {

    // Events management (protected)
    Route::middleware(['permission:create events,edit events,delete events'])->group(function () {
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
    });

    // Event publishing (special permission)
    Route::middleware(['permission:publish events'])->group(function () {
        Route::post('/events/{event}/publish', [EventController::class, 'publish']);
        Route::post('/events/{event}/unpublish', [EventController::class, 'unpublish']);
    });
});
