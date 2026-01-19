<?php

namespace Devlab\ShopifyApiLaravel\Classes;


use Devlab\ShopifyApiLaravel\ShopifyAPI\SecretProvider;
use Devlab\ShopifyApiLaravel\ShopifyAPI\Store;
use Illuminate\Support\Facades\Log;

class WebhookSecretProvider implements SecretProvider
{
    public function getSecret(string $domain): string
    {
        $store = Store::whereJsonContains('store_urls', [$domain])->get();
        if (length($store) > 0) {
            $store = $store->first();
        } else {
            $store = null;
        }

        $token = ($store) ? ($store->api_credentials['webhook_secret'] ?? '##') : '##';
        // Log::info('URL: '.$domain.' => token: '.$token);
        return $token;
    }
    public function getSecret2(string $domain): string
    {
        $store = Store::whereJsonContains('store_urls', [$domain])->get();
        if (length($store) > 0) {
            $store = $store->first();
        } else {
            $store = null;
        }

        $token = ($store) ? ($store->api_credentials['api_secret'] ?? '##') : '##';
        // Log::info('URL: '.$domain.' => token: '.$token);
        return $token;
    }
}
