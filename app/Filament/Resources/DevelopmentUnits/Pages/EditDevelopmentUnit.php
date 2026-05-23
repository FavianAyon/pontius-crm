<?php

namespace App\Filament\Resources\DevelopmentUnits\Pages;

use App\Filament\Resources\DevelopmentUnits\DevelopmentUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDevelopmentUnit extends EditRecord
{
    protected static string $resource = DevelopmentUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
