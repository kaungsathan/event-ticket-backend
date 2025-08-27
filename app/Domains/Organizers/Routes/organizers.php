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
    // Standard CRUD routes for organizers
    // Route::apiResource('organizers', OrganizerController::class);

    Route::get('organizers', [OrganizerController::class, 'index'])->middleware('permission:view-organizers');
    Route::post('organizers', [OrganizerController::class, 'store'])->middleware('permission:create-organizers');
    Route::get('organizers/{id}', [OrganizerController::class, 'show'])->middleware('permission:view-organizers');
    Route::post('organizers/{id}/update', [OrganizerController::class, 'update'])->middleware('permission:edit-organizers');
    Route::delete('organizers/{id}', [OrganizerController::class, 'destroy'])->middleware('permission:delete-organizers');

    // Additional organizer-specific actions
    Route::patch('organizers/{organizer}/verify', [OrganizerController::class, 'toggleVerification'])
        ->name('organizers.toggle-verification');

    Route::patch('organizers/{organizer}/status', [OrganizerController::class, 'toggleStatus'])
        ->name('organizers.toggle-status');
});
