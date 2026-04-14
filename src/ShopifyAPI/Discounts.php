<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

use Devlab\ShopifyApiLaravel\ShopifyAPI\BuildGraphQl;
use Devlab\ShopifyApiLaravel\ShopifyAPI\Core;

class Discounts
{
    public static function getCodeDiscount($store, $discount_id, $sh_client = null , $with = [], $limits = [])
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($discount_id)) {
            $discount_id = 'gid://shopify/DiscountCodeNode/'.$discount_id;
        }

        $queryString = (new BuildGraphQl('codeDiscountNode'))->with($with)->limits($limits)->build();

        $queryString = '
            query getCodeDiscount($id: ID!) {
                codeDiscountNode (id: $id){
                    '.$queryString.'
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $discount_id
        ], 'codeDiscountNode',  $sh_client);

        return $response;
    }

    public static function getCodeDiscounts($store, $filters, $cursor = null, $recordsInPage = 100, $sh_client = null, $with = [], $limits = [])
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $query = '';
        if (!empty($filters)) {
            $text_filters = [];
            $query = 'query: "###FILTERS###", ';
            if (isset($filters['code'])) {
                $text_filters[] = 'code:\''.$filters['code'].'\'';
            }
            if (isset($filters['method'])) {
                $text_filters[] = 'method:\''.$filters['method'].'\'';
            }

            $query = str_replace('###FILTERS###', implode(' AND ', $text_filters), $query);
        }

        $queryString = (new BuildGraphQl('codeDiscountNode'))->with($with)->limits($limits)->build();

        $queryString = '
         query ($recordsInPage: Int!, $cursor: String){
                codeDiscountNodes ('.$query.'first: $recordsInPage, after: $cursor){
                    nodes {
                        '.$queryString.'
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
        ], 'codeDiscountNodes', $sh_client);

        return $response;
    }

    public static function createCodeBasicDiscount($store, $discount, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            mutation CreateSegmentDiscountCode($basicCodeDiscount: DiscountCodeBasicInput!) {
                discountCodeBasicCreate(basicCodeDiscount: $basicCodeDiscount) {
                    codeDiscountNode {
                        id
                        codeDiscount {
                            ... on DiscountCodeBasic {
                                title
                                summary
                                codes(first: 10) {
                                    nodes {
                                        id
                                        code
                                    }
                                }
                                customerGets {
                                    value {
                                        ... on DiscountAmount {
                                            amount {
                                                amount
                                                currencyCode
                                            }
                                            appliesOnEachItem
                                        }
                                        ... on DiscountPercentage {
                                            percentage
                                        }
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
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'basicCodeDiscount' => $discount,
        ], 'discountCodeBasicCreate', $sh_client);

        return $response;
    }

    public static function updateCodeBasicDiscount($store, $discount_id, $discount, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($discount_id)) {
            $discount_id = 'gid://shopify/DiscountCodeNode/'.$discount_id;
        }

        $queryString = '
            mutation discountCodeBasicUpdate($id: ID!, $basicCodeDiscount: DiscountCodeBasicInput!) {
                discountCodeBasicUpdate(id: $id, basicCodeDiscount: $basicCodeDiscount) {
                    codeDiscountNode {
                        id
                        codeDiscount {
                            ... on DiscountCodeBasic {
                                title
                                summary
                                codes(first: 10) {
                                    nodes {
                                        id
                                        code
                                    }
                                }
                                customerGets {
                                    value {
                                        ... on DiscountAmount {
                                            amount {
                                                amount
                                                currencyCode
                                            }
                                            appliesOnEachItem
                                        }
                                        ... on DiscountPercentage {
                                            percentage
                                        }
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
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $discount_id,
            'basicCodeDiscount' => $discount,
        ], 'discountCodeBasicUpdate', $sh_client);

        return $response;
    }

    public static function activateCodeDiscount($store, $discount_id, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($discount_id)) {
            $discount_id = 'gid://shopify/DiscountCodeNode/'.$discount_id;
        }

        $queryString = '
            mutation discountCodeActivate($id: ID!) {
                discountCodeActivate(id: $id) {
                    codeDiscountNode {
                        id
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $discount_id,
        ], 'discountCodeActivate', $sh_client);

        return $response;
    }

    public static function desactivateCodeDiscount($store, $discount_id, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($discount_id)) {
            $discount_id = 'gid://shopify/DiscountCodeNode/'.$discount_id;
        }

        $queryString = '
            mutation discountCodeDeactivate($id: ID!) {
                discountCodeDeactivate(id: $id) {
                    codeDiscountNode {
                        id
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $discount_id,
        ], 'discountCodeDeactivate', $sh_client);

        return $response;
    }

    public static function deleteCodeDiscount($store, $discount_id, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($discount_id)) {
            $discount_id = 'gid://shopify/DiscountCodeNode/'.$discount_id;
        }

        $queryString = '
            mutation discountCodeDelete($id: ID!) {
                discountCodeDelete(id: $id) {
                    deletedCodeDiscountId
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $discount_id,
        ], 'discountCodeDelete', $sh_client);

        return $response;
    }
}
