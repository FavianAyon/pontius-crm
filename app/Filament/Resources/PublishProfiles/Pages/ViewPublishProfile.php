<?php

namespace App\Filament\Resources\PublishProfiles\Pages;

use App\Filament\Resources\PublishProfiles\PublishProfileResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPublishProfile extends ViewRecord
{
    protected static string $resource = PublishProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
