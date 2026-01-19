<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

use Exception;

class Store
{
    public static function getShop($store, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            query {
                shop {
                    id
                    name
                    url
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [], 'shop', $sh_client);

        return $response;
    }
}
