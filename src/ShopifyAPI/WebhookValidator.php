<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Devlab\ShopifyApiLaravel\Exceptions\WebhookFailed;
use Devlab\ShopifyApiLaravel\ShopifyAPI\VerifiesWebhooks;

class WebhookValidator
{
    use VerifiesWebhooks;

    private SecretProvider $secretProvider;

    public function __construct(SecretProvider $secretProvider)
    {
        $this->secretProvider = $secretProvider;
    }

    public function validate(string $signature, string $domain, string $data): void
    {
        // Validate webhook secret presence
        $secret = $this->secretProvider->getSecret($domain);
        $secret2 = $this->secretProvider->getSecret2($domain);
        throw_if(empty($secret), WebhookFailed::missingSigningSecret());

        // Validate webhook signature
        throw_unless(
            $this->isWebhookSignatureValid($signature, $data, $secret) || $this->isWebhookSignatureValid($signature, $data, $secret2),
            WebhookFailed::invalidSignature($signature)
        );
    }

    public function validateFromRequest(Request $request): void
    {
        // Validate signature presence
        $signature = $request->shopifyHmacSignature();
        throw_unless($signature, WebhookFailed::missingSignature());

        // Validate topic presence
        throw_unless($request->shopifyTopic(), WebhookFailed::missingTopic());

        $this->validate($signature, $request->shopifyShopDomain(), $request->getContent());
    }
}
