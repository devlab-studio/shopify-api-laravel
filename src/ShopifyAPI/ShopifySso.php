<?php

namespace Devlab\ShopifyApiLaravel\ShopifyApi;

use Devlab\LaravelLogs\Models\ModelsLog;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ShopifySso
{
    public static function getAuthorize($store)
    {
        $authorizeUrl = $store->sso_data['authorize_url'] ?? '';
        $clientId = trim((string) ($store->api_credentials['client_id'] ?? ''));
        $redirectUri = rtrim((string) env('APP_URL'), '/') . '/auth/shopify/code';

        if (blank($authorizeUrl) || blank($clientId)) {
            ModelsLog::doLog(dl_get_procedure(static::class, __FUNCTION__), [
                'error' => 'missing_authorize_config',
                'authorize_url' => $authorizeUrl,
                'client_id_present' => ! blank($clientId),
            ]);

            throw new Exception('Missing Shopify authorize_url or client_id in store configuration.');
        }

        $codeVerifier = Str::random(64);
        session(['shopify_pkce_code_verifier' => $codeVerifier]);

        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $params = [
            'client_id' => $clientId,
            'scope' => 'openid email customer-account-api:full',
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'state' => Str::random(64),
            'nonce' => Str::random(64),
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
            // 'store_id' => $store->id,
        ];

        $separator = str_contains($authorizeUrl, '?') ? '&' : '?';
        $url = $authorizeUrl . $separator . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        ModelsLog::doLog(dl_get_procedure(static::class, __FUNCTION__), [
            'authorize_url' => $authorizeUrl,
            'redirect_uri' => $redirectUri,
            'client_id_prefix' => substr($clientId, 0, 6),
        ]);

        return response()->redirectTo($url);
    }
    public static function getToken($store)
    {
        $response = Http::asForm()->post($store->sso_data['token_url'], [
            'client_id' => $store->api_credentials['client_id'],
            'client_secret' => $store->api_credentials['secret'],
            'grant_type' => 'authorization_code',
        ]);

        return $response->json();
    }


    public static function getUserToken($store, $code)
    {
        $response = Http::asForm()->post($store->sso_data['user_token_url'], [
            'client_id' => $store->api_credentials['client_id'],
            // 'client_secret' => $store->api_credentials['secret'],
            // 'scope' => rawurlencode('openid '.$store->api_credentials['client_id'].' offline_access'),
            'scope' => 'openid '.$store->api_credentials['client_id'].' offline_access',
            'redirect_uri' => env('APP_URL') . '/auth/shopify/code',
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

        ModelsLog::doLog(dl_get_procedure(static::class, __FUNCTION__), $response->json());

        return $response->json();
    }
    public static function getUserInfo($store, $token)
    {
        $response = Http::withToken($token)->get('https://'.$store->api.'/api/v1/fan/me');

        ModelsLog::doLog(dl_get_procedure(static::class, __FUNCTION__), $response->json());

        return $response->json();
    }



    public static function getAppAuthorize($store)
    {
        $authorizeUrl = $store->sso_data['authorize_app_url'] ?? '';
        $clientId = trim((string) ($store->api_credentials['client_id'] ?? ''));
        $redirectUri = rtrim((string) env('APP_URL'), '/') . '/auth/shopify/app-code';

        if (blank($authorizeUrl) || blank($clientId)) {
            ModelsLog::doLog(dl_get_procedure(static::class, __FUNCTION__), [
                'error' => 'missing_authorize_config',
                'authorize_url' => $authorizeUrl,
                'client_id_present' => ! blank($clientId),
            ]);

            throw new Exception('Missing Shopify authorize_url or client_id in store configuration.');
        }

        $codeVerifier = Str::random(64);
        session(['shopify_pkce_code_verifier' => $codeVerifier]);

        $codeChallenge = rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');

        $params = [
            'client_id' => $clientId,
            'scope' => 'read_companies,write_companies,read_customers,write_customers,read_price_rules,write_price_rules,read_discounts,write_discounts,write_inventory,read_inventory,read_markets,write_markets,read_orders,write_orders,read_product_listings,write_product_listings,read_products,write_products,read_publications,write_publications,customer_read_companies,customer_write_companies,customer_write_customers,customer_read_customers,customer_read_draft_orders,customer_read_markets,customer_read_metaobjects,customer_read_orders,customer_write_orders,customer_read_quick_sale,customer_write_quick_sale,customer_read_store_credit_account_transactions,customer_read_store_credit_accounts',
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'state' => Str::random(64),
        ];

        $separator = str_contains($authorizeUrl, '?') ? '&' : '?';
        $url = $authorizeUrl . $separator . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        ModelsLog::doLog(dl_get_procedure(static::class, __FUNCTION__), [
            'authorize_url' => $authorizeUrl,
            'redirect_uri' => $redirectUri,
            'client_id_prefix' => substr($clientId, 0, 6),
        ]);

        return response()->redirectTo($url);
    }
    public static function getAppToken($store, $code)
    {
        $response = Http::asForm()->post($store->sso_data['token_app_url'], [
            'client_id' => $store->api_credentials['client_id'],
            'client_secret' => $store->api_credentials['api_secret'],
            'code' => $code,
        ]);

        ModelsLog::doLog(dl_get_procedure(static::class, __FUNCTION__), [
            'response' => $response->json(),
        ]);

        return $response->json();
    }
}
