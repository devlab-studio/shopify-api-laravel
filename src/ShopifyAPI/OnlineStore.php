<?php

namespace App\ShopifyAPI;

use Devlab\ShopifyApiLaravel\ShopifyAPI\BuildGraphQl;
use Devlab\ShopifyApiLaravel\ShopifyAPI\Core;

class OnlineStore
{

    public static function createCustomerCredit($store, $customer_id, $credit, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($customer_id)) {
            $customer_id = 'gid://shopify/Customer/'.$customer_id;
        }

        $queryString = '
            mutation storeCreditAccountCredit($id: ID!, $creditInput: StoreCreditAccountCreditInput!) {
                storeCreditAccountCredit(id: $id, creditInput: $creditInput) {
                    storeCreditAccountTransaction {
                        id
                        amount {
                            amount
                            currencyCode
                        }
                        account {
                            id
                            balance {
                                amount
                                currencyCode
                            }
                        }
                    }
                    userErrors {
                        code
                        message
                        field
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $customer_id,
            'creditInput' => $credit,
        ], 'storeCreditAccountCredit', $sh_client);

        return $response;
    }
    public static function createCustomerDebit($store, $customer_id, $debit, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($customer_id)) {
            $customer_id = 'gid://shopify/Customer/'.$customer_id;
        }

        $queryString = '
            mutation storeCreditAccountDebit($id: ID!, $debitInput: StoreCreditAccountDebitInput!) {
                storeCreditAccountDebit(id: $id, debitInput: $debitInput) {
                    storeCreditAccountTransaction {
                        id
                        amount {
                            amount
                            currencyCode
                        }
                        account {
                            id
                            balance {
                                amount
                                currencyCode
                            }
                        }
                    }
                    userErrors {
                        code
                        message
                        field
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $customer_id,
            'debitInput' => $debit,
        ], 'storeCreditAccountDebit', $sh_client);

        return $response;
    }

}
