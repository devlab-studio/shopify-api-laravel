<br></br>
<p align="center">
  <a href="https://dev-lab.es">
    <img src="https://dev-lab.es/assets/logos/main-light.svg" alt="Devlab Logo" width="400"/>
  </a>
</p>

<p align="right">
  <a href="README.es.md"><img src="https://img.shields.io/badge/Espa%C3%B1ol-Ver%20en%20ES-blue.svg?style=flat-square" alt="Español"/></a>
</p>



**Shopify API Laravel** is a package to easily integrate the Shopify API into your Laravel projects. It provides methods to fetch and manage Shopify products, orders, customers and stores using PHP, simplifying the connection to Shopify's official API from Laravel.

- Fetch and manage Shopify products
- Fetch and manage Shopify orders
- Fetch and manage Shopify customers
- Fetch and manage Shopify inventory
- Webhook workflow for Shopify
- Methods to retrieve stores from the database
- Utilities to work with GraphQL and the official API


## Installation

Install the package via Composer:

```bash
composer require devlab-studio/shopify-api-laravel
```

Publish and run the required migrations:

```bash
php artisan vendor:publish --tag=shopify-api-laravel-migrations
php artisan migrate
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=shopify-api-laravel-config
```


## Basic usage examples

### Get stores

```php
use Devlab\ShopifyApiLaravel\Models\Store;

$stores = Store::dlGet();
```

### Fetch products for a store

```php
use Devlab\ShopifyApiLaravel\ShopifyAPI\Products;

$store = Store::dlGet(1);
$products = Products::getProducts($store, []);
```

### Fetch an order

```php
use Devlab\ShopifyApiLaravel\ShopifyAPI\Orders;

$store = Store::dlGet(1);
$order = Orders::getOrder($store, $orderId);
```


Refer to the [Shopify/GraphQL documentation](https://shopify.dev/docs/api/admin-graphql/latest) for more examples and details.


## Resources

- [Support and contact](https://dev-lab.es/contact)

---

<div align="center">
  © 2026 <a href="https://dev-lab.es">Devlab Studio</a>
</div>
