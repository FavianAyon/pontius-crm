<?php

namespace App\Filament\Resources\CaseFiles\Pages;

use App\Filament\Resources\CaseFiles\CaseFileResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCaseFile extends EditRecord
{
    protected static string $resource = CaseFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
