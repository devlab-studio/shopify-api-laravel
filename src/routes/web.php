<?php

use Illuminate\Support\Facades\Route;
use Devlab\ShopifyApiLaravel\Http\Controllers\SooController;
use Devlab\ShopifyApiLaravel\Http\Controllers\ShopifyWebhookController;

Route::get('/auth/shopify/{store_id}/login', [SooController::class, 'login'])->name('soo.login');
Route::get('/auth/shopify/code', [SooController::class, 'getCode'])->name('soo.get-code');
Route::get('/auth/shopify/{store_id}/app-login', [SooController::class, 'appLogin'])->name('soo.app-login');
Route::get('/auth/shopify/app-code', [SooController::class, 'getAppCode'])->name('soo.get-app-code');
Route::post('/shopify/webhooks', [ShopifyWebhookController::class, 'process'])->name('shopify.webhooks');
