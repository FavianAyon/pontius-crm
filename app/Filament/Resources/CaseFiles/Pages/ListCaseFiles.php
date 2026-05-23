<?php

namespace App\Filament\Resources\CaseFiles\Pages;

use App\Filament\Resources\CaseFiles\CaseFileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCaseFiles extends ListRecords
{
    protected static string $resource = CaseFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
