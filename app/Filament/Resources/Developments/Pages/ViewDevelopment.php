<?php

namespace App\Filament\Resources\Developments\Pages;

use App\Filament\Resources\Developments\DevelopmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDevelopment extends ViewRecord
{
    protected static string $resource = DevelopmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
