<?php

namespace App\ShopifyAPI;

use Devlab\ShopifyApiLaravel\ShopifyAPI\BuildGraphQl;
use Devlab\ShopifyApiLaravel\ShopifyAPI\Core;

class Companies
{
    public static function getCompanies($store, $filters, $cursor = null, $recordsInPage = 100, $sh_client = null, $with = [], $limits = [])
    {

        $query = '';
        if (!empty($filters)) {
            $text_filters = [];
            $query = 'query: "###FILTERS###", ';
            if (isset($filters['tag'])) {
                $text_filters[] = 'tag:\''.$filters['tag'].'\'';
            }
            if (isset($filters['tag_not'])) {
                $text_filters[] = 'tag_not:\''.$filters['tag_not'].'\'';
            }
            if (isset($filters['created_at'])) {
                $text_filters[] = 'created_at:>=\''.$filters['created_at'].'\'';
            }
            if (isset($filters['email'])) {
                $text_filters[] = 'email:\''.$filters['email'].'\'';
            }
            if (isset($filters['state'])) {
                $text_filters[] ='state:'.$filters['state'];
            }

            $query = str_replace('###FILTERS###', implode(' AND ', $text_filters), $query);
        }

        $queryString = (new BuildGraphQl('company'))->with($with)->limits($limits)->build();

        $queryString = '
         query ($recordsInPage: Int!, $cursor: String){
                companies ('.$query.'first: $recordsInPage, after: $cursor){
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
        ], 'companies', $sh_client);

        return $response;
    }
}
