<?php
namespace App\Config;

return [
    'domains' => [
        'Auth',
        'Events',
        'Organizers',
        'Orders',
        'Users',
    ],

    'route_settings' => [
        'middleware' => 'api',
        'prefix' => 'api',
    ],
];
