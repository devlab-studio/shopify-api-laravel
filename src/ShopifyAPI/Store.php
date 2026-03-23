<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

use Exception;

class Store
{
    public static function getShop($store, $sh_client = null, $with = [], $limits = [])
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = (new BuildGraphQl('shop'))->with($with)->limits($limits)->build();

        $queryString = '
            query {
                shop {
                    '.$queryString.'
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [], 'shop', $sh_client);

        return $response;
    }
    public static function getAppp($store, $app_id, $sh_client = null, $with = [], $limits = [])
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = (new BuildGraphQl('app'))->with($with)->limits($limits)->build();

        $queryString = '
            query getApp($id: ID!) {
                app (id: $id){
                    '.$queryString.'
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, ['id' => $app_id], 'app', $sh_client);

        return $response;
    }
    public static function getApppByKey($store, $apikey, $sh_client = null, $with = [], $limits = [])
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = (new BuildGraphQl('app'))->with($with)->limits($limits)->build();

        $queryString = '
            query getAppByKey($apikey: String!) {
                appByKey (apiKey: $apikey){
                    '.$queryString.'
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, ['apikey' => $apikey], 'appByKey', $sh_client);

        return $response;
    }
}
