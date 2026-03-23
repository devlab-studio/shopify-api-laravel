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

        $this->offerPublishing();
    }

    protected function offerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        // Editable files to be published
        $this->publishes([
            __DIR__ . '/ShopifyAPI' => app_path('ShopifyAPI'),
        ], 'shopify-api-laravel-files');

        $this->publishes([
            __DIR__ . '/../config/shopify-api-laravel.php' => config_path('shopify-api-laravel.php'),
        ], 'shopify-api-laravel-config');
    }
}
