<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearPublicInventoryCache extends Command
{
    protected $signature = 'crm:clear-public-inventory-cache';

    protected $description = 'Clear public inventory API cache';

    public function handle(): int
    {
        Cache::forget('public_inventory_manifest');
        Cache::forget('public_inventory_sitemap');
        Cache::forget('public_inventory_ai_context_es');
        Cache::forget('public_inventory_ai_context_en');

        $this->info('Public inventory cache cleared.');

        return self::SUCCESS;
    }
}
