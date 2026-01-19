<?php

namespace Devlab\ShopifyApiLaravel\ShopifyAPI;

interface SecretProvider
{
    public function getSecret(string $domain): string;
    public function getSecret2(string $domain): string;
}
