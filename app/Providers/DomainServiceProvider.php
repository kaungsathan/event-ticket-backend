<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Add new domains here when you create them.
     */
    protected array $domains = [
        'Auth',
        'Events',
        'Orders',
        'Users',
        // 'Tickets',
        // 'Analytics',
        // 'Notifications',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadDomainRoutes();
    }

    protected function loadDomainRoutes(): void
    {
        foreach ($this->domains as $domain) {
            $routeFile = app_path("Domains/{$domain}/Routes/" . strtolower($domain) . '.php');

            if (file_exists($routeFile)) {
                Route::middleware('api')
                    ->prefix('api')
                    ->group($routeFile);
            }
        }
    }
}
