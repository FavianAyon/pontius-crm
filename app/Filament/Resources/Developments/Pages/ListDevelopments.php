<?php

namespace App\Filament\Resources\Developments\Pages;

use App\Filament\Resources\Developments\DevelopmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDevelopments extends ListRecords
{
    protected static string $resource = DevelopmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
