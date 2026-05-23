<?php

namespace App\Filament\Resources\CaseFileDocuments\Pages;

use App\Filament\Resources\CaseFileDocuments\CaseFileDocumentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCaseFileDocuments extends ListRecords
{
    protected static string $resource = CaseFileDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
