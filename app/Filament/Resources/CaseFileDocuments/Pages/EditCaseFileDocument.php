<?php

namespace App\Filament\Resources\CaseFileDocuments\Pages;

use App\Filament\Resources\CaseFileDocuments\CaseFileDocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCaseFileDocument extends EditRecord
{
    protected static string $resource = CaseFileDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
