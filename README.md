<br></br>
<p align="center">
  <a href="https://dev-lab.es">
    <img src="https://dev-lab.es/assets/logos/main-light.svg" alt="Devlab Logo" width="400"/>
  </a>
</p>
<br></br>




**Shopify API Laravel** es un paquete para integrar de forma sencilla la API de Shopify en tus proyectos Laravel. Proporciona métodos para consultar y gestionar productos, pedidos y tiendas Shopify usando PHP, facilitando la conexión con la API oficial de Shopify desde Laravel.

- Consulta y gestión de productos Shopify
- Consulta y gestión de pedidos Shopify
- Consulta y gestión de clientes Shopify
- Consulta y gestión de inventario Shopify
- Flujo de trabajo para webhooks Shopify
- Métodos para obtener tiendas desde la base de datos
- Utilidades para trabajar con GraphQL y la API oficial


## Instalación

Instala el paquete vía composer:

```bash
composer require devlab-studio/shopify-api-laravel
```

Publica y ejecuta las migraciones necesarias:

```bash
php artisan vendor:publish --tag=shopify-api-laravel-migrations
php artisan migrate
```

Publica el archivo de configuración:

```bash
php artisan vendor:publish --tag=shopify-api-laravel-config
```




## Ejemplo de uso básico

### Obtener tiendas

```php
use Devlab\ShopifyApiLaravel\Models\Store;

$stores = Store::dlGet();
```

### Consultar productos de una tienda

```php
use Devlab\ShopifyApiLaravel\ShopifyAPI\Products;

$store = Store::dlGet(1); 
$products = Products::getProducts($store, []); 
```

### Consultar un pedido

```php
use Devlab\ShopifyApiLaravel\ShopifyAPI\Orders;

$store = Store::dlGet(1);
$order = Orders::getOrder($store, $orderId);
```



## Ejemplo de uso básico



Consulta la [documentación de Shopify/GraphQL](https://shopify.dev/docs/api/admin-graphql/latest) para más ejemplos y detalles de uso.



## Recursos


- [Soporte y contacto](https://dev-lab.es/contact)

---

<div align="center">
  © 2026 <a href="https://dev-lab.es">Devlab Studio</a>
</div>
