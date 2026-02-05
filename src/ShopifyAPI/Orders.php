<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

class Orders
{
    public static $orderNodeQuery = '
        id
        name
        tags
        createdAt
        processedAt
        cancelledAt
        paymentGatewayNames
        confirmationNumber
        currencyCode
        customAttributes {
            key
            value
        }
        fulfillable
        displayFulfillmentStatus
        displayFinancialStatus
        metafields (first: ##metafieldsCount##) {
            nodes {
                id
                key
                value
                type
                namespace
            }
        }
        discountCodes
        totalDiscountsSet {
            shopMoney {
                amount
                currencyCode
            }
        }
        note
        totalShippingPriceSet{
            shopMoney{
                amount
            }
        }
        currentTotalPriceSet {
            shopMoney {
                amount
                currencyCode
            }
        }
        shippingLine {
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
        }
        lineItems(first: 100) {
            nodes {
                id
                title
                currentQuantity
                sku
                discountedTotalSet {
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
            }
        }
        fulfillments(first: 10) {
            id
            status
            trackingInfo {
                company
                number
                url
            }
        }
        fulfillmentOrders(first: 10) {
            nodes {
                id
                status
                lineItems (first: 100) {
                    nodes {
                        id
                        sku
                        totalQuantity
                        lineItem {
                            id
                        }
                    }
                }
            }
        }
        paymentTerms {
          id
        }
        customerLocale
        ##customer##
    ';

    private static function getOrderNodeQuery($withCustomer = false, $metafieldsCount = 15)
    {
        $node_query = self::$orderNodeQuery;
        if ($withCustomer) {
            $node_query = str_replace('##customer##',
                'phone
                email
                billingAddress {
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
                }
                shippingAddress {
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
                }
                customer{
                    id
                    firstName
                    lastName
                    email
                    phone
                }',
                $node_query
            );
        } else {
            $node_query = str_replace('##customer##', '', $node_query);
        }

        $node_query = str_replace('##metafieldsCount##', $metafieldsCount, $node_query);
        return $node_query;
    }

    public static function checkOrder($store, $order_name, $email, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            query {
                orders(first: 10, query: "name:'.$order_name.'") {
                    edges {
                        node {
                            ' . self::$orderNodeQuery . '
                        }
                    }
                }
            }
        ';
        $response = $sh_client->query(["query" => $queryString]);
        $orderResponse = json_decode($response->getBody()->getContents(), true);
        $orderId = isset($orderResponse['data']['orders']['edges']['0']) ? $orderResponse['data']['orders']['edges']['0']['node']['id'] : null;
        $orderEmail = isset($orderResponse['data']['orders']['edges']['0']) ? $orderResponse['data']['orders']['edges']['0']['node']['email'] : null;

        if (!empty($orderId) && (empty($email) || $email == $orderEmail)) {
            return str_replace('gid://shopify/Order/','', $orderId);
        } else {
            return null;
        }
    }
    public static function getOrder($store, $order_id, $sh_client = null, $withCustomer = true, $metafieldsCount = 15)
    {
        if (is_numeric($order_id)) {
            $order_id = 'gid://shopify/Order/'.$order_id;
        }
        $queryString = '
            query getOrder($id: ID!) {
                order (id: $id){
                    '.self::getOrderNodeQuery($withCustomer, $metafieldsCount).'
                }
            }
        ';
        return Core::executeQueryAndHandleErrors($store, $queryString, ['id' => $order_id], 'order', $sh_client);
    }

    public static function getOrders($store, $filters, $cursor = null, $recordsInPage = 100, $sh_client = null, $withCustomer = true, $metafieldsCount = 15)
    {
        $query = '';
        if (!empty($filters)) {
            $text_filters = [];
            $query = 'query: "###FILTERS###", ';
            if (isset($filters['tag_not'])) {
                $text_filters[] = 'tag_not:\''.$filters['tag_not'].'\'';
            }
            if (isset($filters['created_at'])) {
                $text_filters[] = 'created_at:>=\''.$filters['created_at'].'\'';
            }
            if (isset($filters['processed_before'])) {
                $text_filters[] = 'processed_at:<=\''.$filters['processed_before'].'\'';
            }
            if (isset($filters['processed_after'])) {
                $text_filters[] = 'processed_at:>=\''.$filters['processed_after'].'\'';
            }
            if (isset($filters['unfulfilled_processed_before'])) {
               $text_filters[] = 'query: "(processed_at:<=\''.$filters['unfulfilled_processed_before'].'\') AND (fulfillment_status:unfulfilled OR fulfillment_status:partial)", ';
            }
            if (isset($filters['financial_status'])) {
                $text_filters[] = 'financial_status:'.$filters['financial_status'];
            }
            if (isset($filters['fulfillment_status'])) {
                $text_filters[] = 'fulfillment_status:'.$filters['fulfillment_status'];
            }
            if (isset($filters['return_status'])) {
                $text_filters[] = 'return_status:'.$filters['return_status'];
            }
             if (isset($filters['tag'])) {
                $text_filters[] = 'tag:\''.$filters['tag'].'\'';
            }

            $query = str_replace('###FILTERS###', implode(' AND ', $text_filters), $query);
        }

        $queryString = '
         query ($recordsInPage: Int!, $cursor: String){
                orders ('.$query.'first: $recordsInPage, after: $cursor){
                    nodes {
                        '.self::getOrderNodeQuery($withCustomer, $metafieldsCount).'
                    }
                    pageInfo {
                        hasNextPage
                        endCursor
                    }
                }
            }
        ';
        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'recordsInPage' => $recordsInPage,
           'cursor' => $cursor,
        ], 'orders', $sh_client);

        return $response;
    }

    public static function createMetafield($store, $metafields, $sh_client = null)
    {
        $metafieldMutation = '
            mutation MetafieldsSet($metafields: [MetafieldsSetInput!]!) {
                metafieldsSet(metafields: $metafields) {
                    metafields {
                        key
                        namespace
                        value
                        createdAt
                        updatedAt
                    }
                    userErrors {
                        field
                        message
                        code
                    }
                }
            }
        ';

        return Core::executeQueryAndHandleErrors($store, $metafieldMutation, ["metafields" => $metafields], 'metafieldsSet', $sh_client);
    }

    public static function updateFulfillmentTracking($store, $fulfillment_id, $tracking_info, $notify_customer = false, $sh_client = null)
    {
        $mutation = '
            mutation FulfillmentTrackingInfoUpdate($fulfillmentId: ID!, $trackingInfoInput: FulfillmentTrackingInput!, $notifyCustomer: Boolean) {
                fulfillmentTrackingInfoUpdate(fulfillmentId: $fulfillmentId, trackingInfoInput: $trackingInfoInput, notifyCustomer: $notifyCustomer) {
                    fulfillment {
                        id
                        status
                        trackingInfo {
                            company
                            number
                            url
                        }
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';

        $variables = [
            'fulfillmentId' => $fulfillment_id,
            'trackingInfoInput' => $tracking_info,
            'notifyCustomer' => $notify_customer,
        ];
        return Core::executeQueryAndHandleErrors($store, $mutation, $variables, 'fulfillmentTrackingInfoUpdate', $sh_client);
    }

    public static function createOrder($store, $sh_order, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $mutation = '
            mutation orderCreate($order: OrderCreateOrderInput!) {
                orderCreate(order: $order) {
                    order {
                        id
                        name
                        email
                        currentTotalPriceSet{
                            shopMoney {
                                amount
                                currencyCode
                            }
                        }
                        lineItems(first: 100) {
                        edges {
                            node {
                                id
                                title
                                currentQuantity
                                sku
                                discountedTotalSet {
                                    shopMoney {
                                        amount
                                        currencyCode
                                    }
                                }
                                originalTotalSet{
                                    shopMoney{
                                        amount
                                    }
                                }
                                product {
                                    id
                                    title
                                }
                                variant {
                                    id
                                    title
                                }
                            }
                        }
                    }
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }';

        $variables = ['order' => $sh_order];

        return Core::executeQueryAndHandleErrors($store, $mutation, $variables, 'orderCreate', $sh_client);
    }
    public static function createFulfillment($store, $fulfillment, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $mutation = '
            mutation fulfillmentCreate($fulfillment: FulfillmentInput!) {
                fulfillmentCreate(fulfillment: $fulfillment) {
                    fulfillment {
                        id
                        status
                        trackingInfo {
                            company
                            number
                            url
                        }
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';

        $variables = [
            'fulfillment' => $fulfillment,
        ];
        return Core::executeQueryAndHandleErrors($store, $mutation, $variables, 'fulfillmentCreate', $sh_client);
    }

    public static function getOrdersPendingPayment($store, $order_id, $sh_client = null, $withCustomer = true, $metafieldsCount = 15)
    {
        if (is_numeric($order_id)) {
            $order_id = 'gid://shopify/Order/'.$order_id;
        }
        $queryString = '
            query OrdersPendingPayment {
                orders(first: 50, query: "financial_status:pending") {
                    edges {
                        node {
                            id
                            name
                            paymentTerms {
                                id
                            }
                        }
                    }
                }
            }
        ';
        return Core::executeQueryAndHandleErrors($store, $queryString, ['id' => $order_id], 'order', $sh_client);
    }
    public static function markAsPaid($store, $order_id, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $mutation = '
           mutation orderMarkAsPaid($input: OrderMarkAsPaidInput!) {
                orderMarkAsPaid(input: $input) {
                    order {
                        id
                    }
                    userErrors {
                        code
                        field
                        message
                    }
                }
            }
        ';

        $variables = [
            'input' => ['id' => $order_id],
        ];
        return Core::executeQueryAndHandleErrors($store, $mutation, $variables, 'orderMarkAsPaid', $sh_client);
    }

    public static function paymentTermsUpdate($store, $input, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $mutation = '
           mutation PaymentTermsUpdate($input: PaymentTermsUpdateInput!) {
                paymentTermsUpdate(input: $input) {
                    paymentTerms {
                        id
                    }
                    userErrors {
                        code
                        field
                        message
                    }
                }
            }
        ';

        $variables = [
            'input' => $input,
        ];
        return Core::executeQueryAndHandleErrors($store, $mutation, $variables, 'paymentTermsUpdate', $sh_client);
    }

}
