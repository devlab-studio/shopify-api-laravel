<?php

namespace Devlab\ShopifyApiLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Devlab\ShopifyApiLaravel\ShopifyApiLaravel
 */
class ShopifyApiLaravel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Devlab\ShopifyApiLaravel\ShopifyApiLaravel::class;
    }
}
