<?php

use App\Http\Controllers\OptionController;

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

Route::group(['prefix' => 'options'], function () {
    Route::get('role', [OptionController::class, 'getRoles']);
});
