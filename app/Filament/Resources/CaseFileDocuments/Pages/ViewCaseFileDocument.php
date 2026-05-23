<?php

namespace App\Filament\Resources\CaseFileDocuments\Pages;

use App\Filament\Resources\CaseFileDocuments\CaseFileDocumentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCaseFileDocument extends ViewRecord
{
    protected static string $resource = CaseFileDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
