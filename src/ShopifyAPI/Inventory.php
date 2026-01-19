<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

use Exception;

class Inventory
{
    public static $inventoryItemNodeQuery = '
        id
        countryCodeOfOrigin
        countryHarmonizedSystemCodes {
            edges {
                node {
                    countryCode
                    harmonizedSystemCode
                }
            }
        }
        createdAt
        duplicateSkuCount
        harmonizedSystemCode
        inventoryHistoryUrl
        inventoryLevel {
            available
            location {
                id
                name
            }
            updatedAt
        }
        inventoryLevels {
            edges {
                node {
                    available
                    location {
                        id
                        name
                    }
                    updatedAt
                }
            }
        }
        legacyResourceId
        measurement {
            weight {
                unit
                value
            }
            height {
                unit
                value
            }
            width {
                unit
                value
            }
            length {
                unit
                value
            }
        }
        provinceCodeOfOrigin
        requiresShipping
        sku
        tracked
        trackedEditable {
            value
            reason
        }
        unitCost {
            amount
            currencyCode
        }
        updatedAt
        variant {
            id
            title
            availableForSale
            sku
            price
            product {
                id
                title
            }
        }
    ';

}
