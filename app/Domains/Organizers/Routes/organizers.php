<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Organizers\Controllers\OrganizerController;

/*
|--------------------------------------------------------------------------
| Organizer API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register organizer-related API routes for your
| application. These routes are loaded by the RouteServiceProvider and
| all of them will be assigned to the "api" middleware group.
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    // Additional organizer-specific actions
    Route::patch('organizers/{organizer}/verify', [OrganizerController::class, 'toggleVerification'])
        ->name('organizers.toggle-verification');

    Route::patch('organizers/{organizer}/status', [OrganizerController::class, 'toggleStatus'])
        ->name('organizers.toggle-status');
});

Route::get('organizers', [OrganizerController::class, 'index']);
Route::post('organizers', [OrganizerController::class, 'store'])->middleware('auth:sanctum');
Route::get('organizers/{id}', [OrganizerController::class, 'show']);
Route::post('organizers/{id}/update', [OrganizerController::class, 'update']);
Route::delete('organizers/{id}', [OrganizerController::class, 'destroy']);
