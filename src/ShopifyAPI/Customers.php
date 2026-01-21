<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

class Customers
{

    public static function getCustomer($store, $customer_id, $sh_client = null , $with = [], $limits = [])
    {
        $queryString = BuildGraphQl::build('customer', $with, $limits);
        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'customer_id' => $customer_id
        ], 'customer',  $sh_client);

        return $response;
    }

    public static function getCustomers($store, $filters, $cursor = null, $recordsInPage = 100, $sh_client = null, $with = [], $limits = [])
    {

        $query = '';
        if (!empty($filters)) {
            if (isset($filters['tag_not'])) {
                $query .= 'query: "tag_not:\''.$filters['tag_not'].'\'", ';
            }
            if (isset($filters['created_at'])) {
                $query .= 'query: "created_at:>=\''.$filters['created_at'].'\'", ';
            }
            if (isset($filters['email'])) {
                $query .= 'query: "email:'.$filters['email'].'", ';
            }
            if (isset($filters['state'])) {
                $query .= 'query: "state:'.$filters['state'].'", ';
            }
        }

        $queryString = BuildGraphQl::build('customers', $with, $limits);    
        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'recordsInPage' => $recordsInPage,
            'cursor' => $cursor,
        ], 'customers', $sh_client);

        return $response;
    }
    public static function createCustomer($store, $customer, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            mutation customerCreate($input: CustomerInput!) {
                customerCreate(input: $input) {
                    customer {
                        id
                        firstName
                        lastName
                        email
                        phone
                        locale
                        taxExempt
                        tags
                        emailMarketingConsent {
                            marketingState
                            marketingOptInLevel
                            consentUpdatedAt
                        }
                        addresses {
                            address1
                            province
                            city
                            countryCode
                            country
                            zip
                            phone
                        }
                        metafields (first: 15) {
                            nodes {
                                id
                                key
                                value
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
            'input' => $customer,
        ], 'customerCreate', $sh_client);

        return $response;
    }
    public static function updateCustomer($store, $customer, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            mutation customerUpdate($input: CustomerInput!) {
                customerUpdate(input: $input) {
                    customer {
                        id
                        firstName
                        lastName
                        email
                        phone
                        locale
                        taxExempt
                        tags
                        emailMarketingConsent {
                            marketingState
                            marketingOptInLevel
                            consentUpdatedAt
                        }
                        addresses {
                            address1
                            province
                            city
                            countryCode
                            zip
                            phone
                        }
                        metafields (first: 15) {
                            nodes {
                                id
                                key
                                value
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
            'input' => $customer,
        ], 'customerUpdate', $sh_client);

        return $response;
    }
    public static function mergeCustomer($store, $customerOneId, $customerTwoId, $overrideFields = null, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            mutation CustomerMerge($customerOneId: ID!, $customerTwoId: ID!, $overrideFields: CustomerMergeOverrideFields!) {
                customerMerge(customerOneId: $customerOneId, customerTwoId: $customerTwoId, overrideFields: $overrideFields) {
                    resultingCustomerId
                    job {
                        id
                        done
                    }
                    userErrors {
                        code
                        field
                        message
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'customerOneId' => $customerOneId,
            'customerTwoId' => $customerTwoId,
            'overrideFields' => $overrideFields,
        ], 'customerUpdate', $sh_client);

        return $response;
    }
    public static function createCustomerToken($store, $token_data, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getStoreFrontClient($store);
        }

        $queryString = '
            mutation customerAccessTokenCreate($input: CustomerAccessTokenCreateInput!) {
                customerAccessTokenCreate(input: $input) {
                    customerAccessToken {
                        accessToken
                        expiresAt
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'input' => $token_data,
        ], 'customerAccessTokenCreate', $sh_client);

        return $response;
    }
    
    public static function createCustomerStoreFront($store, $customer, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getStoreFrontClient($store);
        }

        $queryString = '
            mutation customerCreate($input: CustomerCreateInput!) {
                customerCreate(input: $input) {
                    customer {
                        id
                        firstName
                        lastName
                        email
                        phone
                    }
                    customerUserErrors {
                        code
                        field
                        message
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'input' => $customer,
        ], 'customerCreate', $sh_client);

        return $response;
    }

    public static function createCustomerTokenWithMultipass($store, $multipass_token, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getStoreFrontClient($store);
        }

        $queryString = '
            mutation customerAccessTokenCreateWithMultipass($multipassToken: String!) {
                customerAccessTokenCreateWithMultipass(multipassToken: $multipassToken) {
                    customerAccessToken {
                        accessToken
                        expiresAt
                    }
                    customerUserErrors {
                        code
                        field
                        message
                    }
                }
            }
        ';


        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'multipassToken' => $multipass_token,
        ], 'customerAccessTokenCreateWithMultipass', $sh_client);

        return $response;
    }
}
