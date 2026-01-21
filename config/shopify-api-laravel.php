<?php

// config for Devlab/ShopifyApiLaravel
return [


    'product' => [

        'id',
        'title',
        'description',
        'descriptionHtml',
        'productType',
        'vendor',
        'handle',
        'tags',
        'status',
        'hasOnlyDefaultVariant',

        'options {
            name
            position
            values
        }',

        'category {
            id
            name
        }',

        'collections(first: ##collectionsCount##) {
            nodes {
                ##collectionsNodes##
            }
        }',

        'metafields(first: ##metafieldsCount##) {
            nodes {
                ##metafieldsNodes##
            }
        }',

        'media(first: ##mediaCount##) {
            nodes {
              ##mediaNodes##
            }
        }',

        'variants(first: ##variantsCount##) {
            nodes {
                ##variantsNodes##
            }
        }',

    ],

    'variants' => [

        'id',
        'title',
        'sku',
        'price',
        'createdAt',
        'inventoryItem {
            id
            inventoryLevels(first: 10) {
                nodes {
                    id
                    quantities(names: ["available"]) {
                        name
                        quantity
                    }
                }
            }
        }',



        'metafields(first: ##metafieldsCount##) {
            nodes {
                ##metafieldsNodes##
            }
        }',

    ],



    'customer' => [
        'id',
        'firstName',
        'lastName',
        'displayName',
        'email',
        'phone',
        'locale',
        'tags',
        'note',
        'state',
        'createdAt',
        'updatedAt',
        'lifetimeDuration',
        'numberOfOrders',


        'image {
            url
        }',


        'amountSpent {
            amount
            currencyCode
        }',


        'defaultAddress {
            firstName
            lastName
            address1
            address2
            phone
            city
            zip
            province
            provinceCode
            country
            countryCodeV2
        }',

        'addresses {
            firstName
            lastName
            address1
            address2
            phone
            city
            zip
            province
            provinceCode
            country
            countryCodeV2
        }',

        'addressesV2(first: ##addressesCount##) {
            edges {
                node {
                    ##addressesV2Nodes##
                }
            }
        }',


        'events(first: ##eventsCount##) {
            edges {
                node {
                    ##eventsNodes##
                }
            }
        }',

    ],

    'order' => [

        'id',
        'name',
        'tags',
        'createdAt',
        'processedAt',
        'cancelledAt',
        'paymentGatewayNames',
        'confirmationNumber',
        'currencyCode',
        'fulfillable',
        'displayFulfillmentStatus',
        'displayFinancialStatus',
        'discountCodes',
        'note',
        'customerLocale',

        'customAttributes {
            key
            value
        }',

        'metafields(first: ##metafieldsCount##) {
            nodes {
              ##metafieldsNodes##
            }
        }',


        'totalDiscountsSet {
            shopMoney {
                amount
                currencyCode
            }
        }
        totalShippingPriceSet {
            shopMoney {
                amount
            }
        }
        currentTotalPriceSet {
            shopMoney {
                amount
                currencyCode
            }
        }',



        'shippingLine {
            title
            source
            code
            originalPriceSet {
                shopMoney {
                    amount
                    currencyCode
                }
            }
            discountedPriceSet {
                shopMoney {
                    amount
                    currencyCode
                }
            }
        }',



        'lineItems(first: ##lineItemsCount##) {
            nodes {
                ##lineItemsNodes##
            }
        }',



        'fulfillments(first: ##fulfillmentsCount##) {
            id
            status
            trackingInfo {
                company
                number
                url
            }
        }',


        'fulfillmentOrders(first: ##fulfillmentOrdersCount##) {
            nodes {
                ##fulfillmentOrdersNodes##
            }
        }',


        'paymentTerms {
            id
        }',


        'customer {
            id
            displayName
            email
        }',

    ],

    'inventoryItem' => [

        'id',
        'sku',
        'tracked',
        'requiresShipping',
        'createdAt',
        'updatedAt',
        'legacyResourceId',
        'inventoryHistoryUrl',
        'duplicateSkuCount',
        'countryCodeOfOrigin',
        'provinceCodeOfOrigin',

        'countryHarmonizedSystemCodes {
            edges {
                node {
                    countryCode
                    harmonizedSystemCode
                }
            }
        }',

        'inventoryLevels {
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
        }',

        'measurement {
            weight { unit value }
            height { unit value }
            width  { unit value }
            length { unit value }
        }',


        'unitCost {
            amount
            currencyCode
        }',

        'variants {
            id
            title
            availableForSale
            sku
            price
            product {
                id
                title
            }
        }',

    ],


    'metafields' => [
        'id',
        'key',
        'value',
        'type',
        'namespace',
    ],

    'collections' => [
        'id',
        'title',
        'handle',
    ],

    'media' => [
        'id',
        'mediaContentType',
        'preview {
            image {
                id
                url
                altText
            }
        }',
    ],

    'addressesV2' => [
        'firstName',
        'lastName',
        'address1',
        'address2',
        'phone',
        'city',
        'zip',
        'province',
        'provinceCode',
        'country',
        'countryCodeV2',
    ],


    'events' => [
        'id',
        'createdAt',
    ],

    'lineItems' => [
        'id',
        'title',
        'currentQuantity',
        'sku',
        'discountedTotalSet {
            shopMoney {
                amount
                currencyCode
                }
            }
            discountedUnitPriceSet {
                shopMoney {
                    amount
                    currencyCode
                }
            }
            product {
                id
                title
                handle
            }
            variant {
                id
                title
            }
        }'
    ],

    'fulfillmentOrders' => [
        'id',
        'status',
        'lineItems(first: ##lineItemsCount##) {
            nodes {
                ##lineItems##
            }
        }'
    ],

    'lineItems' => [
        'id',
        'sku',
        'totalQuantity',
        'lineItem {
            id
        }'
    ],
];

