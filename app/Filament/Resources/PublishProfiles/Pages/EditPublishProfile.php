<?php

namespace App\Filament\Resources\PublishProfiles\Pages;

use App\Filament\Resources\PublishProfiles\PublishProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPublishProfile extends EditRecord
{
    protected static string $resource = PublishProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
