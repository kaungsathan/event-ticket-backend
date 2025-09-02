<?php

use App\Http\Controllers\OptionController;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Domain routes are loaded by DomainServiceProvider.
| To add a new domain:
| 1. Create: app/Domains/{DomainName}/Routes/{domainname}.php
| 2. Add '{DomainName}' to DomainServiceProvider::$domains array
|
| Current domains: Auth, Events, Organizers, Orders, Users
|
*/

// Domain routes are loaded by DomainServiceProvider

// Event routes
Route::apiResource('events', EventController::class)->except(['edit', 'create']);
Route::post('events/{event}/gallery-image', [EventController::class, 'removeGalleryImage'])->middleware('auth:sanctum');
Route::patch('events/{event}/toggle-publish', [EventController::class, 'togglePublish'])->middleware('auth:sanctum');

Route::group(['prefix' => 'options', 'middleware' => 'auth:sanctum'], function () {
    Route::get('role', [OptionController::class, 'getRoles']);
});

Route::group(['prefix' => 'attributes', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [AttributeController::class, 'index'])->middleware('permission:manage-settings');
    Route::post('/', [AttributeController::class, 'store'])->middleware('permission:manage-settings');
    Route::get('/{attribute}/{id}', [AttributeController::class, 'show'])->middleware('permission:manage-settings');
    Route::put('/{id}', [AttributeController::class, 'update'])->middleware('permission:manage-settings');
    Route::delete('/{attribute}/{id}', [AttributeController::class, 'destroy'])->middleware('permission:manage-settings');
});

Route::group(['prefix' => 'payments', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [PaymentController::class, 'index'])->middleware('permission:manage-settings');
    Route::get('/{id}', [PaymentController::class, 'show'])->middleware('permission:manage-settings');
    Route::put('/{id}', [PaymentController::class, 'update'])->middleware('permission:manage-settings');
});
