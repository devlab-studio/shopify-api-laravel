<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

use Devlab\ShopifyApiLaravel\Models\Store;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Graphql;
use Shopify\Context;
use Exception;
use Illuminate\Support\Facades\Http;

use Shopify\Clients\Storefront;

class Core
{
    public static function getGraphQLClient($store)
    {
        if (isset($store->api_credentials['client_id'])){
            if (empty($store->api_credentials['api_token'])) {
                self::getToken($store);
            }
            Context::initialize($store->api_credentials['client_id'], $store->api_credentials['api_secret'], '*', $store->api_credentials['api_url'],
            new FileSessionStorage(sys_get_temp_dir()), $store->api_credentials['api_version']);
            $sh_client = new Graphql(
                $store->api_credentials['api_url'],
                $store->api_credentials['api_token']
            );

        }else{

            Context::initialize($store->api_credentials['api_token'], $store->api_credentials['api_secret'], '*', $store->api_credentials['api_url'],
            new FileSessionStorage(sys_get_temp_dir()), $store->api_credentials['api_version']);
            $sh_client = new Graphql(
                $store->api_credentials['api_url'],
                $store->api_credentials['api_token']
            );
        }
        return $sh_client;
    }

    public static function getStoreFrontClient($store)
    {
        Context::initialize($store->api_credentials['api_token'], $store->api_credentials['api_secret'], '*', $store->api_credentials['api_url'],
            new FileSessionStorage(sys_get_temp_dir()), $store->api_credentials['api_version']);
        $sh_client = new Storefront(
            $store->api_credentials['api_url'],
            $store->api_credentials['store_front_token']
        );

        return $sh_client;
    }


    public static function executeQueryAndHandleErrors($store, $queryString, $variables = [], $type = null, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        try {

            if (empty($type)) {
                return ["errors" => "Type parameter is missing."];
            }

            if (empty($variables)) {
                $response = $sh_client->query(["query" => $queryString]);
            } else {
                $response = $sh_client->query(["query" => $queryString, "variables" => $variables]);
            }

            $response_status = $response->getStatusCode();
            $response = json_decode($response->getBody()->getContents(), true);
            // Refresh token and retry once if unauthorized
            if (isset($response['errors']) && is_string($response['errors']) && $response_status == 401){
                self::getToken($store);
                $sh_client = Core::getGraphQLClient($store);
                if (empty($variables)) {
                    $response = $sh_client->query(["query" => $queryString]);
                } else {
                    $response = $sh_client->query(["query" => $queryString, "variables" => $variables]);
                }
                $response = json_decode($response->getBody()->getContents(), true);
            }

            if (isset($response['errors'][0]['extensions']['code']) &&
                ($response['errors'][0]['extensions']['code']=='MAX_COST_EXCEEDED' || $response['errors'][0]['extensions']['code']=='THROTTLED')
            ) {
                sleep(10);
                if (empty($variables)) {
                    $response = $sh_client->query(["query" => $queryString]);
                } else {
                    $response = $sh_client->query(["query" => $queryString, "variables" => $variables]);
                }
                $response = json_decode($response->getBody()->getContents(), true);

                if (isset($response['errors'][0]['extensions']['code']) &&
                    ($response['errors'][0]['extensions']['code']=='MAX_COST_EXCEEDED' || $response['errors'][0]['extensions']['code']=='THROTTLED')
                ) {
                    sleep(10);
                    if (empty($variables)) {
                        $response = $sh_client->query(["query" => $queryString]);
                    } else {
                        $response = $sh_client->query(["query" => $queryString, "variables" => $variables]);
                    }
                    $response = json_decode($response->getBody()->getContents(), true);
                }
            }

            // $available_query_cost = $response['extensions']['cost']['throttleStatus']['currentlyAvailable'] ?? 2000;
            // if ($available_query_cost < 500) {
            //     sleep(5);
            // }

            if (isset($response['data'][$type]['userErrors']) && !empty($response['data'][$type]['userErrors'])) {
                return ["errors" => $response['data'][$type]['userErrors']];
            } elseif (isset($response['errors']) && !empty($response['errors'])) {
                return ["errors" => $response['errors']];
            } elseif (isset($response['data'][$type]['nodes'])) {
                return [
                    "nodes" => $response['data'][$type]['nodes'],
                    "pageInfo" => $response['data'][$type]['pageInfo'] ?? null,
                    "extensions" => $response['extensions'],
                ];
            } elseif (isset($response['data'][$type])) {
                return $response['data'][$type];
            } else {
                return [
                    "errors" => "Unexpected response or missing data",
                    "errors_detail" => $response
                ];
            }

        } catch (Exception $e) {
            return ["errors" => "Exception occurred: " . $e->getMessage()];
        }
    }

    public static function getToken($store)
    {
        $response = Http::post('https://'.$store->api_credentials['api_url'].'/admin/oauth/access_token', [
            'grant_type' => 'client_credentials',
            'client_id' => $store->api_credentials['client_id'],
            'client_secret' => $store->api_credentials['api_secret']]
        );

        if ($response->successful()) {
            $response = $response->json();
            $expires_in = now()->addSeconds($response['expires_in']);
            $api_credentials = $store->api_credentials;
            $api_credentials['api_token'] = $response['access_token'];
            $api_credentials['expires_in'] = $expires_in;
            $store->api_credentials = $api_credentials;
            $store->save();
        }

        return $response;
    }

    public static function refreshToken()
    {
        $stores = Store::dlGet(0, 0, [], ['expires_in' => 'expires_in'], []);
        foreach($stores as $store){
            if(empty($store->api_credentials['expires_in']) || (!isset($store->api_credentials['expires_in']))){
                continue;
            } else {
                self::getToken($store);
            }
        }
    }
}
