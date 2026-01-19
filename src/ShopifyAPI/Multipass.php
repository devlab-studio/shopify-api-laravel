<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

use Exception;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Graphql;
use Shopify\Clients\Storefront;
use Shopify\Context;

date_default_timezone_set("UTC");
class Multipass
{
    public static $encryption_key;
    public static $signature_key;

    public static function generate_token($store, $customer_data_hash)
    {
        $key_material = hash("sha256", $store->api_credentials['multipass_secret'], true);
        self::$encryption_key = substr($key_material, 0, 16);
        self::$signature_key = substr($key_material, 16, 16);

        // Store the current time in ISO8601 format.
        // The token will only be valid for a small timeframe around this timestamp.
        $customer_data_hash["created_at"] = date("c");

        // Serialize the customer data to JSON and encrypt it
        $ciphertext = static::encrypt(json_encode($customer_data_hash));

        // Create a signature (message authentication code) of the ciphertext
        // and encode everything using URL-safe Base64 (RFC 4648)
        return strtr(base64_encode($ciphertext . static::sign($ciphertext)), '+/', '-_');
    }

    private static function encrypt($plaintext)
    {
        // Use a random IV
        $iv = openssl_random_pseudo_bytes(16);
        // Use IV as first block of ciphertext
        return $iv . openssl_encrypt($plaintext, "AES-128-CBC", self::$encryption_key, OPENSSL_RAW_DATA, $iv);
    }

    private static function sign($data)
    {
        return hash_hmac("sha256", $data, self::$signature_key, true);
    }
}
