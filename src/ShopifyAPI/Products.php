<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

class Products
{

    public static function getProduct($store, $product_id, $sh_client = null, $with = [], $limits = [])
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($product_id)) {
            $product_id = 'gid://shopify/Product/'.$product_id;
        }

        $queryString = (new BuildGraphQl('product'))->with($with)->limits($limits)->build();

        $queryString = '
            query getProduct($id: ID!) {
                product (id: $id){
                    '.$queryString.'
                }
            }
        ';
        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $product_id
        ], 'product', $sh_client);

        return $response;
    }
    public static function getProducts($store, $filters, $sh_client = null, $cursor = null, $recordsInPage = 100, $with = [], $limits = [])
    {

        $query = '';
        if (!empty($filters)) {
            $text_filters = [];
            $query = 'query: "###FILTERS###", ';
            if (isset($filters['tag_not'])) {
                $text_filters[] = 'tag_not:\''.$filters['tag_not'].'\'';
            }
            if (isset($filters['sku'])) {
                $text_filters[] = 'sku:\''.$filters['sku'].'\' AND status:ACTIVE,DRAFT", ';
            }
            if (isset($filters['handle'])) {
                $text_filters[] = 'handle:'.$filters['handle'].' AND status:ACTIVE,DRAFT", ';
            }
            if (isset($filters['created_at'])) {
                $text_filters[] = 'created_at:>=\''.$filters['created_at'].'\'", ';
            }
            if (isset($filters['id'])) {
                $text_filters[] = '"id:>='.$filters['id'].'", ';
            }
            if (isset($filters['status'])) {
                $text_filters[] = '"status:'.$filters['status'].'", ';
            }
            $query = str_replace('###FILTERS###', implode(' AND ', $text_filters), $query);
        }
        $queryString = (new BuildGraphQl('product'))->with($with)->limits($limits)->build();

        $queryString = '
            query ($recordsInPage: Int!, $cursor: String){
                products ('.$query.'first: $recordsInPage, after: $cursor){
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
            'cursor' => $cursor
        ], 'products', $sh_client);

        return $response;
    }
    public static function getProductVariant($store, $variant_id, $sh_client = null, $with = [], $limits = [])
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        if (is_numeric($variant_id)) {
            $variant_id = 'gid://shopify/ProductVariant/'.$variant_id;
        }

        $queryString = (new BuildGraphQl('productVariants'))->with($with)->limits($limits)->build();

        return Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $variant_id
        ], 'productVariant', $sh_client);

        return $response;
    }
    public static function getProductVariants($store, $filters, $sh_client = null, $cursor = null, $recordsInPage = 100, $with = [], $limits = [])
    {

         $query = '';
        if (!empty($filters)) {
            $text_filters = [];
            $query = 'query: "###FILTERS###", ';
            if (isset($filters['created_at'])) {
                $text_filters[] = 'created_at:>=\''.$filters['created_at'].'\'';
            }
            if (isset($filters['id'])) {
                $text_filters[] = 'id:>='.$filters['id'].'", ';
            }
            if (isset($filters['sku'])) {
                $text_filters[] = 'sku:\''.$filters['sku'].'\', ';
            }

            $query = str_replace('###FILTERS###', implode(' AND ', $text_filters), $query);
        }

        $queryString = (new BuildGraphQl('productVariants'))->with($with)->limits($limits)->build();

        $queryString = '
            query ($recordsInPage: Int!, $cursor: String){
                productVariants ('.$query.'first: $recordsInPage, after: $cursor){
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
        ], 'productVariants', $sh_client);

        return $response;

    }
    public static function createProduct($store, $product, $media, $sh_client = null)
    {

        $query_reponse = '
            product {
                id
                options {
                    id
                    name
                    position
                    values
                    optionValues {
                        id
                        name
                        hasVariants
                    }
                }
                metafields(first: 5) {
                    edges {
                        node {
                            id
                            namespace
                            key
                            value
                        }
                    }
                }
                variants(first: 15) {
                    nodes {
                        id
                        title
                        price
                        selectedOptions {
                            name
                            value
                        }
                    }
                }
            }
            userErrors {
                field
                message
            }
        ';
        if ($media) {
            $queryString = '
                mutation productCreate($product: ProductCreateInput!, $media: [CreateMediaInput!]) {
                    productCreate(product: $product, media: $media) {
                        '.$query_reponse.'
                    }
                }
            ';
        } else {
            $queryString = '
                mutation productCreate($product: ProductInput!) {
                    productCreate(product: $product) {
                        '.$query_reponse.'
                    }
                }
            ';
        }
        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'product' => $product,
            'media' => $media
        ], 'productCreate', $sh_client);

        return $response;
    }
    public static function updateProduct($store, $product, $sh_client = null)
    {
        $queryString = '
            mutation productCreate($input: ProductInput!) {
                productUpdate(input: $input) {
                    product {
                        id
                        options {
                            id
                            name
                            position
                            values
                            optionValues {
                                id
                                name
                                hasVariants
                            }
                        }
                        metafields(first: 5) {
                            edges {
                                node {
                                    id
                                    namespace
                                    key
                                    value
                                }
                            }
                        }
                        variants(first: 15) {
                            nodes {
                                id
                                title
                                price
                                selectedOptions {
                                    name
                                    value
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
            'input' => $product
        ], 'productUpdate', $sh_client);

        return $response;
    }
    public static function setProduct($store, $product, $sh_client = null)
    {

        $queryString = '
            mutation productSet($input: ProductSetInput!, $synchronous: Boolean!) {
                productSet(synchronous: $synchronous, input: $input) {
                    product {
                        id
                        title
                        description
                        handle
                        options (first: 30) {
                            id
                            name
                            position
                            values
                            optionValues {
                                id
                                name
                                hasVariants
                            }
                        }
                        metafields(first: 30) {
                            edges {
                                node {
                                    id
                                    namespace
                                    key
                                    value
                                }
                            }
                        }
                        media(first: 20) {
                            nodes {
                                id
                            }
                        }
                        variants(first: 30) {
                            nodes {
                                id
                                title
                                price
                                selectedOptions {
                                    name
                                    value
                                    optionValue {
                                        id
                                        name
                                    }
                                }
                                inventoryItem {
                                    id
                                    inventoryLevels (first: 5) {
                                        nodes {
                                            id
                                            quantities(names: ["available"]) {
                                                name
                                                quantity
                                            }
                                        }
                                    }
                                    tracked
                                }
                                metafields (first: 30) {
                                    nodes {
                                        id
                                        key
                                        value
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
            'input' => $product,
            'synchronous' => true
        ], 'productSet', $sh_client);

        return $response;
    }
    public static function createProductVariants($store, $product_id, $variants, $sh_client = null)
    {

        $queryString = '
            mutation productVariantsBulkCreate($productId: ID!, $variants: [ProductVariantsBulkInput!]!) {
                productVariantsBulkCreate(productId: $productId, variants: $variants) {
                    product {
                        id
                        options {
                            id
                            name
                            values
                            position
                            optionValues {
                                id
                                name
                                hasVariants
                            }
                        }
                    }
                    productVariants {
                        id
                        title
                        price
                        sku
                        selectedOptions {
                            name
                            value
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
            'productId' => $product_id,
            'variants' => $variants
        ], 'productVariantsBulkCreate', $sh_client);

        return $response;
    }
    public static function updateProductVariants($store, $product_id, $variants, $sh_client = null)
    {

        $queryString = '
            mutation productVariantsBulkUpdate($productId: ID!, $variants: [ProductVariantsBulkInput!]!) {
                productVariantsBulkUpdate(productId: $productId, variants: $variants) {
                    product {
                        id
                        options {
                            id
                            name
                            values
                            position
                            optionValues {
                                id
                                name
                                hasVariants
                            }
                        }
                    }
                    productVariants {
                        id
                        title
                        price
                        sku
                        selectedOptions {
                            name
                            value
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
            'productId' => $product_id,
            'variants' => $variants
        ], 'productVariantsBulkUpdate', $sh_client);

        return $response;
    }
    public static function activateInventory($store, $inventoryItemId, $inventory_data, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            mutation inventoryItemUpdate($id: ID!, $input: InventoryItemInput!) {
                inventoryItemUpdate(id: $id, input: $input) {
                    inventoryItem {
                        id
                        tracked
                    }
                }
            }
        ';
        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $inventoryItemId,
            'input' => $inventory_data
        ], 'inventoryItemUpdate', $sh_client);

        return $response;
    }
    public static function getInventory($store, $inventory_id, $sh_client = null)
    {
        $queryString = '
            query ($id: ID!){
                inventoryLevel(id: $id) {
                    id
                    quantities(names: ["available", "incoming", "committed", "damaged", "on_hand", "quality_control", "reserved", "safety_stock"]) {
                        name
                        quantity
                    }
                    item {
                        id
                        sku
                    }
                    location {
                        id
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $queryString, [
            'id' => $inventory_id
        ], 'inventoryLevel', $sh_client);

        return $response;
    }
    public static function updateInventory($store, $inventory, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            mutation inventorySetQuantities($input: InventorySetQuantitiesInput!) {
                inventorySetQuantities(input: $input) {
                    inventoryAdjustmentGroup {
                        createdAt
                        reason
                        referenceDocumentUri
                        changes {
                            name
                            delta
                        }
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';
        $response = Core::executeQueryAndHandleErrors(
            $store,
            $queryString,
            ['input' => $inventory],
            'inventorySetQuantities',
            $sh_client
        );

        return $response;
    }

    public static function updateHS($store, $inventoryItemId, $hsCode, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $queryString = '
            mutation inventoryItemUpdate($input: InventoryItemUpdateInput!) {
                inventoryItemUpdate(input: $input) {
                    inventoryItem {
                        id
                        harmonizedSystemCode
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';

        $variables = [
            'input' => [
                'id' => $inventoryItemId,
                'harmonizedSystemCode' => $hsCode,
            ]
        ];

        $response = Core::executeQueryAndHandleErrors(
            $store,
            $queryString,
            $variables,
            'inventoryItemUpdate',
            $sh_client
        );

        return $response;
    }

    public static function getProductByHandle($store, $handle, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $query = '
            query getProductByHandle($handle: String!) {
                productByHandle(handle: $handle) {
                    id
                    title
                    handle
                    variants(first: 20) {
                        nodes {
                            id
                            title
                            sku
                            selectedOptions {
                                name
                                value
                            }
                            inventoryItem {
                                id
                                tracked
                            }
                        }
                    }
                }
            }
        ';

        return Core::executeQueryAndHandleErrors($store, $query, [
            'handle' => $handle,
        ], 'productByHandle', $sh_client);
    }

    public static function getOrCreateCollection($store, $handle, $collection_title, $collection_description, $collection_publication, $sh_client = null)
    {

        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $publication_ids = [];
        if (!empty($collection_publication)) {
            $publication_ids = self::getPublicationIdsByNames($store, $collection_publication, $sh_client);
        }
        $query = '
                query getCollectionByHandle($handle: String!) {
                    collectionByHandle(handle: $handle) {
                        id
                        title
                        handle
                    }
                }
            ';

        $variables = [
            'handle' => $handle,
        ];

        $response = Core::executeQueryAndHandleErrors($store, $query, $variables, 'collectionByHandle', $sh_client);

        if (!empty($response['id'])) {
            return $response;
        }

        $mutation = '
            mutation createCollection($input: CollectionInput!) {
                collectionCreate(input: $input) {
                    collection {
                        id
                        handle
                        title
                        descriptionHtml
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';

        $input = [
            'input' => [
                'title' => $collection_title ?? null,
                'handle' => $handle ?? null,
                'descriptionHtml' => $collection_description ?? null,
            ]
        ];


        $response = Core::executeQueryAndHandleErrors($store, $mutation, $input, 'collectionCreate', $sh_client);
        if (!empty($publication_ids)) {
           Self::publishCollection($store, $response['collection']['id'], $publication_ids, $sh_client);
        }
        return $response;
    }

    public static function collectionAddProducts($store, $collection_gid, $product_gid, $sh_client = null)
    {
    if (empty($sh_client)) {
        $sh_client = Core::getGraphQLClient($store);
    }

    $mutation = '
        mutation collectionAddProducts($id: ID!, $productIds: [ID!]!) {
            collectionAddProducts(id: $id, productIds: $productIds) {
                collection {
                    id
                }
                userErrors {
                    field
                    message
                }
            }
        }
    ';

    $variables = [
        'id' => $collection_gid,
        'productIds' => $product_gid
    ];

    $response = Core::executeQueryAndHandleErrors($store, $mutation, $variables, 'collectionAddProducts', $sh_client);


    return $response;
    }

    public static function getPublicationIdsByNames($store, array $collection_publication, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $query = '
            query {
                publications(first: 50) {
                    nodes {
                        id
                        name
                    }
                }
            }
        ';

        $response = Core::executeQueryAndHandleErrors($store, $query, [], 'publications', $sh_client);
        $publication_ids = [];
        foreach ($response['nodes'] as $publication) {
            if (in_array($publication['name'], $collection_publication)) {
                $publication_ids[] = $publication['id'];
            }
        }
        return $publication_ids;
    }

    public static function publishCollection($store, $collection_id, $publication_ids, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

           $mutation = '
                mutation collectionPublish($input: CollectionPublishInput!) {
                    collectionPublish(input: $input) {
                        userErrors {
                            field
                            message
                        }
                    }
                }
            ';
        foreach ($publication_ids as $pub_id) {
               $variables = [
                    'input' => [
                        'id' => $collection_id,
                        'collectionPublications' => array_map(function($pub_id) {
                            return ['publicationId' => $pub_id];
                        }, $publication_ids),
                    ]
                ];


            $response = Core::executeQueryAndHandleErrors($store, $mutation, $variables, 'publishablePublish', $sh_client);
        }
        // return $response;
    }

    public static function deleteProduct($store, $id, $sh_client = null)
    {
        $query = '
            mutation productDelete($input: ProductDeleteInput!) {
                productDelete(input: $input) {
                    deletedProductId
                    userErrors {
                        field
                        message
                    }
                }
            }
        ';
        $response = Core::executeQueryAndHandleErrors($store, $query, [
            'input' => [
                'id' => $id
            ]
        ], 'productDelete', $sh_client);

        return $response;
    }

    public static function createRedirect($store, array $urlRedirect, $sh_client = null)
    {
        if (empty($sh_client)) {
            $sh_client = Core::getGraphQLClient($store);
        }

        $query = 'mutation urlRedirectCreate($urlRedirect: UrlRedirectInput!) {
            urlRedirectCreate(urlRedirect: $urlRedirect) {
                urlRedirect {
                    id
                    path
                    target
                }
                userErrors {
                    field
                    message
                }
            }
        }';

        $variables = [
            'urlRedirect' => $urlRedirect
        ];

        return Core::executeQueryAndHandleErrors($store, $query, $variables, 'urlRedirectCreate', $sh_client);
    }

    public static function getProductVariantByInventoryItemId($store, string $inventoryItemId, $sh_client = null)
{
    if (empty($sh_client)) {
        $sh_client = Core::getGraphQLClient($store);
    }

    $queryString = '
        query ($id: ID!) {
            inventoryItem(id: $id) {
                id
                sku
                variant {
                    id
                    title
                    sku
                    price
                    product {
                        id
                        title
                        handle
                    }
                }
            }
        }
    ';

    $variables = ['id' => $inventoryItemId];

    return Core::executeQueryAndHandleErrors($store, $queryString, $variables, 'inventoryItem', $sh_client);
}

}
