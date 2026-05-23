<?php

namespace App\Filament\Resources\DevelopmentUnits\Pages;

use App\Filament\Resources\DevelopmentUnits\DevelopmentUnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDevelopmentUnits extends ListRecords
{
    protected static string $resource = DevelopmentUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
