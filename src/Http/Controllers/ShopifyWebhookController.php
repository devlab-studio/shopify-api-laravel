<?php

namespace Devlab\ShopifyApiLaravel\Http\Controllers;

use Devlab\ShopifyApiLaravel\ShopifyAPI\WebhookSecretProvider;
use Devlab\LaravelLogs\Models\ModelsLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Devlab\ShopifyApiLaravel\ShopifyAPI\WebhookValidator;
use Devlab\ShopifyApiLaravel\ShopifyAPI\Webhook;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class ShopifyWebhookController extends Controller
{
    public function process(Request $request)
    {
        $shopifyHmacSignature = $request->header(Webhook::HEADER_HMAC_SIGNATURE);
        $shopifyShopDomain = $request->header(Webhook::HEADER_SHOP_DOMAIN);
        $shopifyTopic = $request->header(Webhook::HEADER_TOPIC);
        $eventName = 'shopify-webhooks.'.str_replace('/', '-', $shopifyTopic);
        $content = $request->getContent();
        $webhook = new Webhook($shopifyShopDomain, $shopifyTopic, json_decode($content, true));

        // ModelsLog::doLog(dl_get_procedure($this, __FUNCTION__), json_decode($content, true));

        $secretProvider = new WebhookSecretProvider();
        $validator = new WebhookValidator($secretProvider);
        $validator->validate($shopifyHmacSignature, $shopifyShopDomain, $content);

        // Process the webhook
        Log::info('ShopifyWebhookController: Dispatching event: '.$eventName);
        Event::dispatch($eventName, $webhook);

        return new JsonResponse();
    }
}
