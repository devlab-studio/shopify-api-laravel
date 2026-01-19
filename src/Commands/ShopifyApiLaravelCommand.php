<?php

namespace Devlab\ShopifyApiLaravel\Commands;

use Illuminate\Console\Command;

class ShopifyApiLaravelCommand extends Command
{
    public $signature = 'shopify-api-laravel';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
