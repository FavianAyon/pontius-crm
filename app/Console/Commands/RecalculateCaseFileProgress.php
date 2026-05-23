<?php

namespace App\Console\Commands;

use App\Models\CaseFile;
use Illuminate\Console\Command;

class RecalculateCaseFileProgress extends Command
{
    protected $signature = 'crm:recalculate-case-file-progress';

    protected $description = 'Recalculate document progress for all case files';

    public function handle(): int
    {
        CaseFile::query()->each(function (CaseFile $caseFile) {
            $caseFile->recalculateDocumentProgress();
        });

        $this->info('Case file progress recalculated successfully.');

        return self::SUCCESS;
    }
}
