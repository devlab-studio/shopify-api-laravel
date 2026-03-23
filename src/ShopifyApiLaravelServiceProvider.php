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
            ->hasMigrations('create_stores_table', 'create_store_actions_table');
    }

    public function boot()
    {
        // Charge migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Editable files to be published
        $this->publishes([
            __DIR__ . '/ShopifyAPI' => app_path('ShopifyAPI'),
            __DIR__ . '/../config' => config_path('shopify-api-laravel'),
        ], 'shopify-api-laravel-files');
    }
}
