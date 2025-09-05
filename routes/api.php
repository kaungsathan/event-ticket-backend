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

Route::group(['prefix' => 'options', 'middleware' => 'auth:sanctum'], function () {
    Route::get('role', [OptionController::class, 'getRoles']);
});

Route::group(['prefix' => 'attributes'], function () {
    Route::get('/', [AttributeController::class, 'index']);
    Route::post('/', [AttributeController::class, 'store']);
    Route::get('/{attribute}/{id}', [AttributeController::class, 'show']);
    Route::put('/{id}', [AttributeController::class, 'update']);
    Route::delete('/{attribute}/{id}', [AttributeController::class, 'destroy']);
});

Route::group(['prefix' => 'payments'], function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::get('/{id}', [PaymentController::class, 'show']);
    Route::put('/{id}', [PaymentController::class, 'update']);
});
