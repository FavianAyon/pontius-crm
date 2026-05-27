<?php

namespace App\Filament\Resources\PublishProfiles\Pages;

use App\Filament\Resources\PublishProfiles\PublishProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPublishProfiles extends ListRecords
{
    protected static string $resource = PublishProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
