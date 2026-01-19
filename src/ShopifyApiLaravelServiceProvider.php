<?php

namespace Devlab\ShopifyApiLaravel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Devlab\ShopifyApiLaravel\Commands\ShopifyApiLaravelCommand;

class ShopifyApiLaravelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * Configura el paquete con Laravel Package Tools
         */
        $package
            ->name('shopify-api-laravel')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_shopify_api_laravel_table')
            ->hasCommand(ShopifyApiLaravelCommand::class);
    }

    public function boot()
    {
        // Charge migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Editable files to be published
        $this->publishes([
            __DIR__ . '/Helpers' => base_path('app/Helpers'),
            __DIR__ . '/Classes' => app_path('Classes'),
            __DIR__ . '/Models' => app_path('Models/ShopifyApiLaravel'),
            __DIR__ . '/Http/Controllers' => app_path('Http/Controllers/ShopifyApiLaravel'),
            __DIR__ . '/ShopifyAPI' => app_path('ShopifyAPI'),
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'shopify-api-laravel-files');

        // Load helpers directly if they are not published
        foreach (glob(__DIR__ . '/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }
}
