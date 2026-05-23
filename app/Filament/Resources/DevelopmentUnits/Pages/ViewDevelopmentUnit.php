<?php

namespace App\Filament\Resources\DevelopmentUnits\Pages;

use App\Filament\Resources\DevelopmentUnits\DevelopmentUnitResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDevelopmentUnit extends ViewRecord
{
    protected static string $resource = DevelopmentUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
