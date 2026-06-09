<?php

namespace App\ShopifyAPI;

use Devlab\ShopifyApiLaravel\ShopifyAPI\Core;

class Analytics
{

    public static function shopifyqlQuery($store, $query, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            query shopifyqlQuery($query: String!) {
                shopifyqlQuery(query: $query) {
                    tableData {
                        columns {
                            name
                            dataType
                            displayName
                        }
                        rows
                    }
                    parseErrors
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'query' => $query,
        ], 'shopifyqlQuery', $sh_client);

        return $response;
    }

}
