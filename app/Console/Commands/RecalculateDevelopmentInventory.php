<?php

namespace App\Console\Commands;

use App\Models\Development;
use Illuminate\Console\Command;

class RecalculateDevelopmentInventory extends Command
{
    protected $signature = 'developments:recalculate-inventory';

    protected $description = 'Recalculate total and available units for all developments';

    public function handle(): int
    {
        Development::query()->each(function (Development $development) {
            $development->recalculateInventory();
        });

        $this->info('Development inventory recalculated successfully.');

        return self::SUCCESS;
    }
}
