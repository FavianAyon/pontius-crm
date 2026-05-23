<?php

namespace App\Filament\Resources\CaseFiles\Pages;

use App\Filament\Resources\CaseFiles\CaseFileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCaseFile extends ViewRecord
{
    protected static string $resource = CaseFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
